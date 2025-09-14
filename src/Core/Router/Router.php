<?php

namespace App\Core\Router;

use App\Core\Application;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, $handler, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base URL from URI
        $baseUrl = Application::getInstance()->getConfig()->get('BASE_URL');
        if (strpos($uri, $baseUrl) === 0) {
            $uri = substr($uri, strlen($baseUrl));
        }
        
        $uri = '/' . ltrim($uri, '/');

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $method, $uri)) {
                $this->executeRoute($route, $uri);
                return;
            }
        }

        // No route found
        $this->handleNotFound();
    }

    private function matchRoute(array $route, string $method, string $uri): bool
    {
        if ($route['method'] !== $method) {
            return false;
        }

        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route['path']);
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $uri);
    }

    private function executeRoute(array $route, string $uri): void
    {
        // Execute middlewares
        foreach ($route['middlewares'] as $middleware) {
            if (is_string($middleware)) {
                $middlewareInstance = new $middleware();
                $middlewareInstance->handle();
            }
        }

        // Extract parameters
        $params = $this->extractParams($route['path'], $uri);

        // Execute handler
        if (is_string($route['handler'])) {
            list($controllerClass, $method) = explode('@', $route['handler']);
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $params);
        } elseif (is_callable($route['handler'])) {
            call_user_func_array($route['handler'], $params);
        }
    }

    private function extractParams(string $routePath, string $uri): array
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));
        $params = [];

        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([^}]+)\}/', $part, $matches)) {
                $params[$matches[1]] = $uriParts[$index] ?? null;
            }
        }

        return array_values($params);
    }

    private function handleNotFound(): void
    {
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
