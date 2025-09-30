<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - SEL Diagnostic Center</title>
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
            max-width: 1400px;
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

        .header-actions {
            display: flex;
            gap: 10px;
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

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .stat-card.warning .value {
            color: #ff9800;
        }

        .stat-card.danger .value {
            color: #f44336;
        }

        .stat-card.success .value {
            color: #4caf50;
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .filters form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .content-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        th {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }

        td {
            color: #212529;
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-primary {
            background: #d1ecf1;
            color: #0c5460;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Inventory Management</h1>
            <div class="header-actions">
                <a href="/dashboard" class="btn btn-secondary">← Dashboard</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/inventory/create" class="btn btn-primary">+ Add Item</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Items</h3>
                <div class="value"><?php echo number_format($stats['total_items'] ?? 0); ?></div>
            </div>
            <div class="stat-card success">
                <h3>Total Value</h3>
                <div class="value">৳<?php echo number_format($stats['total_value'] ?? 0, 2); ?></div>
            </div>
            <div class="stat-card warning">
                <h3>Low Stock Items</h3>
                <div class="value"><?php echo number_format($stats['low_stock_count'] ?? 0); ?></div>
            </div>
            <div class="stat-card danger">
                <h3>Out of Stock</h3>
                <div class="value"><?php echo number_format($stats['out_of_stock_count'] ?? 0); ?></div>
            </div>
        </div>

        <div class="filters">
            <form method="GET" action="/inventory">
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" placeholder="Item name or code..."
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"
                                    <?php echo (($_GET['category'] ?? '') === $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst($cat)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="">All Status</option>
                        <option value="low_stock" <?php echo (($_GET['status'] ?? '') === 'low_stock') ? 'selected' : ''; ?>>Low Stock</option>
                        <option value="out_of_stock" <?php echo (($_GET['status'] ?? '') === 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>

        <div class="content-card">
            <div class="table-container">
                <?php if (!empty($items)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>In Stock</th>
                                <th>Reorder Level</th>
                                <th>Unit Price</th>
                                <th>Total Value</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <?php
                                    $stock = (int)$item['quantity_in_stock'];
                                    $reorder = (int)$item['reorder_level'];
                                    $unitPrice = (float)$item['unit_price'];
                                    $totalValue = $stock * $unitPrice;

                                    if ($stock == 0) {
                                        $statusBadge = '<span class="badge badge-danger">Out of Stock</span>';
                                    } elseif ($stock <= $reorder) {
                                        $statusBadge = '<span class="badge badge-warning">Low Stock</span>';
                                    } else {
                                        $statusBadge = '<span class="badge badge-success">In Stock</span>';
                                    }
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($item['item_code']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td><span class="badge badge-primary"><?php echo htmlspecialchars(ucfirst($item['category'])); ?></span></td>
                                    <td><strong><?php echo number_format($stock); ?></strong> <?php echo htmlspecialchars($item['unit'] ?? 'units'); ?></td>
                                    <td><?php echo number_format($reorder); ?></td>
                                    <td>৳<?php echo number_format($unitPrice, 2); ?></td>
                                    <td><strong>৳<?php echo number_format($totalValue, 2); ?></strong></td>
                                    <td><?php echo $statusBadge; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($item['updated_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3>No inventory items found</h3>
                        <p>Try adjusting your filters or add a new item</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>