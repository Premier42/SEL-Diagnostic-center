<?php
// Test Results API endpoints

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
        case 'POST':
            handleAddResults($pdo, $input);
            break;
        case 'PUT':
            handleUpdateResults($pdo, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleAddResults($pdo, $input) {
    if (!$input || !isset($input['report_id']) || !isset($input['results'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Report ID and results are required']);
        return;
    }
    
    $pdo->beginTransaction();
    
    try {
        $reportId = $input['report_id'];
        $notes = $input['notes'] ?? '';
        $results = $input['results'];
        
        // Update report status and notes
        $stmt = $pdo->prepare("UPDATE test_reports SET status = 'completed', notes = ? WHERE id = ?");
        $stmt->execute([$notes, $reportId]);
        
        // Insert test results
        $resultStmt = $pdo->prepare("
            INSERT INTO test_results (report_id, parameter_name, value, unit, normal_range, is_abnormal, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
        ");
        
        foreach ($results as $result) {
            $resultStmt->execute([
                $reportId,
                $result['parameter_name'],
                $result['value'],
                $result['unit'],
                $result['normal_range'],
                $result['is_abnormal']
            ]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Test results added successfully'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function handleUpdateResults($pdo, $input) {
    if (!$input || !isset($input['report_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Report ID is required']);
        return;
    }
    
    $pdo->beginTransaction();
    
    try {
        $reportId = $input['report_id'];
        
        // Deactivate existing results
        $stmt = $pdo->prepare("UPDATE test_results SET is_active = 0 WHERE report_id = ?");
        $stmt->execute([$reportId]);
        
        // Insert new results if provided
        if (isset($input['results']) && is_array($input['results'])) {
            $resultStmt = $pdo->prepare("
                INSERT INTO test_results (report_id, parameter_name, value, unit, normal_range, is_abnormal, is_active, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
            ");
            
            foreach ($input['results'] as $result) {
                $resultStmt->execute([
                    $reportId,
                    $result['parameter_name'],
                    $result['value'],
                    $result['unit'],
                    $result['normal_range'],
                    $result['is_abnormal']
                ]);
            }
        }
        
        // Update report notes if provided
        if (isset($input['notes'])) {
            $stmt = $pdo->prepare("UPDATE test_reports SET notes = ? WHERE id = ?");
            $stmt->execute([$input['notes'], $reportId]);
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Test results updated successfully'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
?>
