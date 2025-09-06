<?php
// Authentication Middleware

function requireAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: /index.php');
        exit();
    }
}

function requireRole($requiredRole) {
    requireAuth();
    
    if ($_SESSION['role'] !== $requiredRole) {
        http_response_code(403);
        die('Access denied. Insufficient permissions.');
    }
}

function requireAnyRole($allowedRoles) {
    requireAuth();
    
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        http_response_code(403);
        die('Access denied. Insufficient permissions.');
    }
}

function getCurrentUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'full_name' => $_SESSION['full_name'] ?? null
    ];
}

function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function hasAnyRole($roles) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
}
?>
