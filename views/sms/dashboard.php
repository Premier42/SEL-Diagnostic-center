<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Notifications - Lab System</title>
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
        .sms-template {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .sms-template:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        .sms-template.selected {
            background: #e7f3ff;
            border: 2px solid #667eea;
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
                        <a class="nav-link" href="../audit/logs.php">
                            <i class="fas fa-history"></i>Audit Logs
                        </a>
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-sms"></i>SMS Notifications
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">SMS Notifications</h2>
                            <p class="text-muted">Send notifications to patients via SMS</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary me-2" onclick="showSMSLogs()">
                                <i class="fas fa-history me-2"></i>SMS Logs
                            </button>
                            <button class="btn btn-primary" onclick="showSendSMSModal()">
                                <i class="fas fa-paper-plane me-2"></i>Send SMS
                            </button>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4" id="statsCards">
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <h3 class="mb-1" id="totalSent">-</h3>
                                <p class="text-muted mb-0">Total Sent</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h3 class="mb-1" id="successful">-</h3>
                                <p class="text-muted mb-0">Successful</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <h3 class="mb-1" id="failed">-</h3>
                                <p class="text-muted mb-0">Failed</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stats-card">
                                <div class="icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="mb-1" id="uniqueRecipients">-</h3>
                                <p class="text-muted mb-0">Unique Recipients</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-container">
                                <h5 class="mb-3">
                                    <i class="fas fa-bolt text-primary me-2"></i>
                                    Quick Actions
                                </h5>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" onclick="sendBulkReportNotifications()">
                                        <i class="fas fa-file-medical me-2"></i>
                                        Notify Ready Reports
                                    </button>
                                    <button class="btn btn-outline-warning" onclick="sendPaymentReminders()">
                                        <i class="fas fa-money-bill me-2"></i>
                                        Send Payment Reminders
                                    </button>
                                    <button class="btn btn-outline-info" onclick="showCustomSMSModal()">
                                        <i class="fas fa-edit me-2"></i>
                                        Send Custom Message
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-container">
                                <h5 class="mb-3">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    SMS Service Info
                                </h5>
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Free Tier Limitations</h6>
                                    <ul class="mb-0">
                                        <li>~1 SMS per day per phone number</li>
                                        <li>Uses Textbelt free service</li>
                                        <li>Bangladesh numbers (+880) supported</li>
                                        <li>All attempts are logged for audit</li>
                                    </ul>
                                </div>
                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        All SMS communications are logged and audited for compliance.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent SMS Activity -->
                    <div class="card-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Recent SMS Activity
                            </h5>
                            <button class="btn btn-sm btn-outline-primary" onclick="loadSMSLogs()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Phone Number</th>
                                        <th>Message Preview</th>
                                        <th>Status</th>
                                        <th>Response</th>
                                    </tr>
                                </thead>
                                <tbody id="smsLogsTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Send SMS Modal -->
    <div class="modal fade" id="sendSMSModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Send SMS Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="smsForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">SMS Type</label>
                                <select class="form-select" id="smsType" onchange="updateSMSForm()">
                                    <option value="report_ready">Report Ready</option>
                                    <option value="payment_reminder">Payment Reminder</option>
                                    <option value="appointment_reminder">Appointment Reminder</option>
                                    <option value="custom">Custom Message</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phoneNumber" placeholder="+8801XXXXXXXXX" required>
                                <div class="form-text">Use E.164 format (+8801XXXXXXXXX)</div>
                            </div>
                        </div>

                        <div id="dynamicFields">
                            <!-- Dynamic fields based on SMS type -->
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message Preview</label>
                            <textarea class="form-control" id="messagePreview" rows="3" readonly></textarea>
                        </div>

                        <div class="alert alert-warning" id="dailyLimitWarning" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This phone number has reached the daily SMS limit.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="sendSMS()" id="sendSMSBtn">
                        <i class="fas fa-paper-plane me-2"></i>Send SMS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Logs Modal -->
    <div class="modal fade" id="smsLogsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">SMS Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Timestamp</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody id="fullSMSLogsTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/sms-dashboard.js"></script>
</body>
</html>