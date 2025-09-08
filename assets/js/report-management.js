// Report Management JavaScript

let reports = [];
let pendingReports = [];
let tests = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadReports();
    loadPendingReports();
    loadTests();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('addResultsForm').addEventListener('submit', handleAddResults);
    document.getElementById('searchInput').addEventListener('input', debounce(filterReports, 300));
}

// Load reports
async function loadReports() {
    try {
        const response = await fetch('../api/reports.php');
        const data = await response.json();
        reports = data.reports || [];
        
        updateStats(data.stats || {});
        renderReportsTable();
        populateTestFilter();
    } catch (error) {
        console.error('Error loading reports:', error);
        showAlert('Error loading reports', 'danger');
    }
}

// Load pending reports for dropdown
async function loadPendingReports() {
    try {
        const response = await fetch('../api/reports.php?status=pending');
        const data = await response.json();
        pendingReports = data.reports || [];
        
        populateReportSelect();
    } catch (error) {
        console.error('Error loading pending reports:', error);
    }
}

// Load tests
async function loadTests() {
    try {
        const response = await fetch('../api/tests.php');
        tests = await response.json();
    } catch (error) {
        console.error('Error loading tests:', error);
    }
}

// Update statistics cards
function updateStats(stats) {
    document.getElementById('totalReports').textContent = stats.total || 0;
    document.getElementById('completedReports').textContent = stats.completed || 0;
    document.getElementById('pendingReports').textContent = stats.pending || 0;
    document.getElementById('abnormalResults').textContent = stats.abnormal || 0;
}

// Populate test filter
function populateTestFilter() {
    const testCodes = [...new Set(reports.map(r => r.test_code))];
    const select = document.getElementById('testFilter');
    
    select.innerHTML = '<option value="">All Tests</option>';
    testCodes.forEach(code => {
        const test = tests.find(t => t.code === code);
        const testName = test ? test.name : code;
        select.innerHTML += `<option value="${code}">${testName}</option>`;
    });
}

// Populate report select for adding results
function populateReportSelect() {
    const select = document.getElementById('reportSelect');
    
    select.innerHTML = '<option value="">Select a pending report</option>';
    pendingReports.forEach(report => {
        select.innerHTML += `
            <option value="${report.id}" data-test-code="${report.test_code}">
                Invoice #${String(report.invoice_id).padStart(4, '0')} - ${report.patient_name} - ${report.test_name}
            </option>
        `;
    });
}

// Load test parameters when report is selected
async function loadTestParameters() {
    const select = document.getElementById('reportSelect');
    const selectedOption = select.selectedOptions[0];
    const container = document.getElementById('parametersContainer');
    
    if (!selectedOption || !selectedOption.value) {
        container.innerHTML = '<p class="text-muted">Select a report to load test parameters</p>';
        return;
    }
    
    const testCode = selectedOption.dataset.testCode;
    
    try {
        const response = await fetch(`../api/tests.php?code=${testCode}`);
        const test = await response.json();
        
        if (test.parameters && test.parameters.length > 0) {
            container.innerHTML = test.parameters.map(param => `
                <div class="row mb-3 parameter-row">
                    <div class="col-md-3">
                        <label class="form-label">${param.parameter_name}</label>
                        <input type="hidden" name="param_name[]" value="${param.parameter_name}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Value *</label>
                        <input type="text" class="form-control" name="param_value[]" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit</label>
                        <input type="text" class="form-control" name="param_unit[]" value="${param.unit || ''}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Normal Range</label>
                        <input type="text" class="form-control" name="param_range[]" value="${param.normal_range || ''}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Abnormal?</label>
                        <select class="form-select" name="param_abnormal[]">
                            <option value="0">Normal</option>
                            <option value="1">Abnormal</option>
                        </select>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<div class="alert alert-info">No parameters defined for this test</div>';
        }
    } catch (error) {
        console.error('Error loading test parameters:', error);
        container.innerHTML = '<div class="alert alert-danger">Error loading test parameters</div>';
    }
}

// Render reports table
function renderReportsTable() {
    const tbody = document.getElementById('reportsTableBody');
    
    if (reports.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No reports found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = reports.map(report => `
        <tr>
            <td><strong>#${String(report.invoice_id).padStart(4, '0')}</strong></td>
            <td>
                <div><strong>${report.patient_name}</strong></div>
                <small class="text-muted">${report.patient_age}Y, ${report.patient_gender}</small>
            </td>
            <td>
                <div><strong>${report.test_name || report.test_code}</strong></div>
                <small class="text-muted">${report.test_code}</small>
            </td>
            <td>${formatDate(report.created_at)}</td>
            <td>${getStatusBadge(report.status)}</td>
            <td>${report.technician_name || 'Not assigned'}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewReport(${report.id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="printReport(${report.id})" title="Print">
                        <i class="fas fa-print"></i>
                    </button>
                    ${report.status === 'pending' ? `
                        <button class="btn btn-outline-warning" onclick="editResults(${report.id})" title="Add Results">
                            <i class="fas fa-edit"></i>
                        </button>
                    ` : ''}
                    ${report.status === 'completed' ? `
                        <button class="btn btn-outline-info" onclick="verifyReport(${report.id})" title="Verify">
                            <i class="fas fa-check"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

// Get status badge HTML
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'in_progress': '<span class="badge bg-info">In Progress</span>',
        'completed': '<span class="badge bg-success">Completed</span>',
        'verified': '<span class="badge bg-primary">Verified</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

// Handle add results form submission
async function handleAddResults(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const reportId = formData.get('report_id');
    const notes = formData.get('notes');
    
    // Collect parameter results
    const paramNames = formData.getAll('param_name[]');
    const paramValues = formData.getAll('param_value[]');
    const paramUnits = formData.getAll('param_unit[]');
    const paramRanges = formData.getAll('param_range[]');
    const paramAbnormal = formData.getAll('param_abnormal[]');
    
    const results = [];
    for (let i = 0; i < paramNames.length; i++) {
        if (paramNames[i] && paramValues[i]) {
            results.push({
                parameter_name: paramNames[i],
                value: paramValues[i],
                unit: paramUnits[i] || '',
                normal_range: paramRanges[i] || '',
                is_abnormal: parseInt(paramAbnormal[i]) || 0
            });
        }
    }
    
    const resultData = {
        report_id: parseInt(reportId),
        notes: notes,
        results: results
    };
    
    try {
        const response = await fetch('../api/test-results.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(resultData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Test results added successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addResultsModal')).hide();
            e.target.reset();
            document.getElementById('parametersContainer').innerHTML = '<p class="text-muted">Select a report to load test parameters</p>';
            loadReports();
            loadPendingReports();
        } else {
            showAlert(result.message || 'Error adding test results', 'danger');
        }
    } catch (error) {
        console.error('Error adding test results:', error);
        showAlert('Error adding test results', 'danger');
    }
}

// Filter reports
function filterReports() {
    const statusFilter = document.getElementById('statusFilter').value;
    const testFilter = document.getElementById('testFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    let filtered = [...reports];
    
    if (statusFilter) {
        filtered = filtered.filter(report => report.status === statusFilter);
    }
    
    if (testFilter) {
        filtered = filtered.filter(report => report.test_code === testFilter);
    }
    
    if (dateFrom) {
        filtered = filtered.filter(report => new Date(report.created_at) >= new Date(dateFrom));
    }
    
    if (dateTo) {
        filtered = filtered.filter(report => new Date(report.created_at) <= new Date(dateTo));
    }
    
    if (searchTerm) {
        filtered = filtered.filter(report => 
            report.patient_name.toLowerCase().includes(searchTerm) ||
            String(report.invoice_id).includes(searchTerm)
        );
    }
    
    // Temporarily replace reports array for rendering
    const originalReports = reports;
    reports = filtered;
    renderReportsTable();
    reports = originalReports;
}

// Clear filters
function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('testFilter').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    document.getElementById('searchInput').value = '';
    renderReportsTable();
}

// Export reports
function exportReports() {
    const csv = generateReportsCSV();
    downloadCSV(csv, 'test-reports.csv');
}

// Generate CSV data for reports
function generateReportsCSV() {
    const headers = ['Invoice#', 'Patient Name', 'Age', 'Gender', 'Test Code', 'Test Name', 'Date', 'Status', 'Technician', 'Notes'];
    const rows = reports.map(report => [
        `#${String(report.invoice_id).padStart(4, '0')}`,
        report.patient_name,
        report.patient_age,
        report.patient_gender,
        report.test_code,
        report.test_name || report.test_code,
        formatDate(report.created_at),
        report.status,
        report.technician_name || 'Not assigned',
        report.notes || ''
    ]);
    
    return [headers, ...rows].map(row => 
        row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')
    ).join('\n');
}

// Generate bulk reports
function generateBulkReports() {
    const completedReports = reports.filter(r => r.status === 'completed' || r.status === 'verified');
    if (completedReports.length === 0) {
        showAlert('No completed reports available for bulk generation', 'warning');
        return;
    }
    
    window.open(`bulk-reports.php?reports=${completedReports.map(r => r.id).join(',')}`, '_blank');
}

// View report details
function viewReport(id) {
    window.open(`view.php?id=${id}`, '_blank');
}

// Print report
function printReport(id) {
    window.open(`print.php?id=${id}`, '_blank');
}

// Edit results
function editResults(id) {
    window.location.href = `edit-results.php?id=${id}`;
}

// Verify report
async function verifyReport(id) {
    if (!confirm('Are you sure you want to verify this report? This action will mark it as final.')) {
        return;
    }
    
    try {
        const response = await fetch('../api/reports.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                status: 'verified'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Report verified successfully', 'success');
            loadReports();
        } else {
            showAlert(result.message || 'Error verifying report', 'danger');
        }
    } catch (error) {
        console.error('Error verifying report:', error);
        showAlert('Error verifying report', 'danger');
    }
}

// Utility functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-BD');
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}
