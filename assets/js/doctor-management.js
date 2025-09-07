// Doctor Management JavaScript

let doctors = [];
let referralStats = {};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadDoctors();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('addDoctorForm').addEventListener('submit', handleAddDoctor);
    document.getElementById('searchInput').addEventListener('input', debounce(filterDoctors, 300));
}

// Load doctors
async function loadDoctors() {
    try {
        const response = await fetch('../api/doctors.php');
        doctors = await response.json();
        
        await loadReferralStats();
        updateStats();
        renderDoctorsTable();
        populateWorkplaceFilter();
    } catch (error) {
        console.error('Error loading doctors:', error);
        showAlert('Error loading doctors', 'danger');
    }
}

// Load referral statistics
async function loadReferralStats() {
    try {
        const response = await fetch('../api/referral-stats.php');
        referralStats = await response.json();
    } catch (error) {
        console.error('Error loading referral stats:', error);
        referralStats = {};
    }
}

// Update statistics cards
function updateStats() {
    const totalDoctors = doctors.length;
    const uniqueHospitals = [...new Set(doctors.map(d => d.workplace))].length;
    
    document.getElementById('totalDoctors').textContent = totalDoctors;
    document.getElementById('uniqueHospitals').textContent = uniqueHospitals;
    document.getElementById('activeReferrals').textContent = referralStats.active || 0;
    document.getElementById('monthlyReferrals').textContent = referralStats.monthly || 0;
}

// Populate workplace filter
function populateWorkplaceFilter() {
    const workplaces = [...new Set(doctors.map(d => d.workplace))].sort();
    const select = document.getElementById('workplaceFilter');
    
    select.innerHTML = '<option value="">All Workplaces</option>';
    workplaces.forEach(workplace => {
        select.innerHTML += `<option value="${workplace}">${workplace}</option>`;
    });
}

// Render doctors table
function renderDoctorsTable() {
    const tbody = document.getElementById('doctorsTableBody');
    
    if (doctors.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No doctors found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = doctors.map(doctor => {
        const referrals = referralStats.doctors && referralStats.doctors[doctor.id] || 0;
        
        return `
            <tr>
                <td>
                    <div><strong>${doctor.name}</strong></div>
                    <small class="text-muted">${doctor.specialization || 'General'}</small>
                </td>
                <td>
                    <span class="badge bg-info">${doctor.qualifications}</span>
                </td>
                <td>
                    <div><strong>${doctor.workplace}</strong></div>
                    <small class="text-muted">${doctor.address || 'No address'}</small>
                </td>
                <td>
                    <div><i class="fas fa-phone"></i> ${doctor.phone}</div>
                    <div><i class="fas fa-envelope"></i> ${doctor.email}</div>
                </td>
                <td>
                    <span class="badge bg-primary">${referrals} referrals</span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="viewDoctor(${doctor.id})" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-success" onclick="viewReferrals(${doctor.id})" title="Referrals">
                            <i class="fas fa-list"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="editDoctor(${doctor.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteDoctor(${doctor.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Handle add doctor form submission
async function handleAddDoctor(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const doctorData = {
        name: formData.get('name'),
        qualifications: formData.get('qualifications'),
        workplace: formData.get('workplace'),
        specialization: formData.get('specialization'),
        phone: formData.get('phone'),
        email: formData.get('email'),
        address: formData.get('address'),
        license_number: formData.get('license_number'),
        experience_years: formData.get('experience_years')
    };
    
    try {
        const response = await fetch('../api/doctors.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(doctorData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Doctor added successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('addDoctorModal')).hide();
            e.target.reset();
            loadDoctors();
        } else {
            showAlert(result.message || 'Error adding doctor', 'danger');
        }
    } catch (error) {
        console.error('Error adding doctor:', error);
        showAlert('Error adding doctor', 'danger');
    }
}

// Filter doctors
function filterDoctors() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const workplaceFilter = document.getElementById('workplaceFilter').value;
    const specializationFilter = document.getElementById('specializationFilter').value.toLowerCase();
    
    let filtered = [...doctors];
    
    if (searchTerm) {
        filtered = filtered.filter(doctor => 
            doctor.name.toLowerCase().includes(searchTerm) ||
            doctor.workplace.toLowerCase().includes(searchTerm) ||
            doctor.qualifications.toLowerCase().includes(searchTerm)
        );
    }
    
    if (workplaceFilter) {
        filtered = filtered.filter(doctor => doctor.workplace === workplaceFilter);
    }
    
    if (specializationFilter) {
        filtered = filtered.filter(doctor => 
            doctor.specialization && doctor.specialization.toLowerCase().includes(specializationFilter)
        );
    }
    
    // Temporarily replace doctors array for rendering
    const originalDoctors = doctors;
    doctors = filtered;
    renderDoctorsTable();
    doctors = originalDoctors;
}

// Clear filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('workplaceFilter').value = '';
    document.getElementById('specializationFilter').value = '';
    renderDoctorsTable();
}

// Export doctors
function exportDoctors() {
    const csv = generateDoctorsCSV();
    downloadCSV(csv, 'doctors.csv');
}

// Generate CSV data for doctors
function generateDoctorsCSV() {
    const headers = ['Name', 'Qualifications', 'Specialization', 'Workplace', 'Phone', 'Email', 'Address', 'License', 'Experience'];
    const rows = doctors.map(doctor => [
        doctor.name,
        doctor.qualifications,
        doctor.specialization || '',
        doctor.workplace,
        doctor.phone,
        doctor.email,
        doctor.address || '',
        doctor.license_number || '',
        doctor.experience_years || ''
    ]);
    
    return [headers, ...rows].map(row => 
        row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',')
    ).join('\n');
}

// View doctor details
function viewDoctor(id) {
    window.open(`view.php?id=${id}`, '_blank');
}

// View doctor referrals
function viewReferrals(id) {
    window.open(`../invoices/list.php?doctor_id=${id}`, '_blank');
}

// Edit doctor
function editDoctor(id) {
    window.location.href = `edit.php?id=${id}`;
}

// Delete doctor
async function deleteDoctor(id) {
    const doctor = doctors.find(d => d.id === id);
    if (!confirm(`Are you sure you want to delete Dr. ${doctor.name}? This action cannot be undone.`)) {
        return;
    }
    
    try {
        const response = await fetch('../api/doctors.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Doctor deleted successfully', 'success');
            loadDoctors();
        } else {
            showAlert(result.message || 'Error deleting doctor', 'danger');
        }
    } catch (error) {
        console.error('Error deleting doctor:', error);
        showAlert('Error deleting doctor', 'danger');
    }
}

// Utility functions
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
