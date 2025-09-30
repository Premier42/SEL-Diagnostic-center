<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors Management - SEL Diagnostic Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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

        .doctor-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 1rem;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
    </style>
</head>
<body>
    <div class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-user-md me-3"></i>
                        Doctors Management
                    </h1>
                    <p class="mb-0 opacity-75">Manage referring physicians</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/dashboard" class="btn btn-light me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/doctors/create" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Add New Doctor
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container fade-in">
        <!-- Search and Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control search-box"
                               placeholder="Search doctors by name, specialization, or workplace..."
                               value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="specialization" class="form-select">
                            <option value="">All Specializations</option>
                            <option value="Cardiology" <?= ($specialization ?? '') === 'Cardiology' ? 'selected' : '' ?>>Cardiology</option>
                            <option value="Dermatology" <?= ($specialization ?? '') === 'Dermatology' ? 'selected' : '' ?>>Dermatology</option>
                            <option value="Endocrinology" <?= ($specialization ?? '') === 'Endocrinology' ? 'selected' : '' ?>>Endocrinology</option>
                            <option value="Gastroenterology" <?= ($specialization ?? '') === 'Gastroenterology' ? 'selected' : '' ?>>Gastroenterology</option>
                            <option value="General Medicine" <?= ($specialization ?? '') === 'General Medicine' ? 'selected' : '' ?>>General Medicine</option>
                            <option value="Nephrology" <?= ($specialization ?? '') === 'Nephrology' ? 'selected' : '' ?>>Nephrology</option>
                            <option value="Neurology" <?= ($specialization ?? '') === 'Neurology' ? 'selected' : '' ?>>Neurology</option>
                            <option value="Oncology" <?= ($specialization ?? '') === 'Oncology' ? 'selected' : '' ?>>Oncology</option>
                            <option value="Orthopedics" <?= ($specialization ?? '') === 'Orthopedics' ? 'selected' : '' ?>>Orthopedics</option>
                            <option value="Pathology" <?= ($specialization ?? '') === 'Pathology' ? 'selected' : '' ?>>Pathology</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="/doctors" class="btn btn-outline-secondary ms-2">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Doctors Grid -->
        <?php if (!empty($doctors)): ?>
            <div class="row">
                <?php foreach ($doctors as $doctor): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="doctor-card card h-100">
                            <div class="card-body text-center">
                                <div class="doctor-avatar">
                                    <i class="fas fa-user-md"></i>
                                </div>

                                <h5 class="card-title mb-1">
                                    <?= htmlspecialchars($doctor['name']) ?>
                                </h5>

                                <?php if (!empty($doctor['qualifications'])): ?>
                                    <p class="text-muted small mb-2">
                                        <?= htmlspecialchars($doctor['qualifications']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($doctor['specialization'])): ?>
                                    <span class="badge bg-primary mb-2">
                                        <?= htmlspecialchars($doctor['specialization']) ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (!empty($doctor['workplace'])): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-hospital me-1"></i>
                                        <?= htmlspecialchars($doctor['workplace']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($doctor['phone'])): ?>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-phone me-1"></i>
                                        <?= htmlspecialchars($doctor['phone']) ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($doctor['email'])): ?>
                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-envelope me-1"></i>
                                        <?= htmlspecialchars($doctor['email']) ?>
                                    </p>
                                <?php endif; ?>

                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/invoices?doctor=<?= $doctor['id'] ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    <a href="/doctors/<?= $doctor['id'] ?>"
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <a href="/doctors/<?= $doctor['id'] ?>/edit"
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 text-center">
                                <small class="text-muted">
                                    Status:
                                    <?php if ($doctor['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <nav aria-label="Doctors pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($page ?? 1) == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($specialization) ? '&specialization=' . urlencode($specialization) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                    <h4>No Doctors Found</h4>
                    <p class="text-muted">No doctors match your search criteria.</p>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="/doctors/create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add First Doctor
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