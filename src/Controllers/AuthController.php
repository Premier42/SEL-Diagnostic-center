<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService(new User(), $this->session);
    }

    public function showLogin(): void
    {
        // Redirect if already logged in
        if ($this->authService->isAuthenticated()) {
            $role = $this->session->getUserRole();
            $dashboard = $role === 'admin' ? '/admin/dashboard' : '/staff/dashboard';
            $this->redirect($dashboard);
        }

        $this->view('auth/login');
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        if (!$this->validateCsrf()) {
            $this->view('auth/login', ['error' => 'Invalid security token']);
            return;
        }

        $username = $this->sanitizeInput($this->getInput('username', ''));
        $password = $this->getInput('password', '');

        $result = $this->authService->login($username, $password);

        if ($result['success']) {
            $role = $result['user']['role'];
            $dashboard = $role === 'admin' ? '/admin/dashboard' : '/staff/dashboard';
            $this->redirect($dashboard);
        } else {
            $error = $result['message'] ?? 'Login failed';
            $this->view('auth/login', ['error' => $error]);
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/login');
    }
}
