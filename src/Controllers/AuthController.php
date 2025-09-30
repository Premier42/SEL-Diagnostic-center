<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/');
            return;
        }

        // Validate CSRF token
        $csrf_token = $_POST['csrf_token'] ?? '';
        $session_token = $_SESSION['csrf_token'] ?? '';

        if (!verify_csrf($csrf_token)) {
            error_log("CSRF Validation Failed - Posted: " . $csrf_token . " | Session: " . $session_token);
            $_SESSION['errors'] = ['general' => ['Invalid security token. Please refresh and try again.']];
            redirect('/');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Store old input for form repopulation
        $_SESSION['old'] = ['username' => $username];

        // Validation
        $errors = [];
        if (empty($username)) {
            $errors['username'] = ['Username is required'];
        }
        if (empty($password)) {
            $errors['password'] = ['Password is required'];
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            redirect('/');
            return;
        }

        // Attempt authentication
        $db = getDB();
        try {
            $stmt = $db->prepare("
                SELECT id, username, password, full_name, email, role, is_active, last_login
                FROM users
                WHERE username = ? AND is_active = 1
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Authentication successful
                session_regenerate_id(true); // Prevent session fixation

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_login'] = $user['last_login'];

                // Update last login
                $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);

                // Log successful login
                log_activity("User {$user['username']} logged in successfully", 'info', [
                    'user_id' => $user['id'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);

                clearOldInput();
                redirect('/dashboard');
            } else {
                // Authentication failed
                log_activity("Failed login attempt for username: {$username}", 'warning', [
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);

                $_SESSION['errors'] = ['general' => ['Invalid username or password']];
                redirect('/');
            }
        } catch (Exception $e) {
            log_activity("Login error: " . $e->getMessage(), 'error');
            $_SESSION['errors'] = ['general' => ['System error occurred. Please try again.']];
            redirect('/');
        }
    }

    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            log_activity("User {$_SESSION['username']} logged out", 'info', [
                'user_id' => $_SESSION['user_id']
            ]);
        }

        // Clear all session data
        $_SESSION = [];

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

        redirect('/');
    }
}