<?php
// Users API endpoints

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
            handleGetUsers($pdo);
            break;
        case 'POST':
            handleCreateUser($pdo, $input);
            break;
        case 'PUT':
            handleUpdateUser($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteUser($pdo, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetUsers($pdo) {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        // Remove password from response
        unset($user['password']);
        echo json_encode($user);
    } else {
        // Get all users
        $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Remove passwords from response
        foreach ($users as &$user) {
            unset($user['password']);
        }

        // Get statistics
        $totalUsers = count($users);
        $adminUsers = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
        $staffUsers = count(array_filter($users, fn($u) => $u['role'] === 'staff'));

        // Get active today (users who logged in today)
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE DATE(last_login) = CURDATE()");
        $stmt->execute();
        $activeTodayResult = $stmt->fetch(PDO::FETCH_ASSOC);

        $stats = [
            'total' => $totalUsers,
            'admins' => $adminUsers,
            'staff' => $staffUsers,
            'active_today' => (int)$activeTodayResult['count']
        ];

        echo json_encode([
            'users' => $users,
            'stats' => $stats
        ]);
    }
}

function handleCreateUser($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }

    $required = ['username', 'full_name', 'email', 'role', 'password'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            return;
        }
    }

    // Validate username uniqueness
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$input['username']]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Username already exists']);
        return;
    }

    // Validate email uniqueness
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$input['email']]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists']);
        return;
    }

    // Hash password
    $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (username, full_name, email, phone, role, department, password, is_active, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $input['username'],
        $input['full_name'],
        $input['email'],
        $input['phone'] ?? null,
        $input['role'],
        $input['department'] ?? null,
        $hashedPassword,
        $input['is_active'] ?? 1
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'user_id' => $pdo->lastInsertId()
    ]);
}

function handleUpdateUser($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'User ID is required']);
        return;
    }

    $updates = [];
    $params = [];

    $allowedFields = ['full_name', 'email', 'phone', 'role', 'department', 'is_active'];

    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updates[] = "$field = ?";
            $params[] = $input[$field];
        }
    }

    // Handle password update separately
    if (isset($input['password']) && !empty($input['password'])) {
        $updates[] = "password = ?";
        $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }

    $params[] = $input['id'];

    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }

    echo json_encode([
        'success' => true,
        'message' => 'User updated successfully'
    ]);
}

function handleDeleteUser($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'User ID is required']);
        return;
    }

    // Check if user is admin (prevent deletion of admin user)
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$input['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }

    if ($user['username'] === 'admin') {
        http_response_code(400);
        echo json_encode(['error' => 'Cannot delete admin user']);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$input['id']]);

    echo json_encode([
        'success' => true,
        'message' => 'User deleted successfully'
    ]);
}
?>