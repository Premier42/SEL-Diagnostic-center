<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= config('app.name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #764ba2;
            --secondary-color: #f093fb;
            --secondary-dark: #f5576c;
            --success-color: #43e97b;
            --danger-color: #f5576c;
            --warning-color: #feca57;
            --info-color: #4facfe;
            --dark-color: #2d3748;
            --light-color: #f7fafc;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            max-width: 100vw;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }

        .sidebar-brand:hover {
            color: white;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.6);
            padding: 0.5rem 1.5rem;
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            border-left-color: white;
            transform: translateX(5px);
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 2rem;
            max-width: calc(100vw - var(--sidebar-width));
            overflow-x: hidden;
        }

        .header {
            background: white;
            border-radius: 15px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .welcome-text h2 {
            margin: 0;
            color: var(--dark-color);
            font-weight: 600;
        }

        .welcome-text p {
            margin: 0;
            color: #6b7280;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-card.primary::before {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }

        .stat-card.success::before {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }

        .stat-card.warning::before {
            background: linear-gradient(135deg, #feca57, #ff9f43);
        }

        .stat-card.info::before {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-card.primary .stat-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }

        .stat-card.success .stat-icon {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }

        .stat-card.warning .stat-icon {
            background: linear-gradient(135deg, #feca57, #ff9f43);
        }

        .stat-card.info .stat-icon {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
            max-width: 100%;
            overflow: hidden;
        }

        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .chart-container {
            flex: 1;
            position: relative;
            min-height: 0;
            overflow: hidden;
        }

        .chart-card canvas {
            max-height: 320px !important;
            height: 320px !important;
            width: 100% !important;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .recent-activities {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 0.9rem;
        }

        .activity-content {
            flex: 1;
        }

        .activity-title {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            border-radius: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 10px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 100%;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="/dashboard" class="sidebar-brand">
                <i class="fas fa-flask me-2"></i>
                SEL Diagnostic
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="/dashboard" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Operations</div>
                <a href="/invoices" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <span>Invoices</span>
                </a>
                <a href="/tests" class="nav-link">
                    <i class="fas fa-vial"></i>
                    <span>Tests</span>
                </a>
                <a href="/reports" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Reports</span>
                </a>
                <a href="/doctors" class="nav-link">
                    <i class="fas fa-user-md"></i>
                    <span>Doctors</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="/users" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                <a href="/inventory" class="nav-link">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                </a>
                <a href="/audit" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span>Audit Logs</span>
                </a>
                <a href="/sms" class="nav-link">
                    <i class="fas fa-sms"></i>
                    <span>SMS</span>
                </a>
            </div>

            <div class="nav-section">
                <a href="/logout" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="welcome-text">
                <h2>Dashboard</h2>
                <p>Welcome back, <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']) ?>!</p>
            </div>
            <div class="user-info">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-2"></i>
                        <?= htmlspecialchars($_SESSION['username']) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/profile"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="/settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['username'], 0, 1)) ?>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="stat-value" id="totalInvoices"><?= number_format($stats['total_invoices'] ?? 0) ?></div>
                <div class="stat-label">Total Invoices</div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">৳<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?= number_format($stats['pending_invoices'] ?? 0) ?></div>
                <div class="stat-label">Pending Payments</div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-value"><?= number_format($stats['reports_today'] ?? 0) ?></div>
                <div class="stat-label">Reports Today</div>
            </div>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-area me-2"></i>
                    Monthly Revenue Trend
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">
                    <i class="fas fa-chart-pie me-2"></i>
                    Test Categories
                </div>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="recent-activities">
            <div class="chart-title">
                <i class="fas fa-bell me-2"></i>
                Recent Activities
            </div>

            <?php if (!empty($recent_invoices)): ?>
                <?php foreach ($recent_invoices as $invoice): ?>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">
                                New invoice for <?= htmlspecialchars($invoice['patient_name']) ?>
                            </div>
                            <div class="activity-time">
                                <?= date('M j, Y - g:i A', strtotime($invoice['created_at'])) ?>
                            </div>
                        </div>
                        <div class="activity-amount">
                            <span class="badge bg-<?= $invoice['payment_status'] === 'paid' ? 'success' : ($invoice['payment_status'] === 'pending' ? 'warning' : 'info') ?>">
                                ৳<?= number_format($invoice['total_amount'], 2) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2"></i>
                    <p>No recent activities</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueData = <?= json_encode($monthly_revenue ?? []) ?>;

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => new Date(item.month + '-01').toLocaleDateString('en-US', { month: 'short', year: 'numeric' })),
                datasets: [{
                    label: 'Revenue',
                    data: revenueData.map(item => item.revenue),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryData = <?= json_encode($test_categories ?? []) ?>;

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.category),
                datasets: [{
                    data: categoryData.map(item => item.count),
                    backgroundColor: [
                        '#667eea',
                        '#43e97b',
                        '#feca57',
                        '#4facfe',
                        '#f5576c'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Mobile sidebar toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Auto-refresh stats every 30 seconds
        setInterval(() => {
            fetch('/api/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalInvoices').textContent = data.invoices || 0;
                })
                .catch(error => console.error('Error updating stats:', error));
        }, 30000);
    </script>
</body>
</html>