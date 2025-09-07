<?php
// Consumables/Inventory API endpoints

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $pdo = getDBConnection();

    switch ($method) {
        case 'GET':
            handleGetConsumables($pdo);
            break;
        case 'POST':
            handleCreateConsumable($pdo, $input);
            break;
        case 'PUT':
            handleUpdateConsumable($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteConsumable($pdo, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetConsumables($pdo) {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM consumables WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            http_response_code(404);
            echo json_encode(['error' => 'Item not found']);
            return;
        }

        echo json_encode($item);
    } else {
        // Get all consumables with filtering
        $category = $_GET['category'] ?? '';
        $supplier = $_GET['supplier'] ?? '';
        $search = $_GET['search'] ?? '';

        $whereConditions = [];
        $params = [];

        if ($category) {
            $whereConditions[] = "category = ?";
            $params[] = $category;
        }

        if ($supplier) {
            $whereConditions[] = "supplier = ?";
            $params[] = $supplier;
        }

        if ($search) {
            $whereConditions[] = "(item_name LIKE ? OR item_code LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        $stmt = $pdo->prepare("SELECT * FROM consumables $whereClause ORDER BY item_name");
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate statistics
        $total = count($items);
        $inStock = 0;
        $lowStock = 0;
        $outOfStock = 0;

        foreach ($items as $item) {
            if ($item['current_stock'] <= 0) {
                $outOfStock++;
            } elseif ($item['current_stock'] <= $item['minimum_level']) {
                $lowStock++;
            } else {
                $inStock++;
            }
        }

        $stats = [
            'total' => $total,
            'in_stock' => $inStock,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock
        ];

        echo json_encode([
            'items' => $items,
            'stats' => $stats
        ]);
    }
}

function handleCreateConsumable($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }

    $required = ['item_name', 'category', 'unit', 'unit_price', 'current_stock', 'minimum_level', 'supplier'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            return;
        }
    }

    // Check if item code is unique (if provided)
    if (!empty($input['item_code'])) {
        $stmt = $pdo->prepare("SELECT id FROM consumables WHERE item_code = ?");
        $stmt->execute([$input['item_code']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Item code already exists']);
            return;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO consumables (
            item_name, item_code, category, unit, unit_price,
            current_stock, minimum_level, maximum_level,
            supplier, expiry_date, description, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $input['item_name'],
        $input['item_code'] ?? null,
        $input['category'],
        $input['unit'],
        $input['unit_price'],
        $input['current_stock'],
        $input['minimum_level'],
        $input['maximum_level'] ?? null,
        $input['supplier'],
        $input['expiry_date'] ?? null,
        $input['description'] ?? null
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Inventory item created successfully',
        'item_id' => $pdo->lastInsertId()
    ]);
}

function handleUpdateConsumable($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Item ID is required']);
        return;
    }

    $updates = [];
    $params = [];

    $allowedFields = [
        'item_name', 'item_code', 'category', 'unit', 'unit_price',
        'current_stock', 'minimum_level', 'maximum_level',
        'supplier', 'expiry_date', 'description'
    ];

    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updates[] = "$field = ?";
            $params[] = $input[$field];
        }
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }

    $params[] = $input['id'];

    $sql = "UPDATE consumables SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Inventory item updated successfully'
    ]);
}

function handleDeleteConsumable($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Item ID is required']);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM consumables WHERE id = ?");
    $stmt->execute([$input['id']]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Inventory item deleted successfully'
    ]);
}
?>