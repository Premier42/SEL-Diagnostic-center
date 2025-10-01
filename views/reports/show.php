<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report #<?= $report['id'] ?> - <?= config('app.name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <h2><i class="fas fa-file-medical me-2"></i>Test Report #<?= $report['id'] ?></h2>
            <div>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <a href="/reports" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?= config('app.name') ?></h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Patient Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="150">Name:</th>
                                <td><?= htmlspecialchars($report['patient_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Age:</th>
                                <td><?= htmlspecialchars($report['patient_age'] ?? 'N/A') ?> years</td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td><?= htmlspecialchars($report['patient_gender'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td><?= htmlspecialchars($report['patient_phone'] ?? 'N/A') ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Test Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th width="150">Test Name:</th>
                                <td><?= htmlspecialchars($report['test_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><?= htmlspecialchars($report['category']) ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-<?= $report['status'] === 'verified' ? 'success' : 'warning' ?>">
                                        <?= ucfirst(str_replace('_', ' ', $report['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Date:</th>
                                <td><?= date('M j, Y g:i A', strtotime($report['created_at'])) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if (!empty($results)): ?>
                    <h5 class="mb-3">Test Results</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                    <th>Unit</th>
                                    <th>Normal Range</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $result): ?>
                                    <tr class="<?= $result['is_abnormal'] ? 'table-warning' : '' ?>">
                                        <td><?= htmlspecialchars($result['parameter_name']) ?></td>
                                        <td><strong><?= htmlspecialchars($result['value']) ?></strong></td>
                                        <td><?= htmlspecialchars($result['unit'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($result['normal_range'] ?? '') ?></td>
                                        <td>
                                            <?php if ($result['is_abnormal']): ?>
                                                <span class="badge bg-danger">Abnormal</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Normal</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No test results available yet
                    </div>
                <?php endif; ?>

                <?php if (!empty($report['notes'])): ?>
                    <div class="mt-4">
                        <h5>Notes</h5>
                        <p><?= nl2br(htmlspecialchars($report['notes'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <small class="text-muted">
                        <?php if ($report['technician_name']): ?>
                            Processed by: <?= htmlspecialchars($report['technician_name']) ?><br>
                        <?php endif; ?>
                        Report generated on: <?= date('F j, Y g:i A') ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>