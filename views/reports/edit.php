<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Test Report - SEL Diagnostic Center</title>
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
            max-width: 1200px;
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

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .content-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .patient-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .patient-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-item .label {
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-item .value {
            color: #333;
            font-weight: 600;
            font-size: 16px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
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

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .results-table th,
        .results-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .results-table th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 14px;
        }

        .results-table input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .results-table input:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
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

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-in_progress {
            background: #cfe2ff;
            color: #084298;
        }

        .badge-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-verified {
            background: #d4edda;
            color: #155724;
        }

        .add-row-btn {
            margin-top: 10px;
            padding: 8px 16px;
            background: #17a2b8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .add-row-btn:hover {
            background: #138496;
        }

        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Edit Test Report</h1>
            <a href="/reports" class="btn btn-secondary">← Back to Reports</a>
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

            <!-- Patient & Test Info -->
            <div class="patient-info">
                <h3>Patient & Test Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Patient Name</span>
                        <span class="value"><?php echo htmlspecialchars($report['patient_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Age / Gender</span>
                        <span class="value"><?php echo htmlspecialchars($report['patient_age']); ?> years / <?php echo htmlspecialchars($report['patient_gender']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Phone</span>
                        <span class="value"><?php echo htmlspecialchars($report['patient_phone']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Test Name</span>
                        <span class="value"><?php echo htmlspecialchars($report['test_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Test Code</span>
                        <span class="value"><?php echo htmlspecialchars($report['test_code']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Category</span>
                        <span class="value"><?php echo htmlspecialchars($report['category']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Current Status</span>
                        <span class="value">
                            <span class="badge badge-<?php echo $report['status']; ?>">
                                <?php echo strtoupper($report['status']); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Report ID</span>
                        <span class="value">#<?php echo $report['id']; ?></span>
                    </div>
                </div>
            </div>

            <!-- Data Entry Form -->
            <form method="POST" action="/reports/<?php echo $report['id']; ?>/update">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf(); ?>">

                <div class="form-section">
                    <h3>🔬 Test Results Data Entry</h3>

                    <?php if (!empty($parameters)): ?>
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                    <th>Unit</th>
                                    <th>Normal Range</th>
                                </tr>
                            </thead>
                            <tbody id="resultsBody">
                                <?php foreach ($parameters as $index => $param): ?>
                                    <?php
                                    $existing = $results_map[$param['parameter_name']] ?? null;
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="parameters[]" value="<?php echo htmlspecialchars($param['parameter_name']); ?>">
                                            <strong><?php echo htmlspecialchars($param['parameter_name']); ?></strong>
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="values[]"
                                                   value="<?php echo $existing ? htmlspecialchars($existing['value']) : ''; ?>"
                                                   placeholder="Enter value">
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="units[]"
                                                   value="<?php echo $existing ? htmlspecialchars($existing['unit']) : htmlspecialchars($param['unit'] ?? ''); ?>"
                                                   placeholder="Unit">
                                        </td>
                                        <td>
                                            <input type="text"
                                                   name="ranges[]"
                                                   value="<?php echo $existing ? htmlspecialchars($existing['normal_range']) : htmlspecialchars($param['normal_range_text'] ?? ''); ?>"
                                                   placeholder="Normal range">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="help-text">No predefined parameters for this test. Add custom parameters below.</p>
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>Parameter</th>
                                    <th>Value</th>
                                    <th>Unit</th>
                                    <th>Normal Range</th>
                                </tr>
                            </thead>
                            <tbody id="resultsBody">
                                <?php if (!empty($existing_results)): ?>
                                    <?php foreach ($existing_results as $result): ?>
                                        <tr>
                                            <td><input type="text" name="parameters[]" value="<?php echo htmlspecialchars($result['parameter_name']); ?>" placeholder="Parameter name"></td>
                                            <td><input type="text" name="values[]" value="<?php echo htmlspecialchars($result['value']); ?>" placeholder="Enter value"></td>
                                            <td><input type="text" name="units[]" value="<?php echo htmlspecialchars($result['unit']); ?>" placeholder="Unit"></td>
                                            <td><input type="text" name="ranges[]" value="<?php echo htmlspecialchars($result['normal_range']); ?>" placeholder="Normal range"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td><input type="text" name="parameters[]" placeholder="Parameter name"></td>
                                        <td><input type="text" name="values[]" placeholder="Enter value"></td>
                                        <td><input type="text" name="units[]" placeholder="Unit"></td>
                                        <td><input type="text" name="ranges[]" placeholder="Normal range"></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <button type="button" class="add-row-btn" onclick="addRow()">+ Add Parameter</button>
                    <?php endif; ?>
                </div>

                <div class="form-section">
                    <h3>📊 Report Status & Notes</h3>

                    <div class="form-group">
                        <label>Report Status *</label>
                        <select name="status" required>
                            <option value="pending" <?php echo $report['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="in_progress" <?php echo $report['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                            <option value="completed" <?php echo $report['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="verified" <?php echo $report['status'] === 'verified' ? 'selected' : ''; ?>>Verified</option>
                        </select>
                        <div class="help-text">Select "Completed" when all results are entered. Admin/Doctor can verify later.</div>
                    </div>

                    <div class="form-group">
                        <label>Technician Notes</label>
                        <textarea name="notes" placeholder="Add any observations, remarks, or special notes..."><?php echo htmlspecialchars($report['notes'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">💾 Save Report</button>
                    <a href="/reports/<?php echo $report['id']; ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addRow() {
            const tbody = document.getElementById('resultsBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="parameters[]" placeholder="Parameter name"></td>
                <td><input type="text" name="values[]" placeholder="Enter value"></td>
                <td><input type="text" name="units[]" placeholder="Unit"></td>
                <td><input type="text" name="ranges[]" placeholder="Normal range"></td>
            `;
            tbody.appendChild(row);
        }
    </script>
</body>
</html>