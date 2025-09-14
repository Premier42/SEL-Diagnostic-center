<?php
// Invoice API endpoints

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
            handleGetInvoices($pdo);
            break;
        case 'POST':
            handleCreateInvoice($pdo, $input);
            break;
        case 'PUT':
            handleUpdateInvoice($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteInvoice($pdo, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetInvoices($pdo) {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Get single invoice with details
        $stmt = $pdo->prepare("
            SELECT i.*, d.name as doctor_name, d.workplace as doctor_workplace 
            FROM invoices i 
            LEFT JOIN doctors d ON i.doctor_id = d.id 
            WHERE i.id = ?
        ");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$invoice) {
            http_response_code(404);
            echo json_encode(['error' => 'Invoice not found']);
            return;
        }
        
        // Get invoice tests
        $stmt = $pdo->prepare("
            SELECT it.*, t.name as test_name 
            FROM invoice_tests it 
            LEFT JOIN tests t ON it.test_code = t.code 
            WHERE it.invoice_id = ?
        ");
        $stmt->execute([$id]);
        $invoice['tests'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($invoice);
    } else {
        // Get all invoices with pagination and filtering
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 50);
        $offset = ($page - 1) * $limit;
        
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $whereConditions = [];
        $params = [];
        
        if ($status) {
            $whereConditions[] = "i.payment_status = ?";
            $params[] = $status;
        }
        
        if ($dateFrom) {
            $whereConditions[] = "DATE(i.created_at) >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $whereConditions[] = "DATE(i.created_at) <= ?";
            $params[] = $dateTo;
        }
        
        if ($search) {
            $whereConditions[] = "(i.patient_name LIKE ? OR i.patient_phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Get invoices
        $sql = "
            SELECT i.*, d.name as doctor_name, d.workplace as doctor_workplace 
            FROM invoices i 
            LEFT JOIN doctors d ON i.doctor_id = d.id 
            $whereClause
            ORDER BY i.created_at DESC 
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get statistics
        $statsParams = array_slice($params, 0, -2); // Remove limit and offset
        
        $statsSql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid,
                SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) as partial,
                SUM(amount_paid) as revenue,
                SUM(total_amount) as total_amount
            FROM invoices i 
            LEFT JOIN doctors d ON i.doctor_id = d.id 
            $whereClause
        ";
        
        $stmt = $pdo->prepare($statsSql);
        $stmt->execute($statsParams);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'invoices' => $invoices,
            'stats' => $stats,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$stats['total']
            ]
        ]);
    }
}

function handleCreateInvoice($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }
    
    // Validate required fields
    $required = ['patient_name', 'patient_age', 'patient_gender', 'patient_phone', 'doctor_id'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            return;
        }
    }
    
    $pdo->beginTransaction();
    
    try {
        // Calculate total amount
        $totalAmount = 0;
        if (isset($input['tests']) && is_array($input['tests'])) {
            foreach ($input['tests'] as $test) {
                $totalAmount += (float)($test['price'] ?? 0);
            }
        }
        
        $discountAmount = (float)($input['discount_amount'] ?? 0);
        $finalAmount = max(0, $totalAmount - $discountAmount);
        
        // Insert invoice
        $stmt = $pdo->prepare("
            INSERT INTO invoices (
                patient_name, patient_age, patient_gender, patient_phone, 
                doctor_id, total_amount, amount_paid, discount_amount, 
                payment_status, notes, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $input['patient_name'],
            $input['patient_age'],
            $input['patient_gender'],
            $input['patient_phone'],
            $input['doctor_id'],
            $finalAmount,
            0, // amount_paid starts at 0
            $discountAmount,
            'pending', // default status
            $input['notes'] ?? ''
        ]);
        
        $invoiceId = $pdo->lastInsertId();
        
        // Insert invoice tests
        if (isset($input['tests']) && is_array($input['tests'])) {
            $testStmt = $pdo->prepare("
                INSERT INTO invoice_tests (invoice_id, test_code, price) 
                VALUES (?, ?, ?)
            ");
            
            foreach ($input['tests'] as $test) {
                $testStmt->execute([
                    $invoiceId,
                    $test['code'],
                    $test['price']
                ]);
            }
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Invoice created successfully',
            'invoice_id' => $invoiceId
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function handleUpdateInvoice($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invoice ID is required']);
        return;
    }
    
    $id = $input['id'];
    $updates = [];
    $params = [];
    
    // Build dynamic update query
    $allowedFields = [
        'patient_name', 'patient_age', 'patient_gender', 'patient_phone',
        'doctor_id', 'total_amount', 'amount_paid', 'discount_amount',
        'payment_status', 'notes'
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
    
    $params[] = $id;
    
    $sql = "UPDATE invoices SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Invoice not found']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Invoice updated successfully'
    ]);
}

function handleDeleteInvoice($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invoice ID is required']);
        return;
    }
    
    $pdo->beginTransaction();
    
    try {
        // Delete related records first
        $stmt = $pdo->prepare("DELETE FROM test_results WHERE report_id IN (SELECT id FROM test_reports WHERE invoice_id = ?)");
        $stmt->execute([$input['id']]);
        
        $stmt = $pdo->prepare("DELETE FROM test_reports WHERE invoice_id = ?");
        $stmt->execute([$input['id']]);
        
        $stmt = $pdo->prepare("DELETE FROM invoice_tests WHERE invoice_id = ?");
        $stmt->execute([$input['id']]);
        
        // Delete invoice
        $stmt = $pdo->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$input['id']]);
        
        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Invoice not found']);
            return;
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Invoice deleted successfully'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>
