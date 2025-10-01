<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice - <?= config('app.name') ?></title>
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
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 2rem;
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        }

        .test-selection {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem;
        }

        .test-category {
            margin-bottom: 1.5rem;
        }

        .test-category-title {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .test-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .test-item:hover {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.05);
        }

        .test-item.selected {
            border-color: var(--primary-color);
            background: rgba(102, 126, 234, 0.1);
        }

        .test-item input[type="checkbox"] {
            margin-right: 0.75rem;
        }

        .invoice-summary {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 15px;
            padding: 1.5rem;
            position: sticky;
            top: 2rem;
        }

        .summary-row {
            display: flex;
            justify-content: between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .alert {
            border-radius: 10px;
            border: none;
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
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
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
            <a href="/logout" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Create New Invoice</h2>
                <p class="text-muted">Add patient details and select tests</p>
            </div>
            <a href="/invoices" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Invoices
            </a>
        </div>

        <?php if ($flash = $this->getFlashMessage()): ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?> alert-dismissible fade show">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="/invoices/store" id="invoiceForm">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Patient Information -->
                    <div class="form-card">
                        <h5 class="mb-3">
                            <i class="fas fa-user me-2 text-primary"></i>
                            Patient Information
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Patient Name *</label>
                                <input type="text"
                                       class="form-control <?= !empty(errors('patient_name')) ? 'is-invalid' : '' ?>"
                                       name="patient_name"
                                       value="<?= htmlspecialchars(old('patient_name')) ?>"
                                       required>
                                <?php if (!empty(errors('patient_name'))): ?>
                                    <div class="invalid-feedback"><?= errors('patient_name')[0] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Age</label>
                                <input type="number"
                                       class="form-control"
                                       name="patient_age"
                                       value="<?= htmlspecialchars(old('patient_age')) ?>"
                                       min="0" max="120">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="patient_gender">
                                    <option value="">Select</option>
                                    <option value="Male" <?= old('patient_gender') === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= old('patient_gender') === 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= old('patient_gender') === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel"
                                       class="form-control <?= !empty(errors('patient_phone')) ? 'is-invalid' : '' ?>"
                                       name="patient_phone"
                                       value="<?= htmlspecialchars(old('patient_phone')) ?>"
                                       placeholder="01XXXXXXXXX"
                                       required>
                                <small class="text-muted">Enter 11-digit number (e.g., 01819976364). +880 will be added automatically.</small>
                                <?php if (!empty(errors('patient_phone'))): ?>
                                    <div class="invalid-feedback"><?= errors('patient_phone')[0] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email"
                                       class="form-control"
                                       name="patient_email"
                                       value="<?= htmlspecialchars(old('patient_email')) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control"
                                      name="patient_address"
                                      rows="2"><?= htmlspecialchars(old('patient_address')) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Referring Doctor</label>
                            <select class="form-select" name="doctor_id">
                                <option value="">Select Doctor (Optional)</option>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor['id'] ?>" <?= old('doctor_id') == $doctor['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($doctor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Test Selection -->
                    <div class="form-card">
                        <h5 class="mb-3">
                            <i class="fas fa-vial me-2 text-primary"></i>
                            Select Tests *
                        </h5>

                        <?php if (!empty(errors('tests'))): ?>
                            <div class="alert alert-danger"><?= errors('tests')[0] ?></div>
                        <?php endif; ?>

                        <div class="test-selection">
                            <?php foreach ($tests_by_category as $category => $categoryTests): ?>
                                <div class="test-category">
                                    <div class="test-category-title">
                                        <?= htmlspecialchars($category) ?>
                                    </div>

                                    <?php foreach ($categoryTests as $test): ?>
                                        <div class="test-item" onclick="toggleTest('<?= $test['code'] ?>')">
                                            <input type="checkbox"
                                                   name="tests[]"
                                                   value="<?= $test['code'] ?>"
                                                   id="test_<?= $test['code'] ?>"
                                                   data-price="<?= $test['price'] ?>"
                                                   onchange="updateSummary()">
                                            <label for="test_<?= $test['code'] ?>" class="mb-0">
                                                <strong><?= htmlspecialchars($test['name']) ?></strong>
                                                <span class="float-end text-primary fw-bold">৳<?= number_format($test['price'], 2) ?></span>
                                                <br>
                                                <small class="text-muted">Code: <?= $test['code'] ?></small>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="form-card">
                        <h5 class="mb-3">
                            <i class="fas fa-credit-card me-2 text-primary"></i>
                            Payment Information
                        </h5>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discount Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number"
                                           class="form-control"
                                           name="discount_amount"
                                           value="<?= htmlspecialchars(old('discount_amount', '0')) ?>"
                                           min="0"
                                           step="0.01"
                                           onchange="updateSummary()">
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Amount Paid</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number"
                                           class="form-control"
                                           name="amount_paid"
                                           value="<?= htmlspecialchars(old('amount_paid', '0')) ?>"
                                           min="0"
                                           step="0.01"
                                           onchange="updateSummary()">
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="payment_method">
                                    <option value="">Select Method</option>
                                    <option value="cash" <?= old('payment_method') === 'cash' ? 'selected' : '' ?>>Cash</option>
                                    <option value="card" <?= old('payment_method') === 'card' ? 'selected' : '' ?>>Card</option>
                                    <option value="bank_transfer" <?= old('payment_method') === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                    <option value="mobile_banking" <?= old('payment_method') === 'mobile_banking' ? 'selected' : '' ?>>Mobile Banking</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control"
                                      name="notes"
                                      rows="3"
                                      placeholder="Additional notes or instructions..."><?= htmlspecialchars(old('notes')) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Invoice Summary -->
                <div class="col-lg-4">
                    <div class="invoice-summary">
                        <h5 class="mb-3">
                            <i class="fas fa-calculator me-2"></i>
                            Invoice Summary
                        </h5>

                        <div id="selectedTests">
                            <p class="text-muted text-center">No tests selected</p>
                        </div>

                        <hr>

                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">৳0.00</span>
                        </div>

                        <div class="summary-row">
                            <span>Discount:</span>
                            <span id="discount">৳0.00</span>
                        </div>

                        <div class="summary-row">
                            <strong>Total Amount:</strong>
                            <strong id="total">৳0.00</strong>
                        </div>

                        <div class="summary-row">
                            <span>Amount Paid:</span>
                            <span id="paid">৳0.00</span>
                        </div>

                        <div class="summary-row">
                            <strong>Balance Due:</strong>
                            <strong id="balance" class="text-danger">৳0.00</strong>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-save me-2"></i>
                            Create Invoice
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/phone-formatter.js"></script>
    <script>
        function toggleTest(testCode) {
            const checkbox = document.getElementById('test_' + testCode);
            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change'));
        }

        function updateSummary() {
            const checkboxes = document.querySelectorAll('input[name="tests[]"]:checked');
            const discountInput = document.querySelector('input[name="discount_amount"]');
            const paidInput = document.querySelector('input[name="amount_paid"]');

            let subtotal = 0;
            let selectedTestsHtml = '';

            checkboxes.forEach(checkbox => {
                const price = parseFloat(checkbox.dataset.price);
                subtotal += price;

                const testItem = checkbox.closest('.test-item');
                const testName = testItem.querySelector('strong').textContent;

                selectedTestsHtml += `
                    <div class="d-flex justify-content-between mb-2">
                        <span>${testName}</span>
                        <span>৳${price.toFixed(2)}</span>
                    </div>
                `;
            });

            if (selectedTestsHtml === '') {
                selectedTestsHtml = '<p class="text-muted text-center">No tests selected</p>';
            }

            const discount = parseFloat(discountInput.value) || 0;
            const paid = parseFloat(paidInput.value) || 0;
            const total = subtotal - discount;
            const balance = total - paid;

            document.getElementById('selectedTests').innerHTML = selectedTestsHtml;
            document.getElementById('subtotal').textContent = '৳' + subtotal.toFixed(2);
            document.getElementById('discount').textContent = '৳' + discount.toFixed(2);
            document.getElementById('total').textContent = '৳' + total.toFixed(2);
            document.getElementById('paid').textContent = '৳' + paid.toFixed(2);
            document.getElementById('balance').textContent = '৳' + balance.toFixed(2);

            // Update balance color
            const balanceElement = document.getElementById('balance');
            if (balance <= 0) {
                balanceElement.className = 'text-success';
            } else {
                balanceElement.className = 'text-danger';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSummary();
        });
    </script>
</body>
</html>