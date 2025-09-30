<?php
/**
 * SEL Diagnostic Center - Bootstrap File
 * Initializes the application with proper error handling and configuration
 */

// Set error reporting for development
if (file_exists(__DIR__ . '/.env')) {
    $envFile = __DIR__ . '/.env';
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;

        list($key, $value) = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Set error reporting based on environment
if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set timezone
date_default_timezone_set($_ENV['TIMEZONE'] ?? 'Asia/Dhaka');

// Start session with security settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true for HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_name($_ENV['SESSION_NAME'] ?? 'sel_session');
    session_start();
}

// Initialize CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Simple autoloader for our classes
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Database connection function
function getDB() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $_ENV['DB_HOST'] ?? 'localhost',
                $_ENV['DB_PORT'] ?? '3306',
                $_ENV['DB_NAME'] ?? 'pathology_lab'
            );

            $pdo = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("System temporarily unavailable. Please try again later.");
            }
        }
    }

    return $pdo;
}

// Utility functions
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

function config($key, $default = null) {
    $keys = explode('.', $key);
    $config = [
        'app' => [
            'name' => env('APP_NAME', 'SEL Diagnostic Center'),
            'env' => env('APP_ENV', 'production'),
            'debug' => env('APP_DEBUG', 'false') === 'true',
            'url' => env('APP_URL', 'http://localhost'),
            'timezone' => env('TIMEZONE', 'Asia/Dhaka'),
        ],
        'database' => [
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '3306'),
            'name' => env('DB_NAME', 'pathology_lab'),
            'user' => env('DB_USER', 'root'),
            'pass' => env('DB_PASS', ''),
        ]
    ];

    $result = $config;
    foreach ($keys as $segment) {
        if (isset($result[$segment])) {
            $result = $result[$segment];
        } else {
            return $default;
        }
    }

    return $result;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function back() {
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referer);
}

function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function errors($key = null) {
    if ($key) {
        return $_SESSION['errors'][$key] ?? [];
    }
    return $_SESSION['errors'] ?? [];
}

function clearOldInput() {
    unset($_SESSION['old']);
    unset($_SESSION['errors']);
}

function csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

function csrf_field() {
    $token = csrf_token();
    return "<input type='hidden' name='csrf_token' value='$token'>";
}

function verify_csrf($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function storage_path($path = '') {
    $base = __DIR__ . '/storage';
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}

function public_path($path = '') {
    $base = __DIR__ . '/public';
    return $path ? $base . '/' . ltrim($path, '/') : $base;
}

function asset($path) {
    return '/assets/' . ltrim($path, '/');
}

function route($name, $params = []) {
    $routes = [
        'login' => '/',
        'dashboard' => '/dashboard',
        'invoices' => '/invoices',
        'invoices.create' => '/invoices/create',
        'tests' => '/tests',
        'doctors' => '/doctors',
        'reports' => '/reports',
        'users' => '/users',
        'inventory' => '/inventory',
        'audit' => '/audit',
        'sms' => '/sms',
        'logout' => '/logout'
    ];

    $url = $routes[$name] ?? '/';

    // Simple parameter replacement
    foreach ($params as $key => $value) {
        $url = str_replace("{{$key}}", $value, $url);
    }

    return $url;
}

// Simple logging function
function log_activity($message, $level = 'info', $context = []) {
    $logFile = storage_path('logs/app.log');
    $timestamp = date('Y-m-d H:i:s');
    $contextJson = json_encode($context);
    $logEntry = "[$timestamp] $level: $message $contextJson" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}