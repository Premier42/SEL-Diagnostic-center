<?php
// Dashboard Statistics API endpoint

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $pdo = getDBConnection();
    
    // Get basic counts
    $stats = [];
    
    // Total invoices
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM invoices");
    $stats['invoices'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total tests
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tests WHERE is_active = 1");
    $stats['tests'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total reports
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM test_reports");
    $stats['reports'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Total doctors
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM doctors WHERE is_active = 1");
    $stats['doctors'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Revenue statistics
    $stmt = $pdo->query("
        SELECT 
            SUM(total_amount) as total_revenue,
            SUM(amount_paid) as collected_amount,
            SUM(total_amount - amount_paid) as pending_amount,
            COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as paid_invoices,
            COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_invoices
        FROM invoices
    ");
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stats['revenue'] = [
        'total' => floatval($revenue['total_revenue'] ?? 0),
        'collected' => floatval($revenue['collected_amount'] ?? 0),
        'pending' => floatval($revenue['pending_amount'] ?? 0),
        'paid_invoices' => intval($revenue['paid_invoices'] ?? 0),
        'pending_invoices' => intval($revenue['pending_invoices'] ?? 0)
    ];
    
    // Today's statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as today_invoices,
            SUM(total_amount) as today_revenue
        FROM invoices 
        WHERE DATE(created_at) = CURDATE()
    ");
    $today = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stats['today'] = [
        'invoices' => intval($today['today_invoices'] ?? 0),
        'revenue' => floatval($today['today_revenue'] ?? 0)
    ];
    
    // Monthly statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as month_invoices,
            SUM(total_amount) as month_revenue
        FROM invoices 
        WHERE YEAR(created_at) = YEAR(CURDATE()) 
        AND MONTH(created_at) = MONTH(CURDATE())
    ");
    $month = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stats['month'] = [
        'invoices' => intval($month['month_invoices'] ?? 0),
        'revenue' => floatval($month['month_revenue'] ?? 0)
    ];
    
    echo json_encode($stats);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
