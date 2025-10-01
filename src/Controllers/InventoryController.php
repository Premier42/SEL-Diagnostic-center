<?php

namespace App\Controllers;

class InventoryController extends BaseController
{
    public function index()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            return;
        }

        $db = getDB();
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';

        try {
            // Build query with filters
            $whereConditions = [];
            $params = [];

            if (!empty($search)) {
                $whereConditions[] = "(item_name LIKE ? OR item_code LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            if (!empty($category)) {
                $whereConditions[] = "category = ?";
                $params[] = $category;
            }

            if (!empty($status)) {
                if ($status === 'low_stock') {
                    $whereConditions[] = "quantity_in_stock <= reorder_level";
                } elseif ($status === 'out_of_stock') {
                    $whereConditions[] = "quantity_in_stock = 0";
                }
            }

            $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Get inventory items
            $query = "
                SELECT *
                FROM inventory_items
                $whereClause
                ORDER BY item_name
            ";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $items = $stmt->fetchAll();

            // Get categories for filter
            $stmt = $db->query("SELECT DISTINCT category FROM inventory_items ORDER BY category");
            $categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            // Get statistics
            $statsQuery = "
                SELECT
                    COUNT(*) as total_items,
                    SUM(quantity_in_stock * unit_price) as total_value,
                    COUNT(CASE WHEN quantity_in_stock <= reorder_level THEN 1 END) as low_stock_count,
                    COUNT(CASE WHEN quantity_in_stock = 0 THEN 1 END) as out_of_stock_count
                FROM inventory_items
            ";

            $stmt = $db->query($statsQuery);
            $stats = $stmt->fetch();

            include __DIR__ . '/../../views/inventory/index.php';

        } catch (Exception $e) {
            log_activity("Inventory listing error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load inventory.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function create()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            return;
        }

        include __DIR__ . '/../../views/inventory/create.php';
    }

    public function store()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->json(['error' => 'Insufficient permissions'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/inventory');
            return;
        }

        // Validate CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->flashMessage('error', 'Invalid security token');
            redirect('/inventory/create');
            return;
        }

        $db = getDB();

        try {
            // Validate required fields
            $errors = $this->validate($_POST, [
                'code' => 'required',
                'name' => 'required',
                'category' => 'required',
                'unit_price' => 'required|numeric',
                'minimum_stock' => 'required|numeric'
            ]);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                redirect('/inventory/create');
                return;
            }

            // Insert inventory item
            $stmt = $db->prepare("
                INSERT INTO inventory_items (
                    item_code, item_name, category, unit_price, quantity_in_stock, reorder_level, description
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                strtoupper($this->sanitize($_POST['code'])),
                $this->sanitize($_POST['name']),
                $this->sanitize($_POST['category']),
                (float)$_POST['unit_price'],
                (int)($_POST['current_stock'] ?? 0),
                (int)$_POST['minimum_stock'],
                $this->sanitize($_POST['description'] ?? '')
            ]);

            $item_id = $db->lastInsertId();

            log_activity("Created inventory item: " . $_POST['name'], 'info', [
                'item_id' => $item_id,
                'item_code' => $_POST['code'],
                'user_id' => $_SESSION['user_id']
            ]);

            $this->flashMessage('success', 'Inventory item created successfully!');
            redirect('/inventory');

        } catch (Exception $e) {
            log_activity("Inventory creation error: " . $e->getMessage(), 'error');

            $this->flashMessage('error', 'Failed to create inventory item. Please try again.');
            $_SESSION['old'] = $_POST;
            redirect('/inventory/create');
        }
    }
}