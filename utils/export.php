<?php
// Data Export Utilities

require_once '../config/database.php';

class DataExporter {
    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    // Export invoices to CSV
    public function exportInvoices($filters = []) {
        $whereConditions = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $whereConditions[] = "i.payment_status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $whereConditions[] = "DATE(i.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = "DATE(i.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "
            SELECT
                CONCAT('#', LPAD(i.id, 4, '0')) as invoice_number,
                i.patient_name,
                i.patient_age,
                i.patient_gender,
                i.patient_phone,
                d.name as doctor_name,
                d.workplace as doctor_workplace,
                i.total_amount,
                i.amount_paid,
                i.discount_amount,
                i.payment_status,
                i.notes,
                DATE(i.created_at) as invoice_date,
                GROUP_CONCAT(CONCAT(t.name, ' (', it.price, ')') SEPARATOR '; ') as tests
            FROM invoices i
            LEFT JOIN doctors d ON i.doctor_id = d.id
            LEFT JOIN invoice_tests it ON i.id = it.invoice_id
            LEFT JOIN tests t ON it.test_code = t.code
            WHERE $whereClause
            GROUP BY i.id
            ORDER BY i.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->generateCSV($data, 'invoices');
    }

    // Export test reports to CSV
    public function exportReports($filters = []) {
        $whereConditions = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $whereConditions[] = "tr.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $whereConditions[] = "DATE(tr.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $whereConditions[] = "DATE(tr.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "
            SELECT
                CONCAT('#', LPAD(tr.invoice_id, 4, '0')) as invoice_number,
                i.patient_name,
                i.patient_age,
                i.patient_gender,
                tr.test_code,
                t.name as test_name,
                tr.status,
                u.full_name as technician_name,
                tr.notes,
                DATE(tr.created_at) as report_date,
                GROUP_CONCAT(
                    CONCAT(res.parameter_name, ': ', res.value, ' ', res.unit,
                           CASE WHEN res.is_abnormal = 1 THEN ' (ABNORMAL)' ELSE '' END)
                    SEPARATOR '; '
                ) as results
            FROM test_reports tr
            LEFT JOIN invoices i ON tr.invoice_id = i.id
            LEFT JOIN tests t ON tr.test_code = t.code
            LEFT JOIN users u ON tr.technician_id = u.id
            LEFT JOIN test_results res ON tr.id = res.report_id AND res.is_active = 1
            WHERE $whereClause
            GROUP BY tr.id
            ORDER BY tr.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->generateCSV($data, 'test_reports');
    }

    // Export inventory to CSV
    public function exportInventory($filters = []) {
        $whereConditions = ['1=1'];
        $params = [];

        if (!empty($filters['category'])) {
            $whereConditions[] = "category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['supplier'])) {
            $whereConditions[] = "supplier = ?";
            $params[] = $filters['supplier'];
        }

        if (!empty($filters['low_stock_only'])) {
            $whereConditions[] = "current_stock <= minimum_level";
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "
            SELECT
                item_name,
                item_code,
                category,
                current_stock,
                unit,
                minimum_level,
                maximum_level,
                unit_price,
                ROUND(current_stock * unit_price, 2) as total_value,
                supplier,
                expiry_date,
                CASE
                    WHEN current_stock <= 0 THEN 'Out of Stock'
                    WHEN current_stock <= minimum_level THEN 'Low Stock'
                    ELSE 'In Stock'
                END as stock_status,
                description
            FROM consumables
            WHERE $whereClause
            ORDER BY item_name
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->generateCSV($data, 'inventory');
    }

    // Export financial summary
    public function exportFinancialSummary($dateFrom = null, $dateTo = null) {
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
                DATE(created_at) as date,
                COUNT(*) as total_invoices,
                SUM(total_amount) as gross_revenue,
                SUM(discount_amount) as total_discounts,
                SUM(amount_paid) as collections,
                SUM(total_amount - amount_paid) as outstanding,
                COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as paid_invoices,
                COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_invoices,
                COUNT(CASE WHEN payment_status = 'partial' THEN 1 END) as partial_invoices
            FROM invoices
            WHERE $whereClause
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->generateCSV($data, 'financial_summary');
    }

    // Export doctor referral statistics
    public function exportDoctorStats($dateFrom = null, $dateTo = null) {
        $whereConditions = ['1=1'];
        $params = [];

        if ($dateFrom) {
            $whereConditions[] = "DATE(i.created_at) >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $whereConditions[] = "DATE(i.created_at) <= ?";
            $params[] = $dateTo;
        }

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "
            SELECT
                d.name as doctor_name,
                d.qualifications,
                d.workplace,
                d.phone,
                d.email,
                COUNT(i.id) as total_referrals,
                SUM(i.total_amount) as total_revenue,
                SUM(i.amount_paid) as collections,
                AVG(i.total_amount) as avg_invoice_value,
                COUNT(CASE WHEN i.payment_status = 'paid' THEN 1 END) as paid_invoices,
                COUNT(CASE WHEN i.payment_status = 'pending' THEN 1 END) as pending_invoices
            FROM doctors d
            LEFT JOIN invoices i ON d.id = i.doctor_id
            WHERE $whereClause
            GROUP BY d.id
            HAVING total_referrals > 0
            ORDER BY total_referrals DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->generateCSV($data, 'doctor_statistics');
    }

    // Generate CSV from data array
    private function generateCSV($data, $filename) {
        if (empty($data)) {
            return ['error' => 'No data to export'];
        }

        $output = fopen('php://temp', 'r+');

        // Add headers
        fputcsv($output, array_keys($data[0]));

        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return [
            'success' => true,
            'filename' => $filename . '_' . date('Y-m-d_H-i-s') . '.csv',
            'data' => $csv,
            'rows' => count($data)
        ];
    }

    // Export all data as ZIP
    public function exportAllData() {
        $exports = [];

        // Export all main data types
        $exports['invoices'] = $this->exportInvoices();
        $exports['reports'] = $this->exportReports();
        $exports['inventory'] = $this->exportInventory();
        $exports['financial_summary'] = $this->exportFinancialSummary();
        $exports['doctor_stats'] = $this->exportDoctorStats();

        // Create ZIP file
        $zipFile = tempnam(sys_get_temp_dir(), 'lab_export_');
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
            foreach ($exports as $type => $export) {
                if (isset($export['success']) && $export['success']) {
                    $zip->addFromString($export['filename'], $export['data']);
                }
            }
            $zip->close();

            return [
                'success' => true,
                'filename' => 'lab_complete_export_' . date('Y-m-d_H-i-s') . '.zip',
                'file_path' => $zipFile
            ];
        }

        return ['error' => 'Failed to create ZIP file'];
    }
}
?>