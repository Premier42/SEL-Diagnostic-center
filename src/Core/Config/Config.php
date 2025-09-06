<?php

namespace App\Core\Config;

class Config
{
    private array $config = [];
    private static ?Config $instance = null;

    public function __construct()
    {
        $this->loadEnvironmentVariables();
        $this->setDefaults();
    }

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnvironmentVariables(): void
    {
        $envFile = __DIR__ . '/../../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                list($key, $value) = explode('=', $line, 2);
                $this->config[trim($key)] = trim($value, '"\'');
            }
        }
    }

    private function setDefaults(): void
    {
        $defaults = [
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'diagnostic_center',
            'DB_USER' => 'npl_user',
            'DB_PASS' => 'npl_password',
            'APP_NAME' => 'Pathology Laboratory Management System',
            'APP_ENV' => 'development',
            'APP_DEBUG' => 'true',
            'BASE_URL' => '/NPL/',
            'SESSION_LIFETIME' => '3600',
            'CSRF_TOKEN_NAME' => 'csrf_token',
            'LOG_LEVEL' => 'error',
            'LOG_FILE' => 'logs/app.log'
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($this->config[$key])) {
                $this->config[$key] = $value;
            }
        }
    }

    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

    public function all(): array
    {
        return $this->config;
    }

    public function isDebug(): bool
    {
        return $this->get('APP_DEBUG') === 'true';
    }

    public function getEnvironment(): string
    {
        return $this->get('APP_ENV', 'production');
    }
}
