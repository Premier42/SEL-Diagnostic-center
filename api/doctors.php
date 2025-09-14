<?php
// Doctors API endpoints

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
            handleGetDoctors($pdo);
            break;
        case 'POST':
            handleCreateDoctor($pdo, $input);
            break;
        case 'PUT':
            handleUpdateDoctor($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteDoctor($pdo, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetDoctors($pdo) {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
        $stmt->execute([$id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            http_response_code(404);
            echo json_encode(['error' => 'Doctor not found']);
            return;
        }
        
        echo json_encode($doctor);
    } else {
        $search = $_GET['search'] ?? '';
        $whereClause = '';
        $params = [];
        
        if ($search) {
            $whereClause = "WHERE name LIKE ? OR workplace LIKE ? OR qualifications LIKE ?";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        
        $stmt = $pdo->prepare("SELECT * FROM doctors $whereClause ORDER BY name");
        $stmt->execute($params);
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($doctors);
    }
}

function handleCreateDoctor($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }
    
    $required = ['name', 'qualifications', 'workplace', 'phone', 'email'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            return;
        }
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO doctors (name, qualifications, workplace, phone, email, address, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $input['name'],
        $input['qualifications'],
        $input['workplace'],
        $input['phone'],
        $input['email'],
        $input['address'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Doctor created successfully',
        'doctor_id' => $pdo->lastInsertId()
    ]);
}

function handleUpdateDoctor($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Doctor ID is required']);
        return;
    }
    
    $updates = [];
    $params = [];
    
    $allowedFields = ['name', 'qualifications', 'workplace', 'phone', 'email', 'address'];
    
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
    
    $sql = "UPDATE doctors SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Doctor not found']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Doctor updated successfully'
    ]);
}

function handleDeleteDoctor($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Doctor ID is required']);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->execute([$input['id']]);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Doctor not found']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Doctor deleted successfully'
    ]);
}
?>
