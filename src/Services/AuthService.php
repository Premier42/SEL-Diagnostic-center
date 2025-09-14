<?php

namespace App\Services;

use App\Models\User;
use App\Core\Session\SessionManager;
use App\Core\Validation\Validator;

class AuthService
{
    private User $userModel;
    private SessionManager $session;

    public function __construct(User $userModel, SessionManager $session)
    {
        $this->userModel = $userModel;
        $this->session = $session;
    }

    public function login(string $username, string $password): array
    {
        $validator = Validator::make([
            'username' => $username,
            'password' => $password
        ], [
            'username' => 'required',
            'password' => 'required'
        ]);

        if (!$validator->validate()) {
            return ['success' => false, 'errors' => $validator->getErrors()];
        }

        $user = $this->userModel->authenticate($username, $password);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        $this->session->login($user);
        
        return ['success' => true, 'user' => $user];
    }

    public function logout(): void
    {
        $this->session->logout();
    }

    public function isAuthenticated(): bool
    {
        return $this->session->isLoggedIn();
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return $this->userModel->find($this->session->getUserId());
    }

    public function hasRole(string $role): bool
    {
        return $this->session->getUserRole() === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            header('Location: /NPL/login');
            exit();
        }
    }

    public function requireRole(string $role): void
    {
        $this->requireAuth();
        
        if (!$this->hasRole($role)) {
            http_response_code(403);
            echo "Access denied";
            exit();
        }
    }
}
