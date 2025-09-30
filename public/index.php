<?php
/**
 * SEL Diagnostic Center - Main Entry Point
 * Modern routing system with clean URLs
 */

require_once __DIR__ . '/../bootstrap.php';

// Simple router
class Router {
    private array $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove query string and normalize
        $uri = rtrim($uri, '/') ?: '/';

        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            $handler = $this->routes[$method][$uri];
            return $this->executeHandler($handler);
        }

        // Check for dynamic routes (simple parameter matching)
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            if ($this->matchRoute($route, $uri)) {
                return $this->executeHandler($handler);
            }
        }

        // 404 Not Found
        http_response_code(404);
        include __DIR__ . '/../views/errors/404.php';
    }

    private function matchRoute($route, $uri) {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $uri);
    }

    private function executeHandler($handler) {
        if (is_callable($handler)) {
            return $handler();
        }

        if (is_string($handler)) {
            // Handle Controller@method format
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                $controllerClass = "App\\Controllers\\{$controller}";

                if (class_exists($controllerClass)) {
                    $instance = new $controllerClass();
                    if (method_exists($instance, $method)) {
                        return $instance->$method();
                    }
                }
            }

            // Handle file include
            if (file_exists(__DIR__ . "/../{$handler}")) {
                return include __DIR__ . "/../{$handler}";
            }
        }

        // Handler not found
        http_response_code(500);
        echo "Handler not found";
    }
}

// Initialize router
$router = new Router();

// Define routes
$router->get('/', function() {
    if (isset($_SESSION['user_id'])) {
        redirect('/dashboard');
    } else {
        include __DIR__ . '/../views/auth/login.php';
    }
});

// Handle login for both root and /login paths
$router->post('/', 'AuthController@login');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Protected routes (require authentication)
$protectedRoutes = [
    '/dashboard' => 'DashboardController@index',
    '/invoices' => 'InvoiceController@index',
    '/invoices/create' => 'InvoiceController@create',
    '/invoices/store' => 'InvoiceController@store',
    '/invoices/{id}' => 'InvoiceController@show',
    '/invoices/{id}/pdf' => 'InvoiceController@pdf',
    '/invoices/{id}/update-payment' => 'InvoiceController@updatePayment',
    '/tests' => 'TestController@index',
    '/tests/create' => 'TestController@create',
    '/tests/{id}' => 'TestController@show',
    '/tests/{id}/edit' => 'TestController@edit',
    '/tests/{id}/update' => 'TestController@update',
    '/doctors' => 'DoctorController@index',
    '/doctors/create' => 'DoctorController@create',
    '/reports' => 'ReportController@index',
    '/reports/{id}' => 'ReportController@show',
    '/reports/{id}/edit' => 'ReportController@edit',
    '/reports/{id}/update' => 'ReportController@update',
    '/users' => 'UserController@index',
    '/users/create' => 'UserController@create',
    '/users/{id}' => 'UserController@delete',
    '/inventory' => 'InventoryController@index',
    '/audit' => 'AuditController@index',
    '/sms' => 'SmsController@index',
    '/sms/send' => 'SmsController@send',
];

foreach ($protectedRoutes as $path => $handler) {
    $router->get($path, function() use ($handler) {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            redirect('/');
            return;
        }

        // Execute the handler
        if (strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controller}";

            if (class_exists($controllerClass)) {
                $instance = new $controllerClass();
                if (method_exists($instance, $method)) {
                    return $instance->$method();
                }
            }
        }

        http_response_code(500);
        echo "Controller not found";
    });

    // Also handle POST for the same routes
    $router->post($path, function() use ($handler) {
        if (!isset($_SESSION['user_id'])) {
            redirect('/');
            return;
        }

        if (strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            $controllerClass = "App\\Controllers\\{$controller}";

            if (class_exists($controllerClass)) {
                $instance = new $controllerClass();
                $postMethod = str_replace('index', 'store', $method);
                if (method_exists($instance, $postMethod)) {
                    return $instance->$postMethod();
                }
            }
        }
    });
}

// API routes
$router->get('/api/dashboard/stats', function() {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $db = getDB();

    try {
        $stats = [];

        // Get total invoices
        $stmt = $db->query("SELECT COUNT(*) as total FROM invoices");
        $stats['invoices'] = $stmt->fetchColumn();

        // Get total tests
        $stmt = $db->query("SELECT COUNT(*) as total FROM tests WHERE is_active = 1");
        $stats['tests'] = $stmt->fetchColumn();

        // Get total reports
        $stmt = $db->query("SELECT COUNT(*) as total FROM test_reports");
        $stats['reports'] = $stmt->fetchColumn();

        // Get total doctors
        $stmt = $db->query("SELECT COUNT(*) as total FROM doctors WHERE is_active = 1");
        $stats['doctors'] = $stmt->fetchColumn();

        echo json_encode($stats);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch stats']);
    }
});

$router->get('/api/invoices/recent', function() {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $db = getDB();

    try {
        $stmt = $db->query("
            SELECT id, patient_name, patient_phone, total_amount, payment_status, created_at
            FROM invoices
            ORDER BY created_at DESC
            LIMIT 5
        ");

        $invoices = $stmt->fetchAll();
        echo json_encode(['invoices' => $invoices]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch invoices']);
    }
});

// Serve static assets
if (preg_match('/\.(?:css|js|png|jpg|jpeg|gif|ico|svg)$/', $_SERVER['REQUEST_URI'])) {
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (file_exists($file)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml'
        ];

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        header("Content-Type: $mimeType");
        readfile($file);
        exit;
    }
}

// Add missing API endpoint with expected naming
$router->get('/api/dashboard-stats', function() {
    header('Content-Type: application/json');
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        return;
    }

    $db = getDB();

    try {
        $stats = [];

        // Get total invoices
        $stmt = $db->query("SELECT COUNT(*) as total FROM invoices");
        $stats['total_invoices'] = (int)$stmt->fetchColumn();

        // Get total tests
        $stmt = $db->query("SELECT COUNT(*) as total FROM tests WHERE is_active = 1");
        $stats['total_tests'] = (int)$stmt->fetchColumn();

        // Get total revenue
        $stmt = $db->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM invoices");
        $stats['total_revenue'] = (float)$stmt->fetchColumn();

        // Get monthly revenue (current month)
        $stmt = $db->query("SELECT COALESCE(SUM(total_amount), 0) as monthly_revenue FROM invoices WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())");
        $stats['monthly_revenue'] = (float)$stmt->fetchColumn();

        // Get pending invoices count
        $stmt = $db->query("SELECT COUNT(*) as pending FROM invoices WHERE payment_status = 'pending'");
        $stats['pending_invoices'] = (int)$stmt->fetchColumn();

        // Get paid invoices count
        $stmt = $db->query("SELECT COUNT(*) as paid FROM invoices WHERE payment_status = 'paid'");
        $stats['paid_invoices'] = (int)$stmt->fetchColumn();

        echo json_encode($stats);
    } catch (Exception $e) {
        log_activity("Dashboard stats API error: " . $e->getMessage(), 'error');
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch statistics']);
    }
});

// Dispatch the route
$router->dispatch();