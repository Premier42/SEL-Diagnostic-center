<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Management - <?= config('app.name') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-card.primary { border-left-color: #667eea; }
        .stat-card.success { border-left-color: #43e97b; }
        .stat-card.danger { border-left-color: #f5576c; }
        .stat-card.info { border-left-color: #4facfe; }

        .sms-log {
            border-left: 3px solid;
            transition: all 0.2s;
        }
        .sms-log:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .sms-log.sent { border-left-color: #43e97b; }
        .sms-log.failed { border-left-color: #f5576c; }
        .sms-log.pending { border-left-color: #feca57; }
        .sms-log.delivered { border-left-color: #667eea; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-sms me-2"></i>SMS Management</h2>
                <p class="text-muted mb-0">Send and manage SMS notifications</p>
            </div>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendSmsModal">
                    <i class="fas fa-paper-plane me-2"></i>Send SMS
                </button>
                <a href="/dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Sent</h6>
                                <h3 class="mb-0"><?= number_format($stats['total_sent'] ?? 0) ?></h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-paper-plane fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Successful</h6>
                                <h3 class="mb-0"><?= number_format($stats['successful'] ?? 0) ?></h3>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Failed</h6>
                                <h3 class="mb-0"><?= number_format($stats['failed'] ?? 0) ?></h3>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stat-card info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Today</h6>
                                <h3 class="mb-0"><?= number_format($stats['today_count'] ?? 0) ?></h3>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SMS Templates -->
        <?php if (!empty($templates)): ?>
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>SMS Templates</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($templates as $template): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($template['name']) ?></h6>
                                <p class="card-text text-muted small mb-2"><?= htmlspecialchars($template['description'] ?? '') ?></p>
                                <code class="d-block p-2 bg-light rounded small"><?= htmlspecialchars($template['message_template']) ?></code>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent SMS Logs -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent SMS Logs</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($sms_logs)): ?>
                    <?php foreach ($sms_logs as $log): ?>
                    <div class="sms-log <?= $log['status'] ?> card mb-2">
                        <div class="card-body py-2">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong><?= htmlspecialchars($log['recipient_name'] ?? 'N/A') ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($log['recipient_phone']) ?></small>
                                </div>
                                <div class="col-md-5">
                                    <small><?= htmlspecialchars(substr($log['message'], 0, 100)) ?><?= strlen($log['message']) > 100 ? '...' : '' ?></small>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="badge bg-<?=
                                        $log['status'] === 'sent' || $log['status'] === 'delivered' ? 'success' :
                                        ($log['status'] === 'failed' ? 'danger' : 'warning')
                                    ?>">
                                        <?= ucfirst($log['status']) ?>
                                    </span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <small class="text-muted">
                                        <?= date('M j, g:i A', strtotime($log['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No SMS logs found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Send SMS Modal -->
    <div class="modal fade" id="sendSmsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-paper-plane me-2"></i>Send SMS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="sendSmsForm">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Using TextBelt free tier (1 SMS/day per number for testing)
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="text" name="phone" class="form-control" placeholder="+8801XXXXXXXXX" required>
                            <small class="text-muted">Format: +8801XXXXXXXXX or 01XXXXXXXXX</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" class="form-control" rows="4" maxlength="160" required></textarea>
                            <small class="text-muted">Maximum 160 characters</small>
                        </div>

                        <?= csrf_field() ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send SMS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sendSmsForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';

            try {
                const response = await fetch('/sms/send', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('SMS sent successfully!');
                    location.reload();
                } else {
                    alert('Failed to send SMS: ' + (result.error || 'Unknown error'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send SMS';
                }
            } catch (error) {
                alert('Error: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send SMS';
            }
        });
    </script>
</body>
</html>