<?php
// Reports API endpoints

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
            handleGetReports($pdo);
            break;
        case 'PUT':
            handleUpdateReport($pdo, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetReports($pdo) {
    $id = $_GET['id'] ?? null;
    $status = $_GET['status'] ?? '';
    
    if ($id) {
        // Get single report with details
        $stmt = $pdo->prepare("
            SELECT tr.*, i.patient_name, i.patient_age, i.patient_gender,
                   t.name as test_name, u.full_name as technician_name
            FROM test_reports tr
            LEFT JOIN invoices i ON tr.invoice_id = i.id
            LEFT JOIN tests t ON tr.test_code = t.code
            LEFT JOIN users u ON tr.technician_id = u.id
            WHERE tr.id = ?
        ");
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) {
            http_response_code(404);
            echo json_encode(['error' => 'Report not found']);
            return;
        }
        
        // Get test results
        $stmt = $pdo->prepare("
            SELECT * FROM test_results 
            WHERE report_id = ? AND is_active = 1
            ORDER BY parameter_name
        ");
        $stmt->execute([$id]);
        $report['results'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($report);
    } else {
        // Get all reports with filtering
        $whereConditions = ['1=1'];
        $params = [];
        
        if ($status) {
            $whereConditions[] = "tr.status = ?";
            $params[] = $status;
        }
        
        if (isset($_GET['date_from']) && $_GET['date_from']) {
            $whereConditions[] = "DATE(tr.created_at) >= ?";
            $params[] = $_GET['date_from'];
        }
        
        if (isset($_GET['date_to']) && $_GET['date_to']) {
            $whereConditions[] = "DATE(tr.created_at) <= ?";
            $params[] = $_GET['date_to'];
        }
        
        if (isset($_GET['test_code']) && $_GET['test_code']) {
            $whereConditions[] = "tr.test_code = ?";
            $params[] = $_GET['test_code'];
        }
        
        if (isset($_GET['search']) && $_GET['search']) {
            $whereConditions[] = "(i.patient_name LIKE ? OR CAST(i.id AS CHAR) LIKE ?)";
            $search = '%' . $_GET['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get reports
        $sql = "
            SELECT tr.*, i.patient_name, i.patient_age, i.patient_gender,
                   t.name as test_name, u.full_name as technician_name
            FROM test_reports tr
            LEFT JOIN invoices i ON tr.invoice_id = i.id
            LEFT JOIN tests t ON tr.test_code = t.code
            LEFT JOIN users u ON tr.technician_id = u.id
            WHERE $whereClause
            ORDER BY tr.created_at DESC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get statistics
        $statsParams = $params;
        $statsSql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN tr.status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN tr.status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN tr.status = 'verified' THEN 1 ELSE 0 END) as verified
            FROM test_reports tr
            LEFT JOIN invoices i ON tr.invoice_id = i.id
            WHERE $whereClause
        ";
        
        $stmt = $pdo->prepare($statsSql);
        $stmt->execute($statsParams);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get abnormal results count
        $abnormalSql = "
            SELECT COUNT(DISTINCT tr.id) as abnormal
            FROM test_reports tr
            LEFT JOIN invoices i ON tr.invoice_id = i.id
            LEFT JOIN test_results res ON tr.id = res.report_id
            WHERE $whereClause AND res.is_abnormal = 1
        ";
        
        $stmt = $pdo->prepare($abnormalSql);
        $stmt->execute($statsParams);
        $abnormalResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['abnormal'] = $abnormalResult['abnormal'];
        
        echo json_encode([
            'reports' => $reports,
            'stats' => $stats
        ]);
    }
}

function handleUpdateReport($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Report ID is required']);
        return;
    }
    
    $id = $input['id'];
    $updates = [];
    $params = [];
    
    // Build dynamic update query
    $allowedFields = ['status', 'notes', 'technician_id'];
    
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
    
    $sql = "UPDATE test_reports SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Report not found']);
        return;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Report updated successfully'
    ]);
}
?>
