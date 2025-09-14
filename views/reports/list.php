<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Management - Pathology Lab</title>
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
                    <a href="list.php" class="sidebar-item active">
                        <i class="fas fa-chart-bar"></i>
                        <span>Reports</span>
                    </a>
                </div>
                
                <div class="sidebar-section">
                    <div class="section-title">MANAGEMENT</div>
                    <a href="../doctors/list.php" class="sidebar-item">
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
                        <h2><i class="fas fa-chart-bar text-primary"></i> Test Reports</h2>
                        <div>
                            <button class="btn btn-success" onclick="generateBulkReports()">
                                <i class="fas fa-file-export"></i> Bulk Reports
                            </button>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResultsModal">
                                <i class="fas fa-plus"></i> Add Results
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Total Reports</h6>
                                        <h3 id="totalReports">0</h3>
                                    </div>
                                    <i class="fas fa-file-medical fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Completed</h6>
                                        <h3 id="completedReports">0</h3>
                                    </div>
                                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Pending</h6>
                                        <h3 id="pendingReports">0</h3>
                                    </div>
                                    <i class="fas fa-clock fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>Abnormal</h6>
                                        <h3 id="abnormalResults">0</h3>
                                    </div>
                                    <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="verified">Verified</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Test Type</label>
                                <select class="form-select" id="testFilter">
                                    <option value="">All Tests</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" class="form-control" id="dateFrom">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" class="form-control" id="dateTo">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Patient name or invoice number">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button class="btn btn-primary me-2" onclick="filterReports()">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <button class="btn btn-secondary me-2" onclick="clearFilters()">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                                <button class="btn btn-success" onclick="exportReports()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="reportsTable">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Patient</th>
                                        <th>Test</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Technician</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reportsTableBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Results Modal -->
    <div class="modal fade" id="addResultsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Test Results</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addResultsForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Select Report *</label>
                                <select class="form-select" name="report_id" id="reportSelect" required onchange="loadTestParameters()">
                                    <option value="">Select a pending report</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Technician Notes</label>
                                <textarea class="form-control" name="notes" rows="2" placeholder="Additional notes or observations"></textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <h6>Test Parameters</h6>
                        <div id="parametersContainer">
                            <p class="text-muted">Select a report to load test parameters</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Results</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/report-management.js"></script>
</body>
</html>
