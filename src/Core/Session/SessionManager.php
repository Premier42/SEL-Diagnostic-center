<?php

namespace App\Core\Session;

use App\Core\Config\Config;

class SessionManager
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->initializeSession();
    }

    private function initializeSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionConfig = [
                'cookie_httponly' => true,
                'use_strict_mode' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_samesite' => 'Strict'
            ];

            session_start($sessionConfig);
        }
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function clear(): void
    {
        session_unset();
    }

    public function destroy(): void
    {
        session_destroy();
    }

    public function regenerateId(): void
    {
        session_regenerate_id(true);
    }

    public function generateCsrfToken(): string
    {
        if (!$this->has($this->config->get('CSRF_TOKEN_NAME'))) {
            $token = bin2hex(random_bytes(32));
            $this->set($this->config->get('CSRF_TOKEN_NAME'), $token);
        }
        return $this->get($this->config->get('CSRF_TOKEN_NAME'));
    }

    public function validateCsrfToken(string $token): bool
    {
        $sessionToken = $this->get($this->config->get('CSRF_TOKEN_NAME'));
        return $sessionToken && hash_equals($sessionToken, $token);
    }

    public function isLoggedIn(): bool
    {
        return $this->has('user_id');
    }

    public function getUserId(): ?int
    {
        return $this->get('user_id');
    }

    public function getUsername(): ?string
    {
        return $this->get('username');
    }

    public function getUserRole(): ?string
    {
        return $this->get('role');
    }

    public function login(array $user): void
    {
        $this->regenerateId();
        $this->set('user_id', $user['id']);
        $this->set('username', $user['username']);
        $this->set('role', $user['role']);
    }

    public function logout(): void
    {
        $this->clear();
        $this->destroy();
    }
}
