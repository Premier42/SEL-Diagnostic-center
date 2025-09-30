<?php

namespace App\Controllers;

class InvoiceController extends BaseController
{
    public function index()
    {
        $this->requireAuth();

        $db = getDB();
        $page = (int)($_GET['page'] ?? 1);
        $limit = 25;
        $offset = ($page - 1) * $limit;

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        try {
            // Build query with filters
            $whereConditions = [];
            $params = [];

            if (!empty($search)) {
                $whereConditions[] = "(patient_name LIKE ? OR patient_phone LIKE ? OR id = ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
                $params[] = $search;
            }

            if (!empty($status)) {
                $whereConditions[] = "payment_status = ?";
                $params[] = $status;
            }

            $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            // Get total count for pagination
            $countQuery = "SELECT COUNT(*) FROM invoices $whereClause";
            $stmt = $db->prepare($countQuery);
            $stmt->execute($params);
            $totalCount = $stmt->fetchColumn();

            // Get invoices with pagination
            $query = "
                SELECT i.*, d.name as doctor_name
                FROM invoices i
                LEFT JOIN doctors d ON i.doctor_id = d.id
                $whereClause
                ORDER BY i.created_at DESC
                LIMIT $limit OFFSET $offset
            ";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $invoices = $stmt->fetchAll();

            // Calculate pagination
            $totalPages = ceil($totalCount / $limit);

            // Get summary statistics
            $statsQuery = "
                SELECT
                    COUNT(*) as total,
                    SUM(total_amount) as total_amount,
                    SUM(amount_paid) as amount_paid,
                    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) as partial_count
                FROM invoices $whereClause
            ";

            $stmt = $db->prepare($statsQuery);
            $stmt->execute($params);
            $stats = $stmt->fetch();

            include __DIR__ . '/../../views/invoices/index.php';

        } catch (Exception $e) {
            log_activity("Invoice listing error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load invoices.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function create()
    {
        $this->requireAuth();

        $db = getDB();

        try {
            // Get doctors for dropdown
            $stmt = $db->query("SELECT id, name FROM doctors WHERE is_active = 1 ORDER BY name");
            $doctors = $stmt->fetchAll();

            // Get tests for selection
            $stmt = $db->query("SELECT code, name, category, price FROM tests WHERE is_active = 1 ORDER BY category, name");
            $tests = $stmt->fetchAll();

            // Group tests by category
            $tests_by_category = [];
            foreach ($tests as $test) {
                $tests_by_category[$test['category']][] = $test;
            }

            include __DIR__ . '/../../views/invoices/create.php';

        } catch (Exception $e) {
            log_activity("Invoice create page error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load invoice form.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function store()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/invoices');
            return;
        }

        // Validate CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->flashMessage('error', 'Invalid security token');
            redirect('/invoices/create');
            return;
        }

        $db = getDB();

        try {
            $db->beginTransaction();

            // Validate required fields
            $errors = $this->validate($_POST, [
                'patient_name' => 'required',
                'patient_phone' => 'required',
                'patient_age' => 'numeric',
                'tests' => 'required'
            ]);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                redirect('/invoices/create');
                return;
            }

            // Calculate totals
            $selectedTests = $_POST['tests'] ?? [];
            $total_amount = 0;

            if (!empty($selectedTests)) {
                $placeholders = str_repeat('?,', count($selectedTests) - 1) . '?';
                $stmt = $db->prepare("SELECT code, name, price FROM tests WHERE code IN ($placeholders)");
                $stmt->execute($selectedTests);
                $test_details = $stmt->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);

                foreach ($selectedTests as $testCode) {
                    if (isset($test_details[$testCode])) {
                        $total_amount += $test_details[$testCode][0]['price'];
                    }
                }
            }

            $discount_amount = (float)($_POST['discount_amount'] ?? 0);
            $amount_paid = (float)($_POST['amount_paid'] ?? 0);
            $final_amount = $total_amount - $discount_amount;

            // Determine payment status
            $payment_status = 'pending';
            if ($amount_paid >= $final_amount) {
                $payment_status = 'paid';
            } elseif ($amount_paid > 0) {
                $payment_status = 'partial';
            }

            // Insert invoice
            $stmt = $db->prepare("
                INSERT INTO invoices (
                    patient_name, patient_age, patient_gender, patient_phone,
                    patient_email, patient_address, doctor_id, total_amount,
                    discount_amount, amount_paid, payment_status, payment_method, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $this->sanitize($_POST['patient_name']),
                (int)($_POST['patient_age'] ?? 0),
                $this->sanitize($_POST['patient_gender'] ?? ''),
                $this->sanitize($_POST['patient_phone']),
                $this->sanitize($_POST['patient_email'] ?? ''),
                $this->sanitize($_POST['patient_address'] ?? ''),
                (int)($_POST['doctor_id'] ?? 0) ?: null,
                $total_amount,
                $discount_amount,
                $amount_paid,
                $payment_status,
                $this->sanitize($_POST['payment_method'] ?? ''),
                $this->sanitize($_POST['notes'] ?? '')
            ]);

            $invoice_id = $db->lastInsertId();

            // Insert invoice tests
            if (!empty($selectedTests)) {
                $stmt = $db->prepare("
                    INSERT INTO invoice_tests (invoice_id, test_code, test_name, price)
                    SELECT ?, code, name, price FROM tests WHERE code = ?
                ");

                foreach ($selectedTests as $testCode) {
                    $stmt->execute([$invoice_id, $testCode]);
                }
            }

            $db->commit();

            log_activity("Created invoice #$invoice_id for patient: " . $_POST['patient_name'], 'info', [
                'invoice_id' => $invoice_id,
                'user_id' => $_SESSION['user_id']
            ]);

            $this->flashMessage('success', 'Invoice created successfully!');
            redirect("/invoices/$invoice_id");

        } catch (Exception $e) {
            $db->rollBack();
            log_activity("Invoice creation error: " . $e->getMessage(), 'error');

            $this->flashMessage('error', 'Failed to create invoice. Please try again.');
            $_SESSION['old'] = $_POST;
            redirect('/invoices/create');
        }
    }

    public function show()
    {
        $this->requireAuth();

        $uri = $_SERVER['REQUEST_URI'];
        $id = (int)basename(parse_url($uri, PHP_URL_PATH));

        if (!$id) {
            redirect('/invoices');
            return;
        }

        $db = getDB();

        try {
            // Get invoice details
            $stmt = $db->prepare("
                SELECT i.*, d.name as doctor_name, d.qualifications, d.workplace
                FROM invoices i
                LEFT JOIN doctors d ON i.doctor_id = d.id
                WHERE i.id = ?
            ");
            $stmt->execute([$id]);
            $invoice = $stmt->fetch();

            if (!$invoice) {
                $this->flashMessage('error', 'Invoice not found');
                redirect('/invoices');
                return;
            }

            // Get invoice tests
            $stmt = $db->prepare("
                SELECT test_code, test_name, price
                FROM invoice_tests
                WHERE invoice_id = ?
                ORDER BY test_name
            ");
            $stmt->execute([$id]);
            $invoice_tests = $stmt->fetchAll();

            include __DIR__ . '/../../views/invoices/show.php';

        } catch (Exception $e) {
            log_activity("Invoice view error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load invoice.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function pdf()
    {
        $this->requireAuth();

        $uri = $_SERVER['REQUEST_URI'];
        $segments = explode('/', trim($uri, '/'));
        $id = (int)($segments[1] ?? 0);

        if (!$id) {
            redirect('/invoices');
            return;
        }

        $db = getDB();

        try {
            // Get invoice details
            $stmt = $db->prepare("
                SELECT i.*, d.name as doctor_name, d.qualifications, d.workplace, d.phone as doctor_phone
                FROM invoices i
                LEFT JOIN doctors d ON i.doctor_id = d.id
                WHERE i.id = ?
            ");
            $stmt->execute([$id]);
            $invoice = $stmt->fetch();

            if (!$invoice) {
                $this->flashMessage('error', 'Invoice not found');
                redirect('/invoices');
                return;
            }

            // Get invoice tests
            $stmt = $db->prepare("
                SELECT test_code, test_name, price
                FROM invoice_tests
                WHERE invoice_id = ?
                ORDER BY test_name
            ");
            $stmt->execute([$id]);
            $invoice_tests = $stmt->fetchAll();

            // Set headers for PDF download
            header('Content-Type: text/html; charset=utf-8');

            include __DIR__ . '/../../views/invoices/pdf.php';

        } catch (Exception $e) {
            log_activity("Invoice PDF error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to generate invoice PDF.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function updatePayment()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/invoices');
            return;
        }

        // Validate CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->flashMessage('error', 'Invalid security token');
            redirect('/invoices');
            return;
        }

        $uri = $_SERVER['REQUEST_URI'];
        $segments = explode('/', trim($uri, '/'));
        $id = (int)($segments[1] ?? 0);

        if (!$id) {
            redirect('/invoices');
            return;
        }

        $db = getDB();

        try {
            // Validate required fields
            $errors = $this->validate($_POST, [
                'amount_paid' => 'required|numeric',
                'payment_status' => 'required',
            ]);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                redirect("/invoices/{$id}");
                return;
            }

            // Get invoice to validate amounts
            $stmt = $db->prepare("SELECT total_amount, discount_amount, amount_paid FROM invoices WHERE id = ?");
            $stmt->execute([$id]);
            $invoice = $stmt->fetch();

            if (!$invoice) {
                $this->flashMessage('error', 'Invoice not found');
                redirect('/invoices');
                return;
            }

            $newAmountPaid = (float)$_POST['amount_paid'];
            $finalAmount = $invoice['total_amount'] - ($invoice['discount_amount'] ?? 0);

            // Validate amount
            if ($newAmountPaid < 0 || $newAmountPaid > $finalAmount) {
                $this->flashMessage('error', 'Invalid payment amount');
                redirect("/invoices/{$id}");
                return;
            }

            // Determine payment status
            $paymentStatus = $_POST['payment_status'];
            if ($newAmountPaid == 0) {
                $paymentStatus = 'pending';
            } elseif ($newAmountPaid >= $finalAmount) {
                $paymentStatus = 'paid';
            } else {
                $paymentStatus = 'partial';
            }

            // Update invoice
            $stmt = $db->prepare("
                UPDATE invoices SET
                    amount_paid = ?,
                    payment_status = ?,
                    payment_method = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $newAmountPaid,
                $paymentStatus,
                $this->sanitize($_POST['payment_method'] ?? NULL),
                $id
            ]);

            log_activity("Updated payment for invoice #{$id}", 'info', [
                'invoice_id' => $id,
                'amount_paid' => $newAmountPaid,
                'payment_status' => $paymentStatus,
                'user_id' => $_SESSION['user_id']
            ]);

            $this->flashMessage('success', 'Payment updated successfully!');
            redirect("/invoices/{$id}");

        } catch (Exception $e) {
            log_activity("Payment update error: " . $e->getMessage(), 'error');

            $this->flashMessage('error', 'Failed to update payment. Please try again.');
            redirect("/invoices/{$id}");
        }
    }
}
