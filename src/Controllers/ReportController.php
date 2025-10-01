<?php

namespace App\Controllers;

class ReportController extends BaseController
{
    public function index()
    {
        $db = getDB();

        try {
            $status = $_GET['status'] ?? '';
            $search = $_GET['search'] ?? '';

            $query = "
                SELECT tr.*, i.patient_name, i.patient_phone, t.name as test_name
                FROM test_reports tr
                JOIN invoices i ON tr.invoice_id = i.id
                JOIN tests t ON tr.test_code = t.code
                WHERE 1=1
            ";

            $params = [];

            if ($status) {
                $query .= " AND tr.status = ?";
                $params[] = $status;
            }

            if ($search) {
                $query .= " AND (i.patient_name LIKE ? OR i.patient_phone LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $query .= " ORDER BY tr.created_at DESC LIMIT 50";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $reports = $stmt->fetchAll();

            include __DIR__ . '/../../views/reports/index.php';

        } catch (Exception $e) {
            log_activity("Reports listing error: " . $e->getMessage(), 'error');
            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load reports.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function show()
    {
        // Extract ID from URL
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($uri, '/'));
        $id = end($segments);

        $db = getDB();

        try {
            $stmt = $db->prepare("
                SELECT tr.*, i.patient_name, i.patient_age, i.patient_gender, i.patient_phone,
                       t.name as test_name, t.category,
                       u.full_name as technician_name
                FROM test_reports tr
                JOIN invoices i ON tr.invoice_id = i.id
                JOIN tests t ON tr.test_code = t.code
                LEFT JOIN users u ON tr.technician_id = u.id
                WHERE tr.id = ?
            ");
            $stmt->execute([$id]);
            $report = $stmt->fetch();

            if (!$report) {
                http_response_code(404);
                include __DIR__ . '/../../views/errors/404.php';
                return;
            }

            // Get test results
            $stmt = $db->prepare("
                SELECT * FROM test_results
                WHERE report_id = ? AND is_active = 1
                ORDER BY id
            ");
            $stmt->execute([$id]);
            $results = $stmt->fetchAll();

            include __DIR__ . '/../../views/reports/show.php';

        } catch (Exception $e) {
            log_activity("Report view error: " . $e->getMessage(), 'error');
            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load report.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function edit()
    {
        $this->requireAuth();

        // Extract ID from URL
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($uri, '/'));
        $id = (int)($segments[1] ?? 0);

        if (!$id) {
            redirect('/reports');
            return;
        }

        $db = getDB();

        try {
            // Get report details
            $stmt = $db->prepare("
                SELECT tr.*, i.patient_name, i.patient_age, i.patient_gender, i.patient_phone,
                       t.name as test_name, t.code as test_code, t.category
                FROM test_reports tr
                JOIN invoices i ON tr.invoice_id = i.id
                JOIN tests t ON tr.test_code = t.code
                WHERE tr.id = ?
            ");
            $stmt->execute([$id]);
            $report = $stmt->fetch();

            if (!$report) {
                $this->flashMessage('error', 'Report not found');
                redirect('/reports');
                return;
            }

            // Get test parameters for this test
            $stmt = $db->prepare("
                SELECT * FROM test_parameters
                WHERE test_code = ? AND is_active = 1
                ORDER BY id
            ");
            $stmt->execute([$report['test_code']]);
            $parameters = $stmt->fetchAll();

            // Get existing test results
            $stmt = $db->prepare("
                SELECT * FROM test_results
                WHERE report_id = ? AND is_active = 1
                ORDER BY id
            ");
            $stmt->execute([$id]);
            $existing_results = $stmt->fetchAll();

            // Create a map of existing results by parameter name
            $results_map = [];
            foreach ($existing_results as $result) {
                $results_map[$result['parameter_name']] = $result;
            }

            include __DIR__ . '/../../views/reports/edit.php';

        } catch (Exception $e) {
            log_activity("Report edit page error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load report form.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function update()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/reports');
            return;
        }

        // Validate CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->flashMessage('error', 'Invalid security token');
            redirect('/reports');
            return;
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($uri, '/'));
        $id = (int)($segments[1] ?? 0);

        if (!$id) {
            redirect('/reports');
            return;
        }

        $db = getDB();

        try {
            // Get report
            $stmt = $db->prepare("SELECT * FROM test_reports WHERE id = ?");
            $stmt->execute([$id]);
            $report = $stmt->fetch();

            if (!$report) {
                $this->flashMessage('error', 'Report not found');
                redirect('/reports');
                return;
            }

            // Begin transaction
            $db->beginTransaction();

            // Update report status and notes
            $status = $this->sanitize($_POST['status'] ?? 'in_progress');
            $notes = $this->sanitize($_POST['notes'] ?? '');

            $stmt = $db->prepare("
                UPDATE test_reports SET
                    status = ?,
                    technician_id = ?,
                    notes = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$status, $_SESSION['user_id'], $notes, $id]);

            // Delete existing results to replace them
            $stmt = $db->prepare("DELETE FROM test_results WHERE report_id = ?");
            $stmt->execute([$id]);

            // Insert new results
            $parameters = $_POST['parameters'] ?? [];
            $values = $_POST['values'] ?? [];
            $units = $_POST['units'] ?? [];
            $ranges = $_POST['ranges'] ?? [];

            foreach ($parameters as $index => $parameter) {
                $value = $values[$index] ?? '';
                $unit = $units[$index] ?? '';
                $range = $ranges[$index] ?? '';

                if (!empty($parameter) && !empty($value)) {
                    // Simple abnormality check (can be enhanced)
                    $is_abnormal = false;

                    $stmt = $db->prepare("
                        INSERT INTO test_results (
                            report_id, parameter_name, value, unit, normal_range, is_abnormal
                        ) VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $id,
                        $this->sanitize($parameter),
                        $this->sanitize($value),
                        $this->sanitize($unit),
                        $this->sanitize($range),
                        $is_abnormal
                    ]);
                }
            }

            $db->commit();

            log_activity("Updated test report #{$id}", 'info', [
                'report_id' => $id,
                'status' => $status,
                'user_id' => $_SESSION['user_id']
            ]);

            $this->flashMessage('success', 'Report updated successfully!');
            redirect("/reports/{$id}");

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            log_activity("Report update error: " . $e->getMessage(), 'error');

            $this->flashMessage('error', 'Failed to update report. Please try again.');
            redirect("/reports/{$id}/edit");
        }
    }
}