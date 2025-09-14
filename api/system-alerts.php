<?php
// System Alerts API endpoint

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $pdo = getDBConnection();
    $alerts = [];
    
    // Check for low stock items
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM consumables 
        WHERE current_stock <= minimum_level AND current_stock > 0
    ");
    $lowStock = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($lowStock > 0) {
        $alerts[] = [
            'type' => 'warning',
            'message' => "{$lowStock} inventory items are running low on stock"
        ];
    }
    
    // Check for out of stock items
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM consumables 
        WHERE current_stock = 0
    ");
    $outOfStock = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($outOfStock > 0) {
        $alerts[] = [
            'type' => 'danger',
            'message' => "{$outOfStock} inventory items are out of stock"
        ];
    }
    
    // Check for pending reports
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM test_reports 
        WHERE status = 'pending'
    ");
    $pendingReports = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($pendingReports > 10) {
        $alerts[] = [
            'type' => 'info',
            'message' => "{$pendingReports} test reports are pending processing"
        ];
    }
    
    // Check for overdue invoices (older than 30 days)
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM invoices 
        WHERE payment_status != 'paid' 
        AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $overdueInvoices = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($overdueInvoices > 0) {
        $alerts[] = [
            'type' => 'warning',
            'message' => "{$overdueInvoices} invoices are overdue (>30 days)"
        ];
    }
    
    // Check for expiring items (within 30 days)
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM consumables 
        WHERE expiry_date IS NOT NULL 
        AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
        AND expiry_date > CURDATE()
    ");
    $expiringItems = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($expiringItems > 0) {
        $alerts[] = [
            'type' => 'warning',
            'message' => "{$expiringItems} inventory items expire within 30 days"
        ];
    }
    
    // Check for expired items
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM consumables 
        WHERE expiry_date IS NOT NULL 
        AND expiry_date <= CURDATE()
    ");
    $expiredItems = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($expiredItems > 0) {
        $alerts[] = [
            'type' => 'danger',
            'message' => "{$expiredItems} inventory items have expired"
        ];
    }
    
    echo json_encode($alerts);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
