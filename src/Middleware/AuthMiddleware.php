<?php

namespace App\Middleware;

use App\Core\Application;
use App\Services\AuthService;
use App\Models\User;

class AuthMiddleware
{
    private AuthService $authService;

    public function __construct()
    {
        $session = Application::getInstance()->getSession();
        $this->authService = new AuthService(new User(), $session);
    }

    public function handle(): void
    {
        if (!$this->authService->isAuthenticated()) {
            header('Location: /NPL/login');
            exit();
        }
    }
}
