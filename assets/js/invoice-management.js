// Invoice Management JavaScript

let invoices = [];
let doctors = [];
let tests = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadDoctors();
    loadTests();
    loadInvoices();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('addInvoiceForm').addEventListener('submit', handleAddInvoice);
    document.getElementById('searchInput').addEventListener('input', debounce(filterInvoices, 300));
}

// Load doctors for dropdown
async function loadDoctors() {
    try {
        const response = await fetch('../api/doctors.php');
        doctors = await response.json();
        
        const doctorSelect = document.querySelector('select[name="doctor_id"]');
        doctorSelect.innerHTML = '<option value="">Select Doctor</option>';
        
        doctors.forEach(doctor => {
            doctorSelect.innerHTML += `<option value="${doctor.id}">${doctor.name} - ${doctor.workplace}</option>`;
        });
    } catch (error) {
        console.error('Error loading doctors:', error);
    }
}

// Load tests for selection
async function loadTests() {
    try {
        const response = await fetch('../api/tests.php');
        tests = await response.json();
    } catch (error) {
        console.error('Error loading tests:', error);
    }
}

// Load invoices
async function loadInvoices() {
    try {
        const response = await fetch('../api/invoices.php');
        const data = await response.json();
        invoices = data.invoices || [];
        
        updateStats(data.stats || {});
        renderInvoicesTable();
    } catch (error) {
        console.error('Error loading invoices:', error);
        showAlert('Error loading invoices', 'danger');
    }
}

// Update statistics cards
function updateStats(stats) {
    document.getElementById('totalInvoices').textContent = stats.total || 0;
    document.getElementById('paidInvoices').textContent = stats.paid || 0;
    document.getElementById('pendingInvoices').textContent = stats.pending || 0;
    document.getElementById('totalRevenue').textContent = `৳${formatNumber(stats.revenue || 0)}`;
}

// Render invoices table
function renderInvoicesTable() {
    const tbody = document.getElementById('invoicesTableBody');
    
    if (invoices.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No invoices found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = invoices.map(invoice => `
        <tr>
            <td><strong>#${String(invoice.id).padStart(4, '0')}</strong></td>
            <td>
                <div><strong>${invoice.patient_name}</strong></div>
                <small class="text-muted">${invoice.patient_age}Y, ${invoice.patient_gender}</small><br>
                <small class="text-muted">${invoice.patient_phone}</small>
            </td>
            <td>
                <div>${invoice.doctor_name || 'N/A'}</div>
                <small class="text-muted">${invoice.doctor_workplace || ''}</small>
            </td>
            <td>${formatDate(invoice.created_at)}</td>
            <td><strong>৳${formatNumber(invoice.total_amount)}</strong></td>
            <td>৳${formatNumber(invoice.amount_paid)}</td>
            <td>${getStatusBadge(invoice.payment_status)}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewInvoice(${invoice.id})" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="printInvoice(${invoice.id})" title="Print">
                        <i class="fas fa-print"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="editInvoice(${invoice.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${invoice.payment_status !== 'paid' ? `
                        <button class="btn btn-outline-info" onclick="addPayment(${invoice.id})" title="Payment">
                            <i class="fas fa-money-bill"></i>
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
        'paid': '<span class="badge bg-success">Paid</span>',
        'partial': '<span class="badge bg-warning">Partial</span>',
        'pending': '<span class="badge bg-danger">Pending</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

// Add test row to invoice form
function addTestRow() {
    const container = document.getElementById('testsContainer');
    const testRow = document.createElement('div');
    testRow.className = 'row mb-2 test-row';
    
    testRow.innerHTML = `
        <div class="col-md-6">
            <select class="form-control test-select" name="test_code[]" onchange="updateTestPrice(this)">
                <option value="">Select Test</option>
                ${tests.map(test => `<option value="${test.code}" data-price="${test.price}">${test.name} - ৳${formatNumber(test.price)}</option>`).join('')}
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" class="form-control test-price" name="test_price[]" placeholder="Price" step="0.01" onchange="calculateTotal()">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeTestRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(testRow);
}

// Remove test row
function removeTestRow(button) {
    button.closest('.test-row').remove();
    calculateTotal();
}

// Update test price when test is selected
function updateTestPrice(select) {
    const option = select.selectedOptions[0];
    const priceInput = select.closest('.test-row').querySelector('.test-price');
    
    if (option && option.dataset.price) {
        priceInput.value = option.dataset.price;
        calculateTotal();
    }
}

// Calculate total amount
function calculateTotal() {
    const priceInputs = document.querySelectorAll('.test-price');
    const discountInput = document.querySelector('input[name="discount_amount"]');
    
    let total = 0;
    priceInputs.forEach(input => {
        if (input.value) {
            total += parseFloat(input.value);
        }
    });
    
    const discount = parseFloat(discountInput.value) || 0;
    const finalTotal = Math.max(0, total - discount);
    
    document.getElementById('totalAmount').textContent = formatNumber(finalTotal);
}

// Handle add invoice form submission
async function handleAddInvoice(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const tests = [];
    
    // Collect test data
    const testCodes = formData.getAll('test_code[]');
    const testPrices = formData.getAll('test_price[]');
    
    for (let i = 0; i < testCodes.length; i++) {
        if (testCodes[i] && testPrices[i]) {
            tests.push({
                code: testCodes[i],
                price: parseFloat(testPrices[i])
            });
        }
    }
    
    const invoiceData = {
        patient_name: formData.get('patient_name'),
        patient_age: parseInt(formData.get('patient_age')),
        patient_gender: formData.get('patient_gender'),
        patient_phone: formData.get('patient_phone'),
        doctor_id: parseInt(formData.get('doctor_id')),
        discount_amount: parseFloat(formData.get('discount_amount')) || 0,
        notes: formData.get('notes'),
        tests: tests
    };
    
    try {
        const response = await fetch('../api/invoices.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(invoiceData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Invoice created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addInvoiceModal')).hide();
            e.target.reset();
            document.getElementById('testsContainer').innerHTML = '';
            loadInvoices();
        } else {
            showAlert(result.message || 'Error creating invoice', 'danger');
        }
    } catch (error) {
        console.error('Error creating invoice:', error);
        showAlert('Error creating invoice', 'danger');
    }
}

// Filter invoices
function filterInvoices() {
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    let filtered = [...invoices];
    
    if (statusFilter) {
        filtered = filtered.filter(invoice => invoice.payment_status === statusFilter);
    }
    
    if (dateFrom) {
        filtered = filtered.filter(invoice => new Date(invoice.created_at) >= new Date(dateFrom));
    }
    
    if (dateTo) {
        filtered = filtered.filter(invoice => new Date(invoice.created_at) <= new Date(dateTo));
    }
    
    if (searchTerm) {
        filtered = filtered.filter(invoice => 
            invoice.patient_name.toLowerCase().includes(searchTerm) ||
            invoice.patient_phone.includes(searchTerm)
        );
    }
    
    // Temporarily replace invoices array for rendering
    const originalInvoices = invoices;
    invoices = filtered;
    renderInvoicesTable();
    invoices = originalInvoices;
}

// Clear filters
function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    document.getElementById('searchInput').value = '';
    renderInvoicesTable();
}

// Export invoices
function exportInvoices() {
    const csv = generateCSV();
    downloadCSV(csv, 'invoices.csv');
}

// Generate CSV data
function generateCSV() {
    const headers = ['Invoice#', 'Patient Name', 'Age', 'Gender', 'Phone', 'Doctor', 'Date', 'Total Amount', 'Amount Paid', 'Status', 'Notes'];
    const rows = invoices.map(invoice => [
        `#${String(invoice.id).padStart(4, '0')}`,
        invoice.patient_name,
        invoice.patient_age,
        invoice.patient_gender,
        invoice.patient_phone,
        invoice.doctor_name || 'N/A',
        formatDate(invoice.created_at),
        invoice.total_amount,
        invoice.amount_paid,
        invoice.payment_status,
        invoice.notes || ''
    ]);
    
    return [headers, ...rows].map(row => 
        row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')
    ).join('\n');
}

// Download CSV file
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

// View invoice details
function viewInvoice(id) {
    window.open(`view.php?id=${id}`, '_blank');
}

// Print invoice
function printInvoice(id) {
    window.open(`print.php?id=${id}`, '_blank');
}

// Edit invoice
function editInvoice(id) {
    window.location.href = `edit.php?id=${id}`;
}

// Add payment to invoice
function addPayment(id) {
    window.location.href = `payment.php?id=${id}`;
}

// Utility functions
function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

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

// Initialize with one test row
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        addTestRow();
    }, 500);
});
