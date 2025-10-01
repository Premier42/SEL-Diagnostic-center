<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= str_pad($invoice['id'], 4, '0', STR_PAD_LEFT) ?> - <?= config('app.name') ?></title>
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
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        .invoice-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .invoice-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 2rem;
        }

        .invoice-body {
            padding: 2rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .status-paid { background: rgba(67, 233, 123, 0.2); color: var(--success-color); }
        .status-partial { background: rgba(254, 202, 87, 0.2); color: var(--warning-color); }
        .status-pending { background: rgba(245, 87, 108, 0.2); color: var(--danger-color); }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-section h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .table-modern {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .table-modern th {
            background: #f8fafc;
            border: none;
            font-weight: 600;
            color: var(--dark-color);
        }

        .table-modern td {
            border-color: #f1f5f9;
            vertical-align: middle;
        }

        .summary-card {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 15px;
            padding: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .btn-action {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        @media print {
            .sidebar, .action-buttons, .no-print {
                display: none !important;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .invoice-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 100%;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar no-print">
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
            <a href="/logout" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Action Buttons -->
        <div class="action-buttons mb-4 no-print">
            <a href="/invoices" class="btn btn-outline-primary btn-action">
                <i class="fas fa-arrow-left me-2"></i>Back to Invoices
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-action">
                <i class="fas fa-print me-2"></i>Print Invoice
            </button>
            <button onclick="generatePDF()" class="btn btn-success btn-action">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </button>
            <?php if ($invoice['payment_status'] !== 'paid'): ?>
                <button onclick="updatePayment()" class="btn btn-warning btn-action">
                    <i class="fas fa-credit-card me-2"></i>Update Payment
                </button>
            <?php endif; ?>
        </div>

        <!-- Invoice -->
        <div class="invoice-card">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3 class="mb-0">
                            <i class="fas fa-flask me-2"></i>
                            <?= config('app.name') ?>
                        </h3>
                        <p class="mb-0 opacity-75">Professional Laboratory Services</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h4 class="mb-2">Invoice #<?= str_pad($invoice['id'], 4, '0', STR_PAD_LEFT) ?></h4>
                        <span class="status-badge status-<?= $invoice['payment_status'] ?>">
                            <?= ucfirst($invoice['payment_status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="invoice-body">
                <!-- Information Grid -->
                <div class="info-grid">
                    <!-- Patient Information -->
                    <div class="info-section">
                        <h6><i class="fas fa-user me-2"></i>Patient Information</h6>
                        <p class="mb-2"><strong><?= htmlspecialchars($invoice['patient_name']) ?></strong></p>
                        <?php if ($invoice['patient_age']): ?>
                            <p class="mb-1">Age: <?= $invoice['patient_age'] ?> years</p>
                        <?php endif; ?>
                        <?php if ($invoice['patient_gender']): ?>
                            <p class="mb-1">Gender: <?= $invoice['patient_gender'] ?></p>
                        <?php endif; ?>
                        <?php if ($invoice['patient_phone']): ?>
                            <p class="mb-1">Phone: <?= htmlspecialchars($invoice['patient_phone']) ?></p>
                        <?php endif; ?>
                        <?php if ($invoice['patient_email']): ?>
                            <p class="mb-1">Email: <?= htmlspecialchars($invoice['patient_email']) ?></p>
                        <?php endif; ?>
                        <?php if ($invoice['patient_address']): ?>
                            <p class="mb-1">Address: <?= htmlspecialchars($invoice['patient_address']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Doctor Information -->
                    <div class="info-section">
                        <h6><i class="fas fa-user-md me-2"></i>Referring Doctor</h6>
                        <?php if ($invoice['doctor_name']): ?>
                            <p class="mb-2"><strong><?= htmlspecialchars($invoice['doctor_name']) ?></strong></p>
                            <?php if ($invoice['qualifications']): ?>
                                <p class="mb-1"><?= htmlspecialchars($invoice['qualifications']) ?></p>
                            <?php endif; ?>
                            <?php if ($invoice['workplace']): ?>
                                <p class="mb-1"><?= htmlspecialchars($invoice['workplace']) ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted">No referring doctor specified</p>
                        <?php endif; ?>
                    </div>

                    <!-- Invoice Details -->
                    <div class="info-section">
                        <h6><i class="fas fa-calendar me-2"></i>Invoice Details</h6>
                        <p class="mb-1">Date: <?= date('M j, Y', strtotime($invoice['created_at'])) ?></p>
                        <p class="mb-1">Time: <?= date('g:i A', strtotime($invoice['created_at'])) ?></p>
                        <?php if ($invoice['payment_method']): ?>
                            <p class="mb-1">Payment Method: <?= ucwords(str_replace('_', ' ', $invoice['payment_method'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Test Details -->
                <div class="row">
                    <div class="col-lg-8">
                        <h6 class="mb-3"><i class="fas fa-vial me-2"></i>Test Details</h6>
                        <div class="table-modern">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Test Code</th>
                                        <th>Test Name</th>
                                        <th class="text-end">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoice_tests as $test): ?>
                                        <tr>
                                            <td><code><?= htmlspecialchars($test['test_code']) ?></code></td>
                                            <td><?= htmlspecialchars($test['test_name']) ?></td>
                                            <td class="text-end">৳<?= number_format($test['price'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($invoice['notes']): ?>
                            <div class="mt-4">
                                <h6><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                                <div class="p-3 bg-light rounded">
                                    <?= nl2br(htmlspecialchars($invoice['notes'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Summary -->
                    <div class="col-lg-4">
                        <h6 class="mb-3"><i class="fas fa-calculator me-2"></i>Payment Summary</h6>
                        <div class="summary-card">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span>৳<?= number_format($invoice['total_amount'] + $invoice['discount_amount'], 2) ?></span>
                            </div>

                            <?php if ($invoice['discount_amount'] > 0): ?>
                                <div class="summary-row">
                                    <span>Discount:</span>
                                    <span class="text-success">-৳<?= number_format($invoice['discount_amount'], 2) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="summary-row">
                                <strong>Total Amount:</strong>
                                <strong>৳<?= number_format($invoice['total_amount'], 2) ?></strong>
                            </div>

                            <div class="summary-row">
                                <span>Amount Paid:</span>
                                <span class="text-success">৳<?= number_format($invoice['amount_paid'], 2) ?></span>
                            </div>

                            <div class="summary-row">
                                <strong>Balance Due:</strong>
                                <strong class="<?= ($invoice['total_amount'] - $invoice['amount_paid']) <= 0 ? 'text-success' : 'text-danger' ?>">
                                    ৳<?= number_format($invoice['total_amount'] - $invoice['amount_paid'], 2) ?>
                                </strong>
                            </div>
                        </div>

                        <!-- Payment Status Card -->
                        <div class="mt-3 p-3 rounded <?= $invoice['payment_status'] === 'paid' ? 'bg-success text-white' : ($invoice['payment_status'] === 'partial' ? 'bg-warning' : 'bg-danger text-white') ?>">
                            <div class="text-center">
                                <i class="fas <?= $invoice['payment_status'] === 'paid' ? 'fa-check-circle' : ($invoice['payment_status'] === 'partial' ? 'fa-exclamation-triangle' : 'fa-clock') ?> fa-2x mb-2"></i>
                                <h6 class="mb-0">
                                    <?= $invoice['payment_status'] === 'paid' ? 'Payment Complete' : ($invoice['payment_status'] === 'partial' ? 'Partial Payment' : 'Payment Pending') ?>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4 pt-4 border-top">
                    <p class="text-muted mb-2">Thank you for choosing <?= config('app.name') ?></p>
                    <p class="text-muted small mb-0">
                        This is a computer-generated invoice. No signature required.
                        <br>
                        Generated on <?= date('M j, Y \a\t g:i A') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Update Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Additional Payment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number"
                                       class="form-control"
                                       id="additionalPayment"
                                       min="0"
                                       step="0.01"
                                       max="<?= $invoice['total_amount'] - $invoice['amount_paid'] ?>">
                            </div>
                            <div class="form-text">
                                Remaining balance: ৳<?= number_format($invoice['total_amount'] - $invoice['amount_paid'], 2) ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" id="paymentMethod">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_banking">Mobile Banking</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitPayment()">Update Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updatePayment() {
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }

        function submitPayment() {
            const additionalPayment = parseFloat(document.getElementById('additionalPayment').value) || 0;
            const currentPaid = <?= $invoice['amount_paid'] ?>;
            const newTotal = currentPaid + additionalPayment;
            const grandTotal = <?= $invoice['total_amount'] - $invoice['discount_amount'] ?>;

            if (additionalPayment <= 0) {
                alert('Please enter a valid payment amount');
                return;
            }

            if (newTotal > grandTotal) {
                alert('Payment amount cannot exceed the total invoice amount');
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/invoices/<?= $invoice['id'] ?>/update-payment';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?= generate_csrf() ?>';
            form.appendChild(csrfInput);

            const amountInput = document.createElement('input');
            amountInput.type = 'hidden';
            amountInput.name = 'amount_paid';
            amountInput.value = newTotal;
            form.appendChild(amountInput);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = 'payment_method';
            methodInput.value = document.getElementById('paymentMethod').value;
            form.appendChild(methodInput);

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'payment_status';
            statusInput.value = newTotal >= grandTotal ? 'paid' : 'partial';
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        }

        function generatePDF() {
            window.open(`/invoices/<?= $invoice['id'] ?>/pdf`, '_blank');
        }
    </script>
</body>
</html>