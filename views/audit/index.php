<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - SEL Diagnostic Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            margin-bottom: 2rem;
        }

        .card-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 1.5rem;
        }

        .filters-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-login {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .badge-create {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }

        .badge-update {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .badge-delete {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .badge-view {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }

        .log-details {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .log-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .icon-login { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .icon-create { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
        .icon-update { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
        .icon-delete { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .icon-view { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }

        .json-viewer {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            max-height: 100px;
            overflow-y: auto;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: #6c757d;
            margin: 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-clipboard-list me-3"></i>
                        Audit Logs
                    </h1>
                    <p class="mb-0 opacity-75">System activity and security tracking</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/dashboard" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card">
                <h3><?php echo number_format($totalCount); ?></h3>
                <p>Total Log Entries</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($types); ?></h3>
                <p>Action Types</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($users); ?></h3>
                <p>Active Users</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $totalPages; ?></h3>
                <p>Total Pages</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="/audit" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Search action or details..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Action Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Actions</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?php echo htmlspecialchars($t); ?>"
                                    <?php echo $type === $t ? 'selected' : ''; ?>>
                                <?php echo ucfirst(htmlspecialchars($t)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select name="user" class="form-select">
                        <option value="">All Users</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>"
                                    <?php echo $user == $u['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Audit Logs Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Activity Log
                    <?php if ($page > 1): ?>
                        <span class="badge bg-light text-dark ms-2">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($logs)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No audit logs found</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;"></th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <?php
                                    $iconClass = 'icon-' . strtolower($log['action']);
                                    $badgeClass = 'badge-' . strtolower($log['action']);
                                    $icon = match($log['action']) {
                                        'login' => 'fa-sign-in-alt',
                                        'create' => 'fa-plus-circle',
                                        'update' => 'fa-edit',
                                        'delete' => 'fa-trash',
                                        'view' => 'fa-eye',
                                        default => 'fa-circle'
                                    };
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="log-icon <?php echo $iconClass; ?>">
                                                <i class="fas <?php echo $icon; ?>"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo strtoupper($log['action']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></strong>
                                            <br>
                                            <small class="text-muted">ID: <?php echo $log['user_id'] ?? 'N/A'; ?></small>
                                        </td>
                                        <td>
                                            <div class="log-details">
                                                <?php if (!empty($log['table_name'])): ?>
                                                    <strong><?php echo htmlspecialchars($log['table_name']); ?></strong>
                                                    <?php if (!empty($log['record_id'])): ?>
                                                        #<?php echo htmlspecialchars($log['record_id']); ?>
                                                    <?php endif; ?>
                                                    <br>
                                                <?php endif; ?>

                                                <?php if (!empty($log['new_values'])): ?>
                                                    <details>
                                                        <summary style="cursor: pointer; color: #667eea;">View Changes</summary>
                                                        <div class="json-viewer mt-2">
                                                            <?php
                                                            $newValues = json_decode($log['new_values'], true);
                                                            if ($newValues) {
                                                                foreach ($newValues as $key => $value) {
                                                                    echo "<div><strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars($value) . "</div>";
                                                                }
                                                            } else {
                                                                echo htmlspecialchars($log['new_values']);
                                                            }
                                                            ?>
                                                        </div>
                                                    </details>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <code><?php echo htmlspecialchars($log['ip_address'] ?? 'N/A'); ?></code>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($log['created_at'])); ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo date('h:i A', strtotime($log['created_at'])); ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="p-3 border-top">
                            <nav>
                                <ul class="pagination justify-content-center mb-0">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type); ?>&user=<?php echo urlencode($user); ?>">
                                                Previous
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type); ?>&user=<?php echo urlencode($user); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&type=<?php echo urlencode($type); ?>&user=<?php echo urlencode($user); ?>">
                                                Next
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
