<?php

namespace App\Controllers;

class AuditController extends BaseController
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
        $page = (int)($_GET['page'] ?? 1);
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        $user = $_GET['user'] ?? '';

        try {
            // Build query with filters
            $whereConditions = [];
            $params = [];

            if (!empty($search)) {
                $whereConditions[] = "(action LIKE ? OR details LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            if (!empty($type)) {
                $whereConditions[] = "type = ?";
                $params[] = $type;
            }

            if (!empty($user)) {
                $whereConditions[] = "user_id = ?";
                $params[] = $user;
            }

            $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Get total count for pagination
            $countQuery = "SELECT COUNT(*) FROM audit_logs $whereClause";
            $stmt = $db->prepare($countQuery);
            $stmt->execute($params);
            $totalCount = $stmt->fetchColumn();

            // Get audit logs with pagination
            $query = "
                SELECT al.*, u.full_name as user_name
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id
                $whereClause
                ORDER BY al.created_at DESC
                LIMIT $limit OFFSET $offset
            ";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $logs = $stmt->fetchAll();

            // Calculate pagination
            $totalPages = ceil($totalCount / $limit);

            // Get users for filter
            $stmt = $db->query("SELECT id, full_name as name FROM users ORDER BY full_name");
            $users = $stmt->fetchAll();

            // Get log types for filter
            $stmt = $db->query("SELECT DISTINCT action FROM audit_logs ORDER BY action");
            $types = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            include __DIR__ . '/../../views/audit/index.php';

        } catch (Exception $e) {
            log_activity("Audit log viewing error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load audit logs.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }
}