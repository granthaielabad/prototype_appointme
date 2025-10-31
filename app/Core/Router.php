<?php
namespace App\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
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
        return rtrim($p, '/') === '' ? '/' : rtrim($p, '/');
    }

    /*
       Normalizes the given path by removing trailing slashes
     */
    public function dispatch(string $uri, string $method): void
    {
        $requestPath = $this->normalize($uri);
        $method = strtoupper($method);

        $handler = $this->routes[$method][$requestPath] ?? null;

        if (!$handler) {
            if (is_callable($this->notFound)) {
                call_user_func($this->notFound);
            } else {
                http_response_code(404);
                echo "404 Not Found";
            }
            return;
        }

        // handler format: "Controller@method" or "Namespace\Ctrl@method"
        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controller, $action] = explode('@', $handler);
            $controllerClass = $this->qualifyController($controller);
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
            call_user_func_array([$instance, $action], []);
            return;
        }

        if (is_callable($handler)) {
            call_user_func($handler);
            return;
        }

        http_response_code(500);
        echo "Invalid route handler.";
    }
        
    protected function qualifyController(string $controller): string
    {
        // If provided with backslash namespace, use it as-is
        if (strpos($controller, '\\') !== false) {
            return $controller;
        }
        // Default namespace for controllers
        return 'App\\Controllers\\' . $controller;
    }

}
