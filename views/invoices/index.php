<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management - <?= config('app.name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #764ba2;
            --success-color: #43e97b;
            --danger-color: #f5576c;
            --warning-color: #feca57;
            --info-color: #4facfe;
            --dark-color: #2d3748;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
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
        }

        .header-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid;
        }

        .stat-card.primary { border-left-color: var(--primary-color); }
        .stat-card.success { border-left-color: var(--success-color); }
        .stat-card.warning { border-left-color: var(--warning-color); }
        .stat-card.info { border-left-color: var(--info-color); }

        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .table-card {
            background: white;
            border-radius: 15px;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-card .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1.25rem 1.5rem;
            border: none;
            margin: 0;
        }

        .table-responsive {
            border-radius: 0 0 15px 15px;
        }

        .table {
            margin: 0;
        }

        .table th {
            border-top: none;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: var(--dark-color);
            padding: 1rem 1.25rem;
        }

        .table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: none;
            color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
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

            .stats-row {
                grid-template-columns: 1fr;
            }
        }

        .search-input {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
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
            <a href="/dashboard" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="/invoices" class="nav-link active">
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
            <a href="/users" class="nav-link">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="/logout" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Invoice Management</h2>
                    <p class="text-muted mb-0">Manage patient invoices and payments</p>
                </div>
                <a href="/invoices/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    New Invoice
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card primary">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= number_format($stats['total'] ?? 0) ?></h4>
                        <p class="text-muted mb-0">Total Invoices</p>
                    </div>
                    <i class="fas fa-file-invoice fa-2x text-primary"></i>
                </div>
            </div>
            <div class="stat-card success">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>৳<?= number_format($stats['amount_paid'] ?? 0, 2) ?></h4>
                        <p class="text-muted mb-0">Total Collected</p>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                </div>
            </div>
            <div class="stat-card warning">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= number_format($stats['pending_count'] ?? 0) ?></h4>
                        <p class="text-muted mb-0">Pending Payments</p>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
            </div>
            <div class="stat-card info">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= number_format($stats['paid_count'] ?? 0) ?></h4>
                        <p class="text-muted mb-0">Completed</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-info"></i>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text"
                           class="form-control search-input"
                           name="search"
                           placeholder="Search by patient name, phone, or invoice ID..."
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="partial" <?= $status === 'partial' ? 'selected' : '' ?>>Partial</option>
                        <option value="paid" <?= $status === 'paid' ? 'selected' : '' ?>>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="/invoices" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-undo me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Invoice Table -->
        <div class="table-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Invoices List
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($invoices)): ?>
                            <?php foreach ($invoices as $invoice): ?>
                                <tr>
                                    <td>
                                        <strong>#<?= str_pad($invoice['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars($invoice['patient_name']) ?></strong>
                                            <?php if ($invoice['patient_phone']): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($invoice['patient_phone']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($invoice['doctor_name'] ?? 'N/A') ?>
                                    </td>
                                    <td>
                                        <strong>৳<?= number_format($invoice['total_amount'], 2) ?></strong>
                                        <?php if ($invoice['discount_amount'] > 0): ?>
                                            <br><small class="text-muted">Discount: ৳<?= number_format($invoice['discount_amount'], 2) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong>৳<?= number_format($invoice['amount_paid'], 2) ?></strong>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = match($invoice['payment_status']) {
                                            'paid' => 'bg-success',
                                            'partial' => 'bg-warning',
                                            'pending' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= ucfirst($invoice['payment_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= date('M j, Y', strtotime($invoice['created_at'])) ?>
                                        <br><small class="text-muted"><?= date('g:i A', strtotime($invoice['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/invoices/<?= $invoice['id'] ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               title="View Invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-success"
                                                    onclick="printInvoice(<?= $invoice['id'] ?>)"
                                                    title="Print Invoice">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No invoices found</h5>
                                    <p class="text-muted">
                                        <?= !empty($search) ? 'Try adjusting your search criteria.' : 'Create your first invoice to get started.' ?>
                                    </p>
                                    <?php if (empty($search)): ?>
                                        <a href="/invoices/create" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Create First Invoice
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= $search ? "&search=" . urlencode($search) : '' ?><?= $status ? "&status=" . urlencode($status) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printInvoice(invoiceId) {
            window.open(`/invoices/${invoiceId}/print`, '_blank');
        }

        // Removed auto-refresh to prevent browser crashes
        // Users can manually refresh if needed
    </script>
</body>
</html>