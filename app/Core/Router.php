<?php
namespace App\Core;

class Router
{
    protected array $routes = [
        'GET' => [],
        'POST' => []
    ];

    protected $notFound;

    public function get(string $path, string $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, string $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function setNotFound(callable $cb): void
    {
        $this->notFound = $cb;
    }

    protected function normalize(string $path): string
    {
        $p = parse_url($path, PHP_URL_PATH) ?: '/';
        $p = rtrim($p, '/');
        return $p === '' ? '/' : $p;
    }

    public function dispatch(string $uri = null, string $method = null): void
    {
        $uri = $uri ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $method ?? $_SERVER['REQUEST_METHOD'];
        $requestPath = $this->normalize($uri);
        $method = strtoupper($method);

        $handler = $this->routes[$method][$requestPath] ?? null;

        if (!$handler) {
            if (is_callable($this->notFound)) {
                call_user_func($this->notFound);
                return;
            }
            http_response_code(404);
            echo "404 Not Found (no route for {$method} {$requestPath})";
            return;
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controller, $action] = explode('@', $handler, 2);

            // If controller contains namespace separators, use it directly; else default namespace
            if (str_contains($controller, '\\')) {
                $controllerClass = "App\\Controllers\\{$controller}";
            } else {
                $controllerClass = "App\\Controllers\\{$controller}";
            }

            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo "Controller {$controllerClass} not found";
                return;
            }

            $instance = new $controllerClass();

            if (!method_exists($instance, $action)) {
                http_response_code(500);
                echo "Action {$action} not found in {$controllerClass}";
                return;
            }

            call_user_func([$instance, $action]);
            return;
        }

        if (is_callable($handler)) {
            call_user_func($handler);
            return;
        }

        http_response_code(500);
        echo "Invalid route handler.";
    }
}
