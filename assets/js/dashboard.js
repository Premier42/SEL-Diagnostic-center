// Dashboard JavaScript

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    loadRecentInvoices();
    loadSystemAlerts();
});

// Load dashboard statistics
async function loadDashboardStats() {
    try {
        const response = await fetch('../api/dashboard-stats.php');
        const stats = await response.json();
        
        if (response.ok) {
            document.getElementById('totalInvoices').textContent = stats.invoices || 0;
            document.getElementById('totalTests').textContent = stats.tests || 0;
            document.getElementById('totalReports').textContent = stats.reports || 0;
            document.getElementById('totalDoctors').textContent = stats.doctors || 0;
        }
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
    }
}

// Load recent invoices
async function loadRecentInvoices() {
    try {
        const response = await fetch('../api/invoices.php?limit=5');
        const data = await response.json();
        
        if (response.ok && data.invoices) {
            displayRecentInvoices(data.invoices);
        }
    } catch (error) {
        console.error('Error loading recent invoices:', error);
        document.getElementById('recentInvoicesTable').innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-3 text-muted">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading recent invoices
                </td>
            </tr>
        `;
    }
}

// Display recent invoices
function displayRecentInvoices(invoices) {
    const tbody = document.getElementById('recentInvoicesTable');
    
    if (!invoices || invoices.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-3 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    No recent invoices found
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = invoices.map(invoice => `
        <tr>
            <td>
                <strong>#${String(invoice.id).padStart(4, '0')}</strong>
            </td>
            <td>
                <div class="fw-medium">${invoice.patient_name}</div>
                <small class="text-muted">${invoice.patient_phone || ''}</small>
            </td>
            <td>
                <strong>৳${parseFloat(invoice.total_amount).toFixed(2)}</strong>
            </td>
            <td>
                <span class="badge ${getStatusBadgeClass(invoice.payment_status)}">
                    ${invoice.payment_status}
                </span>
            </td>
            <td>
                <small>${formatDate(invoice.created_at)}</small>
            </td>
        </tr>
    `).join('');
}

// Load system alerts
async function loadSystemAlerts() {
    try {
        const response = await fetch('../api/system-alerts.php');
        const alerts = await response.json();
        
        if (response.ok) {
            displaySystemAlerts(alerts);
        }
    } catch (error) {
        console.error('Error loading system alerts:', error);
        document.getElementById('systemAlerts').innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <small>Unable to load system alerts</small>
            </div>
        `;
    }
}

// Display system alerts
function displaySystemAlerts(alerts) {
    const container = document.getElementById('systemAlerts');
    
    if (!alerts || alerts.length === 0) {
        container.innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <small>All systems operational</small>
            </div>
        `;
        return;
    }
    
    container.innerHTML = alerts.map(alert => `
        <div class="alert alert-${alert.type} alert-dismissible fade show">
            <i class="fas fa-${getAlertIcon(alert.type)} me-2"></i>
            <small>${alert.message}</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `).join('');
}

// Get status badge class
function getStatusBadgeClass(status) {
    const classes = {
        'paid': 'bg-success',
        'pending': 'bg-warning text-dark',
        'partial': 'bg-info',
        'overdue': 'bg-danger'
    };
    return classes[status] || 'bg-secondary';
}

// Get alert icon
function getAlertIcon(type) {
    const icons = {
        'success': 'check-circle',
        'info': 'info-circle',
        'warning': 'exclamation-triangle',
        'danger': 'exclamation-circle'
    };
    return icons[type] || 'info-circle';
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}
