<?php
// Audit Logs API endpoint

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../utils/audit-logger.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $auditLogger = new AuditLogger();

    switch ($method) {
        case 'GET':
            handleGetLogs($auditLogger);
            break;
        case 'POST':
            handleLogAction($auditLogger, $input);
            break;
        case 'DELETE':
            handleCleanLogs($auditLogger, $input);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGetLogs($auditLogger) {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'stats':
            $stats = $auditLogger->getStats(
                $_GET['date_from'] ?? null,
                $_GET['date_to'] ?? null
            );
            echo json_encode($stats);
            break;

        case 'user_activity':
            $userId = $_GET['user_id'] ?? null;
            if (!$userId) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
                return;
            }

            $activity = $auditLogger->getUserActivity(
                $userId,
                $_GET['date_from'] ?? null,
                $_GET['date_to'] ?? null
            );
            echo json_encode($activity);
            break;

        default:
            // Get audit logs with filtering
            $filters = [
                'user_id' => $_GET['user_id'] ?? '',
                'action' => $_GET['action'] ?? '',
                'table_name' => $_GET['table_name'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? ''
            ];

            $limit = (int)($_GET['limit'] ?? 50);
            $offset = (int)($_GET['offset'] ?? 0);

            $logs = $auditLogger->getLogs($filters, $limit, $offset);

            // Get total count for pagination
            $totalSql = "SELECT COUNT(*) as total FROM audit_logs WHERE 1=1";
            $totalParams = [];

            if ($filters['user_id']) {
                $totalSql .= " AND user_id = ?";
                $totalParams[] = $filters['user_id'];
            }
            if ($filters['action']) {
                $totalSql .= " AND action = ?";
                $totalParams[] = $filters['action'];
            }
            if ($filters['table_name']) {
                $totalSql .= " AND table_name = ?";
                $totalParams[] = $filters['table_name'];
            }
            if ($filters['date_from']) {
                $totalSql .= " AND DATE(created_at) >= ?";
                $totalParams[] = $filters['date_from'];
            }
            if ($filters['date_to']) {
                $totalSql .= " AND DATE(created_at) <= ?";
                $totalParams[] = $filters['date_to'];
            }

            $pdo = getDBConnection();
            $stmt = $pdo->prepare($totalSql);
            $stmt->execute($totalParams);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            echo json_encode([
                'logs' => $logs,
                'pagination' => [
                    'total' => (int)$total,
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + $limit) < $total
                ]
            ]);
    }
}

function handleLogAction($auditLogger, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }

    $required = ['action', 'table', 'record_id'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$field' is required"]);
            return;
        }
    }

    $success = $auditLogger->log(
        $input['action'],
        $input['table'],
        $input['record_id'],
        $input['old_data'] ?? null,
        $input['new_data'] ?? null,
        $input['user_id'] ?? null
    );

    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Audit log recorded successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to record audit log']);
    }
}

function handleCleanLogs($auditLogger, $input) {
    $daysToKeep = $input['days_to_keep'] ?? 365;

    if ($daysToKeep < 30) {
        http_response_code(400);
        echo json_encode(['error' => 'Cannot delete logs newer than 30 days']);
        return;
    }

    $deletedCount = $auditLogger->cleanOldLogs($daysToKeep);

    echo json_encode([
        'success' => true,
        'message' => "Deleted $deletedCount old audit log entries",
        'deleted_count' => $deletedCount
    ]);
}
?>