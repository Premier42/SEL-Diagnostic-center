<?php

namespace App\Core;

use App\Core\Config\Config;
use App\Core\Database\DatabaseManager;
use App\Core\Router\Router;
use App\Core\Session\SessionManager;
use App\Core\Error\ErrorHandler;

class Application
{
    private static ?Application $instance = null;
    private Config $config;
    private DatabaseManager $database;
    private Router $router;
    private SessionManager $session;
    private ErrorHandler $errorHandler;

    private function __construct()
    {
        $this->initializeApplication();
    }

    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initializeApplication(): void
    {
        // Initialize configuration
        $this->config = new Config();
        
        // Initialize error handler
        $this->errorHandler = new ErrorHandler($this->config);
        
        // Initialize session
        $this->session = new SessionManager($this->config);
        
        // Initialize database
        $this->database = new DatabaseManager($this->config);
        
        // Initialize router
        $this->router = new Router();
    }

    public function run(): void
    {
        try {
            $this->router->dispatch();
        } catch (\Exception $e) {
            $this->errorHandler->handleException($e);
        }
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getDatabase(): DatabaseManager
    {
        return $this->database;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getSession(): SessionManager
    {
        return $this->session;
    }
}
