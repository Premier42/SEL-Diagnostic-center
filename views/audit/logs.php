<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Lab System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .main-content {
            padding: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: none;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-top: 30px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
        }
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 10px;
            padding: 8px 16px;
            font-weight: 500;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .badge {
            font-size: 0.75em;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .log-details {
            font-size: 0.9em;
            color: #6c757d;
        }
        .json-data {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.8em;
            max-height: 100px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-4">
                        <h4 class="text-white mb-4">
                            <i class="fas fa-flask me-2"></i>
                            Lab System
                        </h4>
                    </div>
                    <nav class="nav flex-column px-3">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                        <a class="nav-link" href="../invoices/list.php">
                            <i class="fas fa-file-invoice"></i>Invoices
                        </a>
                        <a class="nav-link" href="../tests/list.php">
                            <i class="fas fa-vial"></i>Tests
                        </a>
                        <a class="nav-link" href="../doctors/list.php">
                            <i class="fas fa-user-md"></i>Doctors
                        </a>
                        <a class="nav-link" href="../reports/list.php">
                            <i class="fas fa-chart-line"></i>Reports
                        </a>
                        <a class="nav-link" href="../users/list.php">
                            <i class="fas fa-users"></i>Users
                        </a>
                        <a class="nav-link" href="../consumables/list.php">
                            <i class="fas fa-boxes"></i>Inventory
                        </a>
                        <a class="nav-link active" href="logs.php">
                            <i class="fas fa-history"></i>Audit Logs
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">Audit Logs</h2>
                            <p class="text-muted">System activity and security audit trail</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary me-2" onclick="exportLogs()">
                                <i class="fas fa-download me-2"></i>Export
                            </button>
                            <button class="btn btn-primary" onclick="showCleanupModal()">
                                <i class="fas fa-broom me-2"></i>Cleanup
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4" id="statsCards">
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-list"></i>
                                </div>
                                <h3 class="mb-1" id="totalEvents">-</h3>
                                <p class="text-muted mb-0">Total Events</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="mb-1" id="uniqueUsers">-</h3>
                                <p class="text-muted mb-0">Unique Users</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <h3 class="mb-1" id="totalLogins">-</h3>
                                <p class="text-muted mb-0">Logins</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                    <i class="fas fa-download"></i>
                                </div>
                                <h3 class="mb-1" id="totalExports">-</h3>
                                <p class="text-muted mb-0">Exports</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="table-container">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Action</label>
                                <select class="form-select" id="actionFilter">
                                    <option value="">All Actions</option>
                                    <option value="LOGIN">Login</option>
                                    <option value="LOGOUT">Logout</option>
                                    <option value="CREATE">Create</option>
                                    <option value="UPDATE">Update</option>
                                    <option value="DELETE">Delete</option>
                                    <option value="EXPORT">Export</option>
                                    <option value="PAYMENT">Payment</option>
                                    <option value="TEST_RESULT">Test Result</option>
                                    <option value="VERIFY">Verify</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Table</label>
                                <select class="form-select" id="tableFilter">
                                    <option value="">All Tables</option>
                                    <option value="invoices">Invoices</option>
                                    <option value="test_reports">Test Reports</option>
                                    <option value="users">Users</option>
                                    <option value="doctors">Doctors</option>
                                    <option value="tests">Tests</option>
                                    <option value="consumables">Inventory</option>
                                    <option value="system">System</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">From Date</label>
                                <input type="date" class="form-control" id="dateFrom">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">To Date</label>
                                <input type="date" class="form-control" id="dateTo">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100" onclick="loadLogs()">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                            </div>
                        </div>

                        <!-- Audit Logs Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Table</th>
                                        <th>Record ID</th>
                                        <th>IP Address</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody id="logsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div id="paginationInfo" class="text-muted">
                                Showing 0 - 0 of 0 entries
                            </div>
                            <nav>
                                <ul class="pagination mb-0" id="pagination">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Details Modal -->
    <div class="modal fade" id="logDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Audit Log Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="logDetailsContent">
                </div>
            </div>
        </div>
    </div>

    <!-- Cleanup Modal -->
    <div class="modal fade" id="cleanupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cleanup Old Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Keep logs for (days)</label>
                        <input type="number" class="form-control" id="daysToKeep" value="365" min="30">
                        <div class="form-text">Minimum 30 days required for compliance</div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This action will permanently delete old audit logs and cannot be undone.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="cleanupLogs()">
                        <i class="fas fa-broom me-2"></i>Cleanup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/audit-logs.js"></script>
</body>
</html>