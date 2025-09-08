<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Management - Pathology Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/modern-sidebar.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="sidebar-header">
                    <h4><i class="fas fa-microscope"></i> Lab System</h4>
                </div>
                
                <div class="sidebar-section">
                    <div class="section-title">MAIN</div>
                    <a href="../admin/simple_dashboard.php" class="sidebar-item">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                
                <div class="sidebar-section">
                    <div class="section-title">OPERATIONS</div>
                    <a href="../invoices/list.php" class="sidebar-item">
                        <i class="fas fa-file-invoice"></i>
                        <span>Invoices</span>
                    </a>
                    <a href="../tests/list.php" class="sidebar-item">
                        <i class="fas fa-vial"></i>
                        <span>Tests</span>
                    </a>
                    <a href="../reports/list.php" class="sidebar-item">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </div>
                
                <div class="sidebar-section">
                    <div class="section-title">MANAGEMENT</div>
                    <a href="list.php" class="sidebar-item active">
                        <i class="fas fa-user-md"></i>
                        <span>Doctors</span>
                    </a>
                    <a href="../users/list.php" class="sidebar-item">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                    <a href="../consumables/list.php" class="sidebar-item">
                        <i class="fas fa-boxes"></i>
                        <span>Inventory</span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="content-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2><i class="fas fa-user-md text-primary"></i> Doctor Management</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                            <i class="fas fa-plus"></i> New Doctor
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Total Doctors</h6>
                                        <h3 id="totalDoctors">0</h3>
                                    </div>
                                    <i class="fas fa-user-md fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Active Referrals</h6>
                                        <h3 id="activeReferrals">0</h3>
                                    </div>
                                    <i class="fas fa-handshake fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>This Month</h6>
                                        <h3 id="monthlyReferrals">0</h3>
                                    </div>
                                    <i class="fas fa-calendar fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Hospitals</h6>
                                        <h3 id="uniqueHospitals">0</h3>
                                    </div>
                                    <i class="fas fa-hospital fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Doctor name or workplace">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Workplace</label>
                                <select class="form-select" id="workplaceFilter">
                                    <option value="">All Workplaces</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Specialization</label>
                                <input type="text" class="form-control" id="specializationFilter" placeholder="e.g., Cardiology">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button class="btn btn-primary" onclick="filterDoctors()">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <button class="btn btn-secondary" onclick="clearFilters()">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                                <button class="btn btn-success" onclick="exportDoctors()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctors Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="doctorsTable">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Qualifications</th>
                                        <th>Workplace</th>
                                        <th>Contact</th>
                                        <th>Referrals</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="doctorsTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Doctor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDoctorForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="name" placeholder="Dr. Full Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Qualifications *</label>
                                <input type="text" class="form-control" name="qualifications" placeholder="MBBS, MD, etc." required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Workplace *</label>
                                <input type="text" class="form-control" name="workplace" placeholder="Hospital/Clinic Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Specialization</label>
                                <input type="text" class="form-control" name="specialization" placeholder="e.g., Cardiology">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="text" class="form-control" name="phone" placeholder="+880-1XXX-XXXXXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" placeholder="doctor@hospital.com" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2" placeholder="Full address"></textarea>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">License Number</label>
                                <input type="text" class="form-control" name="license_number" placeholder="Medical license number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Experience (Years)</label>
                                <input type="number" class="form-control" name="experience_years" placeholder="Years of experience">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/doctor-management.js"></script>
</body>
</html>
