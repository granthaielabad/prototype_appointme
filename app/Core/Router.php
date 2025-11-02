<?php
namespace App\Core;

class Router
{
    protected array $routes = [];

    public function get(string $path, string $controllerAction): void
    {
        $this->addRoute('GET', $path, $controllerAction);
    }

    public function post(string $path, string $controllerAction): void
    {
        $this->addRoute('POST', $path, $controllerAction);
    }

    protected function addRoute(string $method, string $path, string $controllerAction): void
    {
        $this->routes[$method][$path] = $controllerAction;
    }

    public function dispatch(string $uri = null, string $method = null): void
    {
        $uri = $uri ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $method ?? $_SERVER['REQUEST_METHOD'];

        // Normalize trailing slash
        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Route {$uri} not defined for {$method}</p>";
            return;
        }

        $controllerAction = $this->routes[$method][$uri];
        [$controllerName, $methodName] = explode('@', $controllerAction);

        // ✅ Detect namespaced controller (like Admin\DashboardController)
        if (str_contains($controllerName, '\\')) {
            $fullController = "App\\Controllers\\{$controllerName}";
        } else {
            $fullController = "App\\Controllers\\{$controllerName}";
        }

        if (!class_exists($fullController)) {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Controller {$fullController} not found.</p>";
            return;
        }

        $controller = new $fullController();

        if (!method_exists($controller, $methodName)) {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Method {$methodName} not found in {$fullController}.</p>";
            return;
        }

        // Finally call the controller action
        $controller->$methodName();
    }
}
