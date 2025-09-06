<?php

namespace App\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Session\SessionManager;

class AuditService
{
    private DatabaseManager $db;
    private SessionManager $session;

    public function __construct(DatabaseManager $db, SessionManager $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

    public function logChange(string $table, string $action, $recordId, array $oldData = [], array $newData = []): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO audit_log (table_name, action, record_id, old_data, new_data, user_id, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $table,
                $action,
                $recordId,
                json_encode($oldData),
                json_encode($newData),
                $this->session->getUserId()
            ]);
        } catch (\Exception $e) {
            error_log("Audit logging failed: " . $e->getMessage());
        }
    }

    public function getAuditHistory(string $table, $recordId, int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT al.*, u.username 
            FROM audit_log al 
            LEFT JOIN users u ON al.user_id = u.id 
            WHERE al.table_name = ? AND al.record_id = ? 
            ORDER BY al.created_at DESC 
            LIMIT ?
        ");
        
        $stmt->execute([$table, $recordId, $limit]);
        return $stmt->fetchAll();
    }
}
