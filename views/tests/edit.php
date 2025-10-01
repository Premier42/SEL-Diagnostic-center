<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Test - SEL Diagnostic Center</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .content-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Edit Test</h1>
            <a href="/tests" class="btn btn-secondary">← Back to Tests</a>
        </div>

        <div class="content-card">
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'success'; ?>">
                    <?php
                    echo $_SESSION['flash_message'];
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/tests/<?php echo htmlspecialchars($test['code']); ?>/update">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf(); ?>">
                <input type="hidden" name="code" value="<?php echo htmlspecialchars($test['code']); ?>">

                <div class="form-group">
                    <label>Test Code</label>
                    <input type="text" value="<?php echo htmlspecialchars($test['code']); ?>" disabled>
                    <small style="color: #666;">Test code cannot be changed</small>
                </div>

                <div class="form-group">
                    <label>Test Name *</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['old']['name'] ?? $test['name']); ?>" required>
                    <?php if (isset($_SESSION['errors']['name'])): ?>
                        <div class="error"><?php echo $_SESSION['errors']['name'][0]; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <?php
                            $predefinedCategories = ['Hematology', 'Biochemistry', 'Endocrinology', 'Immunology', 'Microbiology', 'Clinical Pathology'];
                            $allCategories = array_unique(array_merge($predefinedCategories, $categories));
                            $selectedCategory = $_SESSION['old']['category'] ?? $test['category'];
                            foreach ($allCategories as $cat):
                            ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $selectedCategory === $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($_SESSION['errors']['category'])): ?>
                            <div class="error"><?php echo $_SESSION['errors']['category'][0]; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Price (৳) *</label>
                        <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($_SESSION['old']['price'] ?? $test['price']); ?>" required>
                        <?php if (isset($_SESSION['errors']['price'])): ?>
                            <div class="error"><?php echo $_SESSION['errors']['price'][0]; ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Sample Type</label>
                        <input type="text" name="sample_type" value="<?php echo htmlspecialchars($_SESSION['old']['sample_type'] ?? $test['sample_type'] ?? ''); ?>" placeholder="e.g., Blood, Urine">
                    </div>

                    <div class="form-group">
                        <label>Turnaround Time</label>
                        <input type="text" name="turnaround_time" value="<?php echo htmlspecialchars($_SESSION['old']['turnaround_time'] ?? $test['turnaround_time'] ?? ''); ?>" placeholder="e.g., 2-4 hours">
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Test description, purpose, and other details..."><?php echo htmlspecialchars($_SESSION['old']['description'] ?? $test['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" <?php echo ($test['is_active'] ?? 1) ? 'checked' : ''; ?>>
                        <label for="is_active" style="margin-bottom: 0;">Active (Test available for orders)</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                    <a href="/tests" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <?php
    // Clear old input and errors
    unset($_SESSION['old'], $_SESSION['errors']);
    ?>
</body>
</html>