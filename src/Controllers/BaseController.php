<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Session\SessionManager;
use App\Services\AuthService;

abstract class BaseController
{
    protected Application $app;
    protected SessionManager $session;
    protected AuthService $auth;

    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->session = $this->app->getSession();
        $this->auth = new AuthService(new \App\Models\User(), $this->session);
    }

    protected function view(string $template, array $data = []): void
    {
        $viewPath = __DIR__ . '/../../views/' . $template . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: {$template}");
        }

        // Extract data to variables
        extract($data);
        
        // Add common variables
        $csrfToken = $this->session->generateCsrfToken();
        $currentUser = $this->auth->getCurrentUser();
        $isLoggedIn = $this->auth->isAuthenticated();
        
        require $viewPath;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit();
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function validateCsrf(): bool
    {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        return $this->session->validateCsrfToken($token);
    }

    protected function requireAuth(): void
    {
        $this->auth->requireAuth();
    }

    protected function requireRole(string $role): void
    {
        $this->auth->requireRole($role);
    }

    protected function getInput(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function getAllInput(): array
    {
        return array_merge($_GET, $_POST);
    }

    protected function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
