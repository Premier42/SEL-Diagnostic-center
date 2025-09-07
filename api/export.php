<?php
// Data Export API endpoint

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../utils/export.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $exporter = new DataExporter();

    if ($method === 'GET') {
        $type = $_GET['type'] ?? '';
        $format = $_GET['format'] ?? 'csv';

        switch ($type) {
            case 'invoices':
                $filters = [
                    'status' => $_GET['status'] ?? '',
                    'date_from' => $_GET['date_from'] ?? '',
                    'date_to' => $_GET['date_to'] ?? ''
                ];
                $result = $exporter->exportInvoices($filters);
                break;

            case 'reports':
                $filters = [
                    'status' => $_GET['status'] ?? '',
                    'date_from' => $_GET['date_from'] ?? '',
                    'date_to' => $_GET['date_to'] ?? ''
                ];
                $result = $exporter->exportReports($filters);
                break;

            case 'inventory':
                $filters = [
                    'category' => $_GET['category'] ?? '',
                    'supplier' => $_GET['supplier'] ?? '',
                    'low_stock_only' => $_GET['low_stock_only'] ?? false
                ];
                $result = $exporter->exportInventory($filters);
                break;

            case 'financial':
                $result = $exporter->exportFinancialSummary(
                    $_GET['date_from'] ?? null,
                    $_GET['date_to'] ?? null
                );
                break;

            case 'doctors':
                $result = $exporter->exportDoctorStats(
                    $_GET['date_from'] ?? null,
                    $_GET['date_to'] ?? null
                );
                break;

            case 'all':
                $result = $exporter->exportAllData();
                if (isset($result['success']) && $result['success']) {
                    // For ZIP files, we need to handle differently
                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                    readfile($result['file_path']);
                    unlink($result['file_path']); // Clean up temp file
                    exit;
                }
                break;

            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid export type']);
                exit;
        }

        if (isset($result['error'])) {
            http_response_code(400);
            echo json_encode($result);
        } elseif (isset($result['success']) && $result['success']) {
            // For CSV files
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
            echo $result['data'];
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Export failed']);
        }

    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>