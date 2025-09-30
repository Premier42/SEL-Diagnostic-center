<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Test - SEL Diagnostic Center</title>
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

        .form-control, .form-select {
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus, .form-select:focus {
            background: white;
            border-color: #667eea;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .preview-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-plus-circle me-3"></i>
                        Create New Test
                    </h1>
                    <p class="mb-0 opacity-75">Add a new laboratory test to the system</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="/tests" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Tests
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container fade-in">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-vial me-2"></i>Test Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/tests/store" id="testForm">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">
                                        <i class="fas fa-barcode me-1"></i>Test Code *
                                    </label>
                                    <input type="text" class="form-control <?= isset($_SESSION['errors']['code']) ? 'is-invalid' : '' ?>"
                                           id="code" name="code" value="<?= htmlspecialchars($_SESSION['old']['code'] ?? '') ?>"
                                           placeholder="e.g., CBC, LFT, RFT" required>
                                    <?php if (isset($_SESSION['errors']['code'])): ?>
                                        <div class="invalid-feedback">
                                            <?= implode('<br>', $_SESSION['errors']['code']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">
                                        <i class="fas fa-folder me-1"></i>Category *
                                    </label>
                                    <select class="form-select <?= isset($_SESSION['errors']['category']) ? 'is-invalid' : '' ?>"
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php
                                        $defaultCategories = [
                                            'Hematology', 'Biochemistry', 'Microbiology', 'Immunology',
                                            'Histopathology', 'Cytology', 'Molecular Biology', 'Toxicology',
                                            'Endocrinology', 'Cardiology', 'Oncology'
                                        ];
                                        foreach ($categories ?? $defaultCategories as $cat):
                                        ?>
                                            <option value="<?= htmlspecialchars($cat) ?>"
                                                    <?= ($_SESSION['old']['category'] ?? '') === $cat ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($_SESSION['errors']['category'])): ?>
                                        <div class="invalid-feedback">
                                            <?= implode('<br>', $_SESSION['errors']['category']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-tag me-1"></i>Test Name *
                                </label>
                                <input type="text" class="form-control <?= isset($_SESSION['errors']['name']) ? 'is-invalid' : '' ?>"
                                       id="name" name="name" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>"
                                       placeholder="Enter the full test name" required>
                                <?php if (isset($_SESSION['errors']['name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= implode('<br>', $_SESSION['errors']['name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>Description
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Brief description of the test (optional)"><?= htmlspecialchars($_SESSION['old']['description'] ?? '') ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">
                                        <i class="fas fa-money-bill me-1"></i>Price (৳) *
                                    </label>
                                    <input type="number" class="form-control <?= isset($_SESSION['errors']['price']) ? 'is-invalid' : '' ?>"
                                           id="price" name="price" value="<?= htmlspecialchars($_SESSION['old']['price'] ?? '') ?>"
                                           min="0" step="0.01" placeholder="0.00" required>
                                    <?php if (isset($_SESSION['errors']['price'])): ?>
                                        <div class="invalid-feedback">
                                            <?= implode('<br>', $_SESSION['errors']['price']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="sample_type" class="form-label">
                                        <i class="fas fa-vial me-1"></i>Sample Type
                                    </label>
                                    <select class="form-select" id="sample_type" name="sample_type">
                                        <option value="">Select Sample Type</option>
                                        <option value="Blood" <?= ($_SESSION['old']['sample_type'] ?? '') === 'Blood' ? 'selected' : '' ?>>Blood</option>
                                        <option value="Urine" <?= ($_SESSION['old']['sample_type'] ?? '') === 'Urine' ? 'selected' : '' ?>>Urine</option>
                                        <option value="Stool" <?= ($_SESSION['old']['sample_type'] ?? '') === 'Stool' ? 'selected' : '' ?>>Stool</option>
                                        <option value="Sputum" <?= ($_SESSION['old']['sample_type'] ?? '') === 'Sputum' ? 'selected' : '' ?>>Sputum</option>
                                        <option value="Tissue" <?= ($_SESSION['old']['sample_type'] ?? '') === 'Tissue' ? 'selected' : '' ?>>Tissue</option>
                                        <option value="Swab" <?= ($_SESSION['old']['sample_type'] ?? '') === 'Swab' ? 'selected' : '' ?>>Swab</option>
                                        <option value="Other" <?= ($_SESSION['old']['sample_type'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="turnaround_time" class="form-label">
                                        <i class="fas fa-clock me-1"></i>Turnaround Time
                                    </label>
                                    <select class="form-select" id="turnaround_time" name="turnaround_time">
                                        <option value="">Select Time</option>
                                        <option value="Same day" <?= ($_SESSION['old']['turnaround_time'] ?? '') === 'Same day' ? 'selected' : '' ?>>Same day</option>
                                        <option value="24 hours" <?= ($_SESSION['old']['turnaround_time'] ?? '') === '24 hours' ? 'selected' : '' ?>>24 hours</option>
                                        <option value="2-3 days" <?= ($_SESSION['old']['turnaround_time'] ?? '') === '2-3 days' ? 'selected' : '' ?>>2-3 days</option>
                                        <option value="1 week" <?= ($_SESSION['old']['turnaround_time'] ?? '') === '1 week' ? 'selected' : '' ?>>1 week</option>
                                        <option value="2 weeks" <?= ($_SESSION['old']['turnaround_time'] ?? '') === '2 weeks' ? 'selected' : '' ?>>2 weeks</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="/tests" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Create Test
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="preview-card">
                    <h5 class="mb-3">
                        <i class="fas fa-eye me-2"></i>Preview
                    </h5>
                    <div id="testPreview">
                        <div class="mb-2">
                            <strong>Code:</strong> <span id="previewCode">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Name:</strong> <span id="previewName">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Category:</strong> <span id="previewCategory">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Price:</strong> ৳<span id="previewPrice">0</span>
                        </div>
                        <div class="mb-2">
                            <strong>Sample:</strong> <span id="previewSample">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Turnaround:</strong> <span id="previewTurnaround">-</span>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle me-2"></i>Guidelines</h6>
                        <ul class="small mb-0">
                            <li>Test codes should be unique and descriptive</li>
                            <li>Use standard medical abbreviations when possible</li>
                            <li>Categories help organize tests for easier management</li>
                            <li>Price should reflect the actual cost of the test</li>
                            <li>Sample type helps lab technicians prepare properly</li>
                        </ul>
                    </div>
                </div>
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
    <script>
        // Real-time preview
        function updatePreview() {
            document.getElementById('previewCode').textContent = document.getElementById('code').value || '-';
            document.getElementById('previewName').textContent = document.getElementById('name').value || '-';
            document.getElementById('previewCategory').textContent = document.getElementById('category').value || '-';
            document.getElementById('previewPrice').textContent = document.getElementById('price').value || '0';
            document.getElementById('previewSample').textContent = document.getElementById('sample_type').value || '-';
            document.getElementById('previewTurnaround').textContent = document.getElementById('turnaround_time').value || '-';
        }

        // Add event listeners
        ['code', 'name', 'category', 'price', 'sample_type', 'turnaround_time'].forEach(id => {
            document.getElementById(id).addEventListener('input', updatePreview);
            document.getElementById(id).addEventListener('change', updatePreview);
        });

        // Auto-uppercase test code
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            updatePreview();
        });

        // Initial preview update
        updatePreview();
    </script>

    <?php
    // Clear old form data and errors
    unset($_SESSION['old'], $_SESSION['errors']);
    ?>
</body>
</html>