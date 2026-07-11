<?php

namespace Boy\S3Materi;

class Router {
    private array $routes = [];

    public function get(string $path, $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method): void {
        if (isset($_GET['r'])) {
            $path = $_GET['r'];
        } else {
            $path = parse_url($uri, PHP_URL_PATH);
            
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $scriptDir = rtrim($scriptDir, '/');

            if (!empty($scriptDir) && str_starts_with($path, $scriptDir)) {
                $path = substr($path, strlen($scriptDir));
            }
        }

        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        if ($path === '' || $path[0] !== '/') {
            $path = '/' . $path;
        }

        if (!isset($this->routes[$method][$path])) {
            $this->sendNotFound();
            return;
        }

        $handler = $this->routes[$method][$path];

        if (is_callable($handler)) {
            $handler();
        } elseif (is_string($handler)) {
            [$controllerName, $action] = explode('@', $handler);
            $controllerClass = "App\\Controllers\\" . $controllerName;

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    $this->sendError("Action '$action' not found in controller '$controllerClass'.");
                }
            } else {
                $this->sendError("Controller class '$controllerClass' not found.");
            }
        }
    }

    private function sendNotFound(): void {
        header("HTTP/1.1 404 Not Found");
        // Renders standard 404 UI
        echo "<div style='font-family: sans-serif; text-align: center; padding: 50px;'>";
        echo "<h1 style='font-size: 50px; color: #4b5563;'>404</h1>";
        echo "<p style='color: #6b7280;'>Halaman tidak ditemukan.</p>";
        echo "<a href='/' style='color: #4f46e5; text-decoration: none; font-weight: bold;'>Kembali ke Beranda</a>";
        echo "</div>";
    }

    private function sendError(string $message): void {
        header("HTTP/1.1 500 Internal Server Error");
        echo "<div style='font-family: sans-serif; padding: 30px; border: 1px solid #fca5a5; background-color: #fef2f2; border-radius: 8px; margin: 20px;'>";
        echo "<h1 style='color: #dc2626;'>500 Internal Server Error</h1>";
        echo "<p style='color: #7f1d1d;'>" . htmlspecialchars($message) . "</p>";
        echo "</div>";
    }
}
