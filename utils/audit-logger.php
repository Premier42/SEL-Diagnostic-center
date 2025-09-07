<?php
// Audit Logging System

require_once '../config/database.php';

class AuditLogger {
    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    /**
     * Log an audit event
     */
    public function log($action, $table, $recordId, $oldData = null, $newData = null, $userId = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO audit_logs (
                    user_id, action, table_name, record_id,
                    old_data, new_data, ip_address, user_agent, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $userId ?? $this->getCurrentUserId(),
                $action,
                $table,
                $recordId,
                $oldData ? json_encode($oldData) : null,
                $newData ? json_encode($newData) : null,
                $this->getClientIP(),
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Audit log failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log invoice creation
     */
    public function logInvoiceCreated($invoiceId, $invoiceData, $userId = null) {
        return $this->log('CREATE', 'invoices', $invoiceId, null, $invoiceData, $userId);
    }

    /**
     * Log invoice update
     */
    public function logInvoiceUpdated($invoiceId, $oldData, $newData, $userId = null) {
        return $this->log('UPDATE', 'invoices', $invoiceId, $oldData, $newData, $userId);
    }

    /**
     * Log payment received
     */
    public function logPaymentReceived($invoiceId, $paymentData, $userId = null) {
        return $this->log('PAYMENT', 'invoices', $invoiceId, null, $paymentData, $userId);
    }

    /**
     * Log test result entry
     */
    public function logTestResultEntered($reportId, $resultData, $userId = null) {
        return $this->log('TEST_RESULT', 'test_reports', $reportId, null, $resultData, $userId);
    }

    /**
     * Log report verification
     */
    public function logReportVerified($reportId, $userId = null) {
        return $this->log('VERIFY', 'test_reports', $reportId, null, ['verified' => true], $userId);
    }

    /**
     * Log stock movement
     */
    public function logStockMovement($itemId, $movementData, $userId = null) {
        return $this->log('STOCK_MOVEMENT', 'consumables', $itemId, null, $movementData, $userId);
    }

    /**
     * Log user login
     */
    public function logUserLogin($userId, $loginData = null) {
        return $this->log('LOGIN', 'users', $userId, null, $loginData ?? ['login_time' => date('Y-m-d H:i:s')], $userId);
    }

    /**
     * Log user logout
     */
    public function logUserLogout($userId) {
        return $this->log('LOGOUT', 'users', $userId, null, ['logout_time' => date('Y-m-d H:i:s')], $userId);
    }

    /**
     * Log data export
     */
    public function logDataExport($exportType, $filters = [], $userId = null) {
        return $this->log('EXPORT', 'system', 0, null, [
            'export_type' => $exportType,
            'filters' => $filters,
            'timestamp' => date('Y-m-d H:i:s')
        ], $userId);
    }

    /**
     * Log system configuration change
     */
    public function logConfigChange($setting, $oldValue, $newValue, $userId = null) {
        return $this->log('CONFIG_CHANGE', 'system', 0,
            ['setting' => $setting, 'value' => $oldValue],
            ['setting' => $setting, 'value' => $newValue],
            $userId
        );
    }

    /**
     * Get audit logs with filtering
     */
    public function getLogs($filters = [], $limit = 100, $offset = 0) {
        $whereConditions = ['1=1'];
        $params = [];

        if (!empty($filters['user_id'])) {
            $whereConditions[] = "al.user_id = ?";
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $whereConditions[] = "al.action = ?";
            $params[] = $filters['action'];
        }

        if (!empty($filters['table_name'])) {
            $whereConditions[] = "al.table_name = ?";
            $params[] = $filters['table_name'];
        }

        if (!empty($filters['date_from'])) {
            $whereConditions[] = "DATE(al.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = "DATE(al.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "
            SELECT al.*, u.username, u.full_name
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE $whereClause
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get audit statistics
     */
    public function getStats($dateFrom = null, $dateTo = null) {
        $whereConditions = ['1=1'];
        $params = [];

        if ($dateFrom) {
            $whereConditions[] = "DATE(created_at) >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $whereConditions[] = "DATE(created_at) <= ?";
            $params[] = $dateTo;
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "
            SELECT
                COUNT(*) as total_events,
                COUNT(DISTINCT user_id) as unique_users,
                COUNT(CASE WHEN action = 'LOGIN' THEN 1 END) as logins,
                COUNT(CASE WHEN action = 'CREATE' THEN 1 END) as creates,
                COUNT(CASE WHEN action = 'UPDATE' THEN 1 END) as updates,
                COUNT(CASE WHEN action = 'DELETE' THEN 1 END) as deletes,
                COUNT(CASE WHEN action = 'EXPORT' THEN 1 END) as exports
            FROM audit_logs
            WHERE $whereClause
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user activity summary
     */
    public function getUserActivity($userId, $dateFrom = null, $dateTo = null) {
        $whereConditions = ['user_id = ?'];
        $params = [$userId];

        if ($dateFrom) {
            $whereConditions[] = "DATE(created_at) >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $whereConditions[] = "DATE(created_at) <= ?";
            $params[] = $dateTo;
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "
            SELECT
                action,
                table_name,
                COUNT(*) as count,
                MAX(created_at) as last_activity
            FROM audit_logs
            WHERE $whereClause
            GROUP BY action, table_name
            ORDER BY count DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Clean old audit logs (older than specified days)
     */
    public function cleanOldLogs($daysToKeep = 365) {
        $sql = "DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$daysToKeep]);

        return $stmt->rowCount();
    }

    /**
     * Get current user ID from session
     */
    private function getCurrentUserId() {
        session_start();
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                return trim($ips[0]);
            }
        }

        return 'Unknown';
    }
}

// Global audit logger instance
function getAuditLogger() {
    static $logger = null;
    if ($logger === null) {
        $logger = new AuditLogger();
    }
    return $logger;
}

// Convenience functions
function auditLog($action, $table, $recordId, $oldData = null, $newData = null, $userId = null) {
    return getAuditLogger()->log($action, $table, $recordId, $oldData, $newData, $userId);
}

function auditInvoiceCreated($invoiceId, $invoiceData, $userId = null) {
    return getAuditLogger()->logInvoiceCreated($invoiceId, $invoiceData, $userId);
}

function auditPaymentReceived($invoiceId, $paymentData, $userId = null) {
    return getAuditLogger()->logPaymentReceived($invoiceId, $paymentData, $userId);
}

function auditTestResult($reportId, $resultData, $userId = null) {
    return getAuditLogger()->logTestResultEntered($reportId, $resultData, $userId);
}

function auditStockMovement($itemId, $movementData, $userId = null) {
    return getAuditLogger()->logStockMovement($itemId, $movementData, $userId);
}

function auditUserLogin($userId, $loginData = null) {
    return getAuditLogger()->logUserLogin($userId, $loginData);
}

function auditDataExport($exportType, $filters = [], $userId = null) {
    return getAuditLogger()->logDataExport($exportType, $filters, $userId);
}
?>