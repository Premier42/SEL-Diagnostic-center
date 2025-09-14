<?php
// Tests API endpoints

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
            handleGetTests($pdo);
            break;
        case 'POST':
            handleCreateTest($pdo, $input);
            break;
        case 'PUT':
            handleUpdateTest($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteTest($pdo, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetTests($pdo) {
    $code = $_GET['code'] ?? null;
    
    if ($code) {
        $stmt = $pdo->prepare("SELECT * FROM tests WHERE code = ?");
        $stmt->execute([$code]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$test) {
            http_response_code(404);
            echo json_encode(['error' => 'Test not found']);
            return;
        }
        
        // Get test parameters
        $stmt = $pdo->prepare("SELECT * FROM test_parameters WHERE test_code = ? ORDER BY parameter_name");
        $stmt->execute([$code]);
        $test['parameters'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($test);
    } else {
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $whereConditions = [];
        $params = [];
        
        if ($category) {
            $whereConditions[] = "category = ?";
            $params[] = $category;
        }
        
        if ($search) {
            $whereConditions[] = "(name LIKE ? OR code LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $stmt = $pdo->prepare("SELECT * FROM tests $whereClause ORDER BY name");
        $stmt->execute($params);
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($tests);
    }
}

function handleCreateTest($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }
    
    $required = ['code', 'name', 'category', 'price'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            return;
        }
    }
    
    $pdo->beginTransaction();
    
    try {
        // Insert test
        $stmt = $pdo->prepare("
            INSERT INTO tests (code, name, category, description, price, sample_type, method, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $input['code'],
            $input['name'],
            $input['category'],
            $input['description'] ?? '',
            $input['price'],
            $input['sample_type'] ?? 'Blood',
            $input['method'] ?? ''
        ]);
        
        // Insert test parameters if provided
        if (isset($input['parameters']) && is_array($input['parameters'])) {
            $paramStmt = $pdo->prepare("
                INSERT INTO test_parameters (test_code, parameter_name, normal_range, unit) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($input['parameters'] as $param) {
                $paramStmt->execute([
                    $input['code'],
                    $param['name'],
                    $param['normal_range'] ?? '',
                    $param['unit'] ?? ''
                ]);
            }
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Test created successfully',
            'test_code' => $input['code']
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function handleUpdateTest($pdo, $input) {
    if (!$input || !isset($input['code'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Test code is required']);
        return;
    }
    
    $updates = [];
    $params = [];
    
    $allowedFields = ['name', 'category', 'description', 'price', 'sample_type', 'method'];
    
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
    
    $params[] = $input['code'];
    
    $sql = "UPDATE tests SET " . implode(', ', $updates) . " WHERE code = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Test not found']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Test updated successfully'
    ]);
}

function handleDeleteTest($pdo, $input) {
    if (!$input || !isset($input['code'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Test code is required']);
        return;
    }
    
    $pdo->beginTransaction();
    
    try {
        // Delete test parameters first
        $stmt = $pdo->prepare("DELETE FROM test_parameters WHERE test_code = ?");
        $stmt->execute([$input['code']]);
        
        // Delete test
        $stmt = $pdo->prepare("DELETE FROM tests WHERE code = ?");
        $stmt->execute([$input['code']]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Test not found']);
            return;
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Test deleted successfully'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>
