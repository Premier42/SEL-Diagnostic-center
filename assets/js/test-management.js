// Test Management JavaScript

let tests = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadTests();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('addTestForm').addEventListener('submit', handleAddTest);
    document.getElementById('searchInput').addEventListener('input', debounce(filterTests, 300));
}

// Load tests
async function loadTests() {
    try {
        const response = await fetch('../api/tests.php');
        tests = await response.json();
        
        updateStats();
        renderTestsTable();
    } catch (error) {
        console.error('Error loading tests:', error);
        showAlert('Error loading tests', 'danger');
    }
}

// Update statistics cards
function updateStats() {
    const totalTests = tests.length;
    const hematologyTests = tests.filter(t => t.category === 'Hematology').length;
    const biochemistryTests = tests.filter(t => t.category === 'Biochemistry').length;
    const avgPrice = tests.length > 0 ? tests.reduce((sum, t) => sum + parseFloat(t.price), 0) / tests.length : 0;
    
    document.getElementById('totalTests').textContent = totalTests;
    document.getElementById('hematologyTests').textContent = hematologyTests;
    document.getElementById('biochemistryTests').textContent = biochemistryTests;
    document.getElementById('avgPrice').textContent = `৳${formatNumber(avgPrice)}`;
}

// Render tests table
function renderTestsTable() {
    const tbody = document.getElementById('testsTableBody');
    
    if (tests.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-vial fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No tests found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = tests.map(test => `
        <tr>
            <td><strong>${test.code}</strong></td>
            <td>
                <div><strong>${test.name}</strong></div>
                <small class="text-muted">${test.description || 'No description'}</small>
            </td>
            <td><span class="badge bg-primary">${test.category}</span></td>
            <td><span class="badge bg-secondary">${test.sample_type}</span></td>
            <td><strong>৳${formatNumber(test.price)}</strong></td>
            <td>
                <button class="btn btn-sm btn-outline-info" onclick="viewParameters('${test.code}')" title="View Parameters">
                    <i class="fas fa-list"></i> Parameters
                </button>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewTest('${test.code}')" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="editTest('${test.code}')" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteTest('${test.code}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Add parameter row to test form
function addParameterRow() {
    const container = document.getElementById('parametersContainer');
    const paramRow = document.createElement('div');
    paramRow.className = 'row mb-2 parameter-row';
    
    paramRow.innerHTML = `
        <div class="col-md-4">
            <input type="text" class="form-control" name="param_name[]" placeholder="Parameter name">
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" name="param_unit[]" placeholder="Unit">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control" name="param_range[]" placeholder="Normal range">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeParameterRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(paramRow);
}

// Remove parameter row
function removeParameterRow(button) {
    button.closest('.parameter-row').remove();
}

// Handle add test form submission
async function handleAddTest(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const parameters = [];
    
    // Collect parameter data
    const paramNames = formData.getAll('param_name[]');
    const paramUnits = formData.getAll('param_unit[]');
    const paramRanges = formData.getAll('param_range[]');
    
    for (let i = 0; i < paramNames.length; i++) {
        if (paramNames[i]) {
            parameters.push({
                name: paramNames[i],
                unit: paramUnits[i] || '',
                normal_range: paramRanges[i] || ''
            });
        }
    }
    
    const testData = {
        code: formData.get('code'),
        name: formData.get('name'),
        category: formData.get('category'),
        sample_type: formData.get('sample_type'),
        price: parseFloat(formData.get('price')),
        method: formData.get('method'),
        description: formData.get('description'),
        parameters: parameters
    };
    
    try {
        const response = await fetch('../api/tests.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(testData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Test created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addTestModal')).hide();
            e.target.reset();
            document.getElementById('parametersContainer').innerHTML = '';
            loadTests();
        } else {
            showAlert(result.message || 'Error creating test', 'danger');
        }
    } catch (error) {
        console.error('Error creating test:', error);
        showAlert('Error creating test', 'danger');
    }
}

// Filter tests
function filterTests() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const sampleFilter = document.getElementById('sampleFilter').value;
    const priceFilter = document.getElementById('priceFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    let filtered = [...tests];
    
    if (categoryFilter) {
        filtered = filtered.filter(test => test.category === categoryFilter);
    }
    
    if (sampleFilter) {
        filtered = filtered.filter(test => test.sample_type === sampleFilter);
    }
    
    if (priceFilter) {
        const [min, max] = priceFilter.split('-').map(p => parseFloat(p.replace('+', '')));
        filtered = filtered.filter(test => {
            const price = parseFloat(test.price);
            if (priceFilter.includes('+')) {
                return price >= min;
            }
            return price >= min && price <= max;
        });
    }
    
    if (searchTerm) {
        filtered = filtered.filter(test => 
            test.name.toLowerCase().includes(searchTerm) ||
            test.code.toLowerCase().includes(searchTerm) ||
            (test.description && test.description.toLowerCase().includes(searchTerm))
        );
    }
    
    // Temporarily replace tests array for rendering
    const originalTests = tests;
    tests = filtered;
    renderTestsTable();
    tests = originalTests;
}

// Clear filters
function clearFilters() {
    document.getElementById('categoryFilter').value = '';
    document.getElementById('sampleFilter').value = '';
    document.getElementById('priceFilter').value = '';
    document.getElementById('searchInput').value = '';
    renderTestsTable();
}

// Export tests
function exportTests() {
    const csv = generateTestsCSV();
    downloadCSV(csv, 'tests.csv');
}

// Generate CSV data for tests
function generateTestsCSV() {
    const headers = ['Code', 'Name', 'Category', 'Sample Type', 'Price', 'Method', 'Description'];
    const rows = tests.map(test => [
        test.code,
        test.name,
        test.category,
        test.sample_type,
        test.price,
        test.method || '',
        test.description || ''
    ]);
    
    return [headers, ...rows].map(row => 
        row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')
    ).join('\n');
}

// View test parameters
async function viewParameters(code) {
    try {
        const response = await fetch(`../api/tests.php?code=${code}`);
        const test = await response.json();
        
        if (test.parameters && test.parameters.length > 0) {
            const parametersHtml = test.parameters.map(param => `
                <tr>
                    <td>${param.parameter_name}</td>
                    <td>${param.unit || '-'}</td>
                    <td>${param.normal_range || '-'}</td>
                </tr>
            `).join('');
            
            const modalHtml = `
                <div class="modal fade" id="parametersModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Test Parameters - ${test.name}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Parameter</th>
                                            <th>Unit</th>
                                            <th>Normal Range</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${parametersHtml}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('parametersModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            new bootstrap.Modal(document.getElementById('parametersModal')).show();
        } else {
            showAlert('No parameters defined for this test', 'info');
        }
    } catch (error) {
        console.error('Error loading test parameters:', error);
        showAlert('Error loading test parameters', 'danger');
    }
}

// View test details
function viewTest(code) {
    window.open(`view.php?code=${code}`, '_blank');
}

// Edit test
function editTest(code) {
    window.location.href = `edit.php?code=${code}`;
}

// Delete test
async function deleteTest(code) {
    if (!confirm('Are you sure you want to delete this test? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('../api/tests.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ code: code })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Test deleted successfully', 'success');
            loadTests();
        } else {
            showAlert(result.message || 'Error deleting test', 'danger');
        }
    } catch (error) {
        console.error('Error deleting test:', error);
        showAlert('Error deleting test', 'danger');
    }
}

// Utility functions
function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

// Initialize with one parameter row
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        addParameterRow();
    }, 500);
});
