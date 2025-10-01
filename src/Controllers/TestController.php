<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function index()
    {
        $this->requireAuth();

        $db = getDB();
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';

        try {
            // Build query with filters
            $whereConditions = ['is_active = 1'];
            $params = [];

            if (!empty($search)) {
                $whereConditions[] = "(name LIKE ? OR code LIKE ?)";
                $params[] = "%$search%";
                $params[] = "%$search%";
            }

            if (!empty($category)) {
                $whereConditions[] = "category = ?";
                $params[] = $category;
            }

            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

            // Get tests
            $query = "
                SELECT *
                FROM tests
                $whereClause
                ORDER BY category, name
            ";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $tests = $stmt->fetchAll();

            // Get categories for filter
            $stmt = $db->query("SELECT DISTINCT category FROM tests WHERE is_active = 1 ORDER BY category");
            $categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            // Group tests by category
            $tests_by_category = [];
            foreach ($tests as $test) {
                $tests_by_category[$test['category']][] = $test;
            }

            // Get statistics
            $statsQuery = "
                SELECT
                    COUNT(*) as total,
                    COUNT(DISTINCT category) as categories,
                    AVG(price) as avg_price,
                    MIN(price) as min_price,
                    MAX(price) as max_price
                FROM tests
                WHERE is_active = 1
            ";

            $stmt = $db->query($statsQuery);
            $stats = $stmt->fetch();

            include __DIR__ . '/../../views/tests/index.php';

        } catch (Exception $e) {
            log_activity("Test listing error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load tests.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function create()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            return;
        }

        $db = getDB();

        try {
            // Get existing categories
            $stmt = $db->query("SELECT DISTINCT category FROM tests ORDER BY category");
            $categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            include __DIR__ . '/../../views/tests/create.php';

        } catch (Exception $e) {
            log_activity("Test create page error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load test form.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function store()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->json(['error' => 'Insufficient permissions'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tests');
            return;
        }

        // Validate CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->flashMessage('error', 'Invalid security token');
            redirect('/tests/create');
            return;
        }

        $db = getDB();

        try {
            // Validate required fields
            $errors = $this->validate($_POST, [
                'code' => 'required',
                'name' => 'required',
                'category' => 'required',
                'price' => 'required|numeric'
            ]);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                redirect('/tests/create');
                return;
            }

            // Check if test code already exists
            $stmt = $db->prepare("SELECT id FROM tests WHERE code = ?");
            $stmt->execute([$_POST['code']]);
            if ($stmt->fetch()) {
                $_SESSION['errors'] = ['code' => ['Test code already exists']];
                $_SESSION['old'] = $_POST;
                redirect('/tests/create');
                return;
            }

            // Insert test
            $stmt = $db->prepare("
                INSERT INTO tests (
                    code, name, category, price, description,
                    sample_type, turnaround_time, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)
            ");

            $stmt->execute([
                strtoupper($this->sanitize($_POST['code'])),
                $this->sanitize($_POST['name']),
                $this->sanitize($_POST['category']),
                (float)$_POST['price'],
                $this->sanitize($_POST['description'] ?? ''),
                $this->sanitize($_POST['sample_type'] ?? ''),
                $this->sanitize($_POST['turnaround_time'] ?? '')
            ]);

            $test_id = $db->lastInsertId();

            log_activity("Created test: " . $_POST['name'], 'info', [
                'test_id' => $test_id,
                'test_code' => $_POST['code'],
                'user_id' => $_SESSION['user_id']
            ]);

            $this->flashMessage('success', 'Test created successfully!');
            redirect('/tests');

        } catch (Exception $e) {
            log_activity("Test creation error: " . $e->getMessage(), 'error');

            $this->flashMessage('error', 'Failed to create test. Please try again.');
            $_SESSION['old'] = $_POST;
            redirect('/tests/create');
        }
    }

    public function show()
    {
        $this->requireAuth();

        $uri = $_SERVER['REQUEST_URI'];
        $code = basename(parse_url($uri, PHP_URL_PATH));

        if (!$code) {
            redirect('/tests');
            return;
        }

        $db = getDB();

        try {
            // Get test details
            $stmt = $db->prepare("SELECT * FROM tests WHERE code = ? AND is_active = 1");
            $stmt->execute([$code]);
            $test = $stmt->fetch();

            if (!$test) {
                $this->flashMessage('error', 'Test not found');
                redirect('/tests');
                return;
            }

            // Get usage statistics
            $stmt = $db->prepare("
                SELECT
                    COUNT(*) as total_orders,
                    SUM(price) as total_revenue,
                    COUNT(DISTINCT invoice_id) as unique_patients
                FROM invoice_tests
                WHERE test_code = ?
            ");
            $stmt->execute([$code]);
            $usage_stats = $stmt->fetch();

            // Get recent orders
            $stmt = $db->prepare("
                SELECT i.id, i.patient_name, i.created_at, it.price
                FROM invoice_tests it
                JOIN invoices i ON it.invoice_id = i.id
                WHERE it.test_code = ?
                ORDER BY i.created_at DESC
                LIMIT 10
            ");
            $stmt->execute([$code]);
            $recent_orders = $stmt->fetchAll();

            include __DIR__ . '/../../views/tests/show.php';

        } catch (Exception $e) {
            log_activity("Test view error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load test details.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function edit()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            return;
        }

        $uri = $_SERVER['REQUEST_URI'];
        $segments = explode('/', trim($uri, '/'));
        $code = $segments[1] ?? null;

        if (!$code) {
            redirect('/tests');
            return;
        }

        $db = getDB();

        try {
            // Get test details
            $stmt = $db->prepare("SELECT * FROM tests WHERE code = ?");
            $stmt->execute([$code]);
            $test = $stmt->fetch();

            if (!$test) {
                $this->flashMessage('error', 'Test not found');
                redirect('/tests');
                return;
            }

            // Get existing categories
            $stmt = $db->query("SELECT DISTINCT category FROM tests ORDER BY category");
            $categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            include __DIR__ . '/../../views/tests/edit.php';

        } catch (Exception $e) {
            log_activity("Test edit page error: " . $e->getMessage(), 'error');

            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load test form.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function update()
    {
        $this->requireAuth();

        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->json(['error' => 'Insufficient permissions'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tests');
            return;
        }

        // Validate CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->flashMessage('error', 'Invalid security token');
            redirect('/tests');
            return;
        }

        $code = $_POST['code'] ?? null;
        if (!$code) {
            redirect('/tests');
            return;
        }

        $db = getDB();

        try {
            // Validate required fields
            $errors = $this->validate($_POST, [
                'name' => 'required',
                'category' => 'required',
                'price' => 'required|numeric'
            ]);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                redirect("/tests/{$code}/edit");
                return;
            }

            // Update test
            $stmt = $db->prepare("
                UPDATE tests SET
                    name = ?,
                    category = ?,
                    price = ?,
                    description = ?,
                    sample_type = ?,
                    turnaround_time = ?,
                    is_active = ?
                WHERE code = ?
            ");

            $stmt->execute([
                $this->sanitize($_POST['name']),
                $this->sanitize($_POST['category']),
                (float)$_POST['price'],
                $this->sanitize($_POST['description'] ?? ''),
                $this->sanitize($_POST['sample_type'] ?? ''),
                $this->sanitize($_POST['turnaround_time'] ?? ''),
                isset($_POST['is_active']) ? 1 : 0,
                $code
            ]);

            log_activity("Updated test: {$_POST['name']}", 'info', [
                'test_code' => $code,
                'user_id' => $_SESSION['user_id']
            ]);

            $this->flashMessage('success', 'Test updated successfully!');
            redirect('/tests');

        } catch (Exception $e) {
            log_activity("Test update error: " . $e->getMessage(), 'error');

            $this->flashMessage('error', 'Failed to update test. Please try again.');
            $_SESSION['old'] = $_POST;
            redirect("/tests/{$code}/edit");
        }
    }
}
