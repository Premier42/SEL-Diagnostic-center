<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lab System</title>
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
        .card-container {
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
        .quick-action-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        .quick-action-card:hover {
            transform: translateY(-5px);
            color: inherit;
            text-decoration: none;
        }
        .quick-action-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            margin: 0 auto 15px;
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
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                        <a class="nav-link" href="invoices/list.php">
                            <i class="fas fa-file-invoice"></i>Invoices
                        </a>
                        <a class="nav-link" href="tests/list.php">
                            <i class="fas fa-vial"></i>Tests
                        </a>
                        <a class="nav-link" href="doctors/list.php">
                            <i class="fas fa-user-md"></i>Doctors
                        </a>
                        <a class="nav-link" href="reports/list.php">
                            <i class="fas fa-chart-line"></i>Reports
                        </a>
                        <a class="nav-link" href="users/list.php">
                            <i class="fas fa-users"></i>Users
                        </a>
                        <a class="nav-link" href="consumables/list.php">
                            <i class="fas fa-boxes"></i>Inventory
                        </a>
                        <a class="nav-link" href="audit/logs.php">
                            <i class="fas fa-history"></i>Audit Logs
                        </a>
                        <a class="nav-link" href="sms/dashboard.php">
                            <i class="fas fa-sms"></i>SMS Notifications
                        </a>
                        <hr class="my-3" style="border-color: rgba(255,255,255,0.2);">
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">Dashboard</h2>
                            <p class="text-muted">Welcome to the Pathology Laboratory Management System</p>
                        </div>
                        <div>
                            <span class="badge bg-primary fs-6">
                                <i class="fas fa-user me-1"></i>
                                <?php echo $_SESSION['username'] ?? 'User'; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4" id="statsCards">
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <h3 class="mb-1" id="totalInvoices">-</h3>
                                <p class="text-muted mb-0">Total Invoices</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <i class="fas fa-vial"></i>
                                </div>
                                <h3 class="mb-1" id="totalTests">-</h3>
                                <p class="text-muted mb-0">Available Tests</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h3 class="mb-1" id="totalReports">-</h3>
                                <p class="text-muted mb-0">Test Reports</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <h3 class="mb-1" id="totalDoctors">-</h3>
                                <p class="text-muted mb-0">Registered Doctors</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card-container">
                        <h5 class="mb-3">
                            <i class="fas fa-bolt text-primary me-2"></i>
                            Quick Actions
                        </h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="invoices/list.php" class="quick-action-card">
                                    <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <h6>New Invoice</h6>
                                    <small class="text-muted">Create patient invoice</small>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="reports/list.php" class="quick-action-card">
                                    <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                        <i class="fas fa-microscope"></i>
                                    </div>
                                    <h6>Test Reports</h6>
                                    <small class="text-muted">Manage test results</small>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="consumables/list.php" class="quick-action-card">
                                    <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <h6>Inventory</h6>
                                    <small class="text-muted">Stock management</small>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="sms/dashboard.php" class="quick-action-card">
                                    <div class="icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                        <i class="fas fa-sms"></i>
                                    </div>
                                    <h6>Send SMS</h6>
                                    <small class="text-muted">Patient notifications</small>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="card-container">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        Recent Invoices
                                    </h5>
                                    <a href="invoices/list.php" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Invoice #</th>
                                                <th>Patient</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentInvoicesTable">
                                            <tr>
                                                <td colspan="5" class="text-center py-3">
                                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-container">
                                <h5 class="mb-3">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    System Alerts
                                </h5>
                                <div id="systemAlerts">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <small>Loading system status...</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
