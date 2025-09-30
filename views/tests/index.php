<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tests Management - SEL Diagnostic Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        .btn-success {
            background: var(--success-gradient);
            border: none;
            border-radius: 10px;
        }

        .btn-warning {
            background: var(--warning-gradient);
            border: none;
            border-radius: 10px;
            color: #333;
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

        .badge {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .search-box:focus {
            background: white;
            border-color: #667eea;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
        }

        .stats-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .category-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-right: 0.5rem;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-vials me-3"></i>
                        Tests Management
                    </h1>
                    <p class="mb-0 opacity-75">Manage laboratory tests and categories</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/dashboard" class="btn btn-light me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/tests/create" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add New Test
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container fade-in">
        <!-- Statistics Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3 class="mb-1"><?= $stats['total'] ?? 0 ?></h3>
                    <p class="mb-0">Total Tests</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3 class="mb-1"><?= $stats['categories'] ?? 0 ?></h3>
                    <p class="mb-0">Categories</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3 class="mb-1">৳<?= number_format($stats['avg_price'] ?? 0, 0) ?></h3>
                    <p class="mb-0">Average Price</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <h3 class="mb-1">৳<?= number_format($stats['max_price'] ?? 0, 0) ?></h3>
                    <p class="mb-0">Highest Price</p>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control search-box"
                               placeholder="Search tests..." value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php foreach ($categories ?? [] as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>"
                                        <?= ($category ?? '') === $cat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="/tests" class="btn btn-outline-secondary ms-2">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tests by Category -->
        <?php if (!empty($tests_by_category)): ?>
            <?php foreach ($tests_by_category as $categoryName => $categoryTests): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-folder-open me-2"></i>
                            <?= htmlspecialchars($categoryName) ?>
                            <span class="badge bg-light text-dark ms-2"><?= count($categoryTests) ?> tests</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Test Code</th>
                                        <th>Test Name</th>
                                        <th>Price</th>
                                        <th>Sample Type</th>
                                        <th>Turnaround Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categoryTests as $test): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary"><?= htmlspecialchars($test['code']) ?></span>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($test['name']) ?></strong>
                                                <?php if (!empty($test['description'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($test['description']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">৳<?= number_format($test['price'], 0) ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($test['sample_type'])): ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($test['sample_type']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not specified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($test['turnaround_time'])): ?>
                                                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($test['turnaround_time']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not specified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="/tests/<?= htmlspecialchars($test['code']) ?>"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                                    <a href="/tests/<?= htmlspecialchars($test['code']) ?>/edit"
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-vials fa-3x text-muted mb-3"></i>
                    <h4>No Tests Found</h4>
                    <p class="text-muted">No tests match your search criteria.</p>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="/tests/create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add First Test
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
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
</body>
</html>