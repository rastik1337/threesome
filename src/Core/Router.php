<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private static array $routes = [];

    public static function get(string $path, array $handler): void
    {
        self::$routes['GET'][$path] = $handler;
    }

    public static function post(string $path, array $handler): void
    {
        self::$routes['POST'][$path] = $handler;
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        $handler = self::$routes[$method][$uri] ?? null;

        if ($handler === null) {
            http_response_code(404);
            View::render('404', ['title' => 'Page Not Found']);
            return;
        }

        [$controllerClass, $action] = $handler;

        if (!class_exists($controllerClass) || !method_exists($controllerClass, $action)) {
            http_response_code(500);
            echo "Error: Controller {$controllerClass} or action {$action} does not exist.";
            return;
        }

        $controller = new $controllerClass();
        $controller->$action();
    }
}
