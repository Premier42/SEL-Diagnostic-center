<?php
// Logout functionality with audit logging

require_once 'utils/audit-logger.php';

session_start();

// Log user logout if user is logged in
if (isset($_SESSION['user_id'])) {
    auditUserLogout($_SESSION['user_id']);
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: index.php');
exit();
