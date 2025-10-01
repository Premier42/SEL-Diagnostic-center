<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($test['name'] ?? 'Test Details') ?> - SEL Diagnostic Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
        }

        .main-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .stats-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .stats-card h3 {
            margin-bottom: 0.5rem;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .badge {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border: none;
            font-weight: 600;
            color: #495057;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .test-code {
            background: var(--primary-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <span class="test-code me-3"><?= htmlspecialchars($test['code'] ?? 'N/A') ?></span>
                        <?= htmlspecialchars($test['name'] ?? 'Test Details') ?>
                    </h1>
                    <p class="mb-0 opacity-75"><?= htmlspecialchars($test['category'] ?? 'Uncategorized') ?> Test</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="/tests" class="btn btn-light me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Tests
                    </a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="/tests/<?= htmlspecialchars($test['code'] ?? '') ?>/edit" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container fade-in">
        <!-- Test Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?= number_format($usage_stats['total_orders'] ?? 0) ?></h3>
                    <p class="mb-0">Total Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3><?= number_format($usage_stats['unique_patients'] ?? 0) ?></h3>
                    <p class="mb-0">Patients Tested</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>৳<?= number_format($usage_stats['total_revenue'] ?? 0) ?></h3>
                    <p class="mb-0">Total Revenue</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3>৳<?= number_format($test['price'] ?? 0) ?></h3>
                    <p class="mb-0">Current Price</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Test Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Test Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6><i class="fas fa-barcode me-2"></i>Test Code</h6>
                                    <span class="badge bg-primary fs-6"><?= htmlspecialchars($test['code'] ?? 'N/A') ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6><i class="fas fa-folder me-2"></i>Category</h6>
                                    <span class="badge bg-info fs-6"><?= htmlspecialchars($test['category'] ?? 'Uncategorized') ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6><i class="fas fa-vial me-2"></i>Sample Type</h6>
                                    <p class="mb-0"><?= htmlspecialchars($test['sample_type'] ?? 'Not specified') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h6><i class="fas fa-clock me-2"></i>Turnaround Time</h6>
                                    <p class="mb-0"><?= htmlspecialchars($test['turnaround_time'] ?? 'Not specified') ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($test['description'])): ?>
                            <div class="info-card">
                                <h6><i class="fas fa-align-left me-2"></i>Description</h6>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($test['description'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Orders -->
                <?php if (!empty($recent_orders)): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Recent Orders
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Patient Name</th>
                                            <th>Date</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <a href="/invoices/<?= $order['id'] ?>" class="text-decoration-none">
                                                        #<?= $order['id'] ?>
                                                    </a>
                                                </td>
                                                <td><?= htmlspecialchars($order['patient_name']) ?></td>
                                                <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                                <td>
                                                    <span class="fw-bold text-success">৳<?= number_format($order['price']) ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/invoices/create?test=<?= htmlspecialchars($test['code'] ?? '') ?>"
                               class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Invoice
                            </a>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="/tests/<?= htmlspecialchars($test['code'] ?? '') ?>/edit"
                                   class="btn btn-outline-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Test
                                </a>
                                <a href="/reports?test=<?= htmlspecialchars($test['code'] ?? '') ?>"
                                   class="btn btn-outline-info">
                                    <i class="fas fa-chart-bar me-2"></i>View Reports
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Test Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>Test Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Status:</span>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Created:</span>
                            <span><?= date('M j, Y', strtotime($test['created_at'] ?? 'now')) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Last Updated:</span>
                            <span><?= date('M j, Y', strtotime($test['updated_at'] ?? $test['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Usage Chart -->
                <?php if (($usage_stats['total_orders'] ?? 0) > 0): ?>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Usage Overview
                            </h6>
                        </div>
                        <div class="card-body" style="height: 300px;">
                            <canvas id="usageChart" style="max-height: 280px !important;"></canvas>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
            <div class="toast show" role="alert">
                <div class="toast-header">
                    <strong class="me-auto">
                        <?= $_SESSION['flash_type'] === 'success' ? 'Success' : 'Error' ?>
                    </strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <?= htmlspecialchars($_SESSION['flash_message']) ?>
                </div>
            </div>
        </div>
        <?php
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php if (($usage_stats['total_orders'] ?? 0) > 0): ?>
    <script>
        // Usage Chart
        const ctx = document.getElementById('usageChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Orders', 'Patients', 'Revenue (৳)'],
                datasets: [{
                    data: [
                        <?= $usage_stats['total_orders'] ?? 0 ?>,
                        <?= $usage_stats['unique_patients'] ?? 0 ?>,
                        <?= ($usage_stats['total_revenue'] ?? 0) / 100 ?>
                    ],
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(67, 233, 123, 0.8)'
                    ],
                    borderWidth: 0
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
    </script>
    <?php endif; ?>
</body>
</html>