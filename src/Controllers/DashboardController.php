<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = getDB();

        try {
            // Get dashboard statistics
            $stats = [];

            // Total invoices
            $stmt = $db->query("SELECT COUNT(*) as total FROM invoices");
            $stats['total_invoices'] = $stmt->fetchColumn();

            // Total revenue
            $stmt = $db->query("SELECT SUM(amount_paid) as total FROM invoices");
            $stats['total_revenue'] = $stmt->fetchColumn() ?: 0;

            // Pending invoices
            $stmt = $db->query("SELECT COUNT(*) as total FROM invoices WHERE payment_status = 'pending'");
            $stats['pending_invoices'] = $stmt->fetchColumn();

            // Total tests
            $stmt = $db->query("SELECT COUNT(*) as total FROM tests WHERE is_active = 1");
            $stats['total_tests'] = $stmt->fetchColumn();

            // Total reports
            $stmt = $db->query("SELECT COUNT(*) as total FROM test_reports");
            $stats['total_reports'] = $stmt->fetchColumn();

            // Completed reports today
            $stmt = $db->query("SELECT COUNT(*) as total FROM test_reports WHERE DATE(created_at) = CURDATE() AND status = 'completed'");
            $stats['reports_today'] = $stmt->fetchColumn();

            // Total doctors
            $stmt = $db->query("SELECT COUNT(*) as total FROM doctors WHERE is_active = 1");
            $stats['total_doctors'] = $stmt->fetchColumn();

            // Total users
            $stmt = $db->query("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
            $stats['total_users'] = $stmt->fetchColumn();

            // Recent invoices
            $stmt = $db->query("
                SELECT id, patient_name, patient_phone, total_amount, payment_status, created_at
                FROM invoices
                ORDER BY created_at DESC
                LIMIT 5
            ");
            $recent_invoices = $stmt->fetchAll();

            // Pending reports
            $stmt = $db->query("
                SELECT tr.id, tr.test_code, i.patient_name, tr.status, tr.created_at
                FROM test_reports tr
                JOIN invoices i ON tr.invoice_id = i.id
                WHERE tr.status IN ('pending', 'in_progress')
                ORDER BY tr.created_at ASC
                LIMIT 5
            ");
            $pending_reports = $stmt->fetchAll();

            // Monthly revenue chart data
            $stmt = $db->query("
                SELECT
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(amount_paid) as revenue
                FROM invoices
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month
            ");
            $monthly_revenue = $stmt->fetchAll();

            // Test category distribution
            $stmt = $db->query("
                SELECT
                    t.category,
                    COUNT(it.id) as count
                FROM tests t
                LEFT JOIN invoice_tests it ON t.code = it.test_code
                WHERE t.is_active = 1
                GROUP BY t.category
                ORDER BY count DESC
                LIMIT 5
            ");
            $test_categories = $stmt->fetchAll();

            include __DIR__ . '/../../views/dashboard/index.php';

        } catch (Exception $e) {
            log_activity("Dashboard error: " . $e->getMessage(), 'error');

            // Show error page
            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load dashboard data.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }
}
