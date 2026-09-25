<?php

namespace Core;

class Router
{
    private $routes = [];

    public function add($method, $path, $controllerAction)
    {
        $this->routes[] = compact('method', 'path', 'controllerAction');
    }

    public function dispatch($url, $method)
    {
        $url = '/' . trim($url, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            // Convert route params like {id} to regex
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $url, $matches)) {
                continue;
            }

            [$controller, $action] = explode('@', $route['controllerAction']);
            $controllerName = "App\\Controllers\\" . $controller;

            if (!class_exists($controllerName) || !method_exists($controllerName, $action)) {
                throw new \LogicException("Route handler {$route['controllerAction']} non trovato");
            }

            // Filter named parameters
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            call_user_func_array([new $controllerName(), $action], $params);
            return;
        }

        $this->notFound($url);
    }

    private function notFound(string $url): void
    {
        http_response_code(404);

        if (str_starts_with($url, '/api/')) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "404 Not Found"]);
            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="it"><head><meta charset="UTF-8"><title>Pagina non trovata</title></head>'
            . '<body style="font-family:system-ui,sans-serif;text-align:center;padding:4rem">'
            . '<h1>404</h1><p>La pagina richiesta non esiste.</p>'
            . '<p><a href="' . htmlspecialchars(BASE_URL) . '/">Torna alla homepage</a></p></body></html>';
    }
}
