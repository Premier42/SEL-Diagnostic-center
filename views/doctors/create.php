<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Doctor - <?= config('app.name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-md me-2"></i>Add New Doctor</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['errors'])): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($_SESSION['errors'] as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                            <?php unset($_SESSION['errors']); ?>
                        <?php endif; ?>

                        <form method="POST" action="/doctors/create">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Qualifications</label>
                                <input type="text" name="qualifications" class="form-control" value="<?= old('qualifications') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" value="<?= old('specialization') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Workplace</label>
                                <input type="text" name="workplace" class="form-control" value="<?= old('workplace') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" value="<?= old('phone') ?>" placeholder="01XXXXXXXXX">
                                <small class="text-muted">Enter 11-digit number. +880 will be added automatically.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= old('email') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="3"><?= old('address') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">License Number</label>
                                <input type="text" name="license_number" class="form-control" value="<?= old('license_number') ?>">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/doctors" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Doctor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/phone-formatter.js"></script>
</body>
</html>
<?php clearOldInput(); ?>