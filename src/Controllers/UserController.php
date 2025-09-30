<?php

namespace App\Controllers;

class UserController extends BaseController
{
    public function index()
    {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['errors'] = ['general' => 'Access denied'];
            redirect('/dashboard');
            return;
        }

        $db = getDB();

        try {
            $search = $_GET['search'] ?? '';
            $role = $_GET['role'] ?? '';

            $query = "SELECT id, username, full_name, email, phone, role, is_active, last_login, created_at FROM users WHERE 1=1";
            $params = [];

            if ($search) {
                $query .= " AND (username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if ($role) {
                $query .= " AND role = ?";
                $params[] = $role;
            }

            $query .= " ORDER BY username ASC";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $users = $stmt->fetchAll();

            include __DIR__ . '/../../views/users/index.php';

        } catch (Exception $e) {
            log_activity("User listing error: " . $e->getMessage(), 'error');
            $error_message = config('app.debug') ? $e->getMessage() : 'Unable to load users.';
            include __DIR__ . '/../../views/errors/500.php';
        }
    }

    public function create()
    {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['errors'] = ['general' => 'Access denied'];
            redirect('/dashboard');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
            return;
        }

        include __DIR__ . '/../../views/users/create.php';
    }

    public function store()
    {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['errors'] = ['general' => 'Access denied'];
            redirect('/dashboard');
            return;
        }

        $db = getDB();

        try {
            $data = [
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'role' => $_POST['role'] ?? 'staff'
            ];

            // Validation
            $errors = [];
            if (empty($data['username'])) {
                $errors['username'] = 'Username is required';
            }
            if (empty($data['password'])) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($data['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }
            if (empty($data['full_name'])) {
                $errors['full_name'] = 'Full name is required';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                redirect('/users/create');
                return;
            }

            // Check if username exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                $_SESSION['errors'] = ['username' => 'Username already exists'];
                $_SESSION['old'] = $data;
                redirect('/users/create');
                return;
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $db->prepare("
                INSERT INTO users (username, password, full_name, email, phone, role)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['username'],
                $hashedPassword,
                $data['full_name'],
                $data['email'],
                $data['phone'],
                $data['role']
            ]);

            $_SESSION['success'] = 'User created successfully';
            redirect('/users');

        } catch (Exception $e) {
            log_activity("User creation error: " . $e->getMessage(), 'error');
            $_SESSION['errors'] = ['general' => 'Failed to create user'];
            redirect('/users/create');
        }
    }

    public function delete()
    {
        $this->requireAuth();

        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/users');
            return;
        }

        // Validate CSRF token
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            $this->flashMessage('error', 'Invalid security token');
            redirect('/users');
            return;
        }

        $uri = $_SERVER['REQUEST_URI'];
        $segments = explode('/', trim($uri, '/'));
        $id = (int)($segments[1] ?? 0);

        if (!$id) {
            redirect('/users');
            return;
        }

        // Prevent deleting yourself
        if ($id == $_SESSION['user_id']) {
            $this->flashMessage('error', 'You cannot delete your own account');
            redirect('/users');
            return;
        }

        $db = getDB();

        try {
            // Get user details before deletion
            $stmt = $db->prepare("SELECT username, full_name FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();

            if (!$user) {
                $this->flashMessage('error', 'User not found');
                redirect('/users');
                return;
            }

            // Delete user
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);

            log_activity("Deleted user: {$user['username']}", 'info', [
                'user_id' => $id,
                'username' => $user['username'],
                'deleted_by' => $_SESSION['user_id']
            ]);

            $this->flashMessage('success', 'User deleted successfully!');
            redirect('/users');

        } catch (Exception $e) {
            log_activity("User deletion error: " . $e->getMessage(), 'error');

            $this->flashMessage('error', 'Failed to delete user. Please try again.');
            redirect('/users');
        }
    }
}