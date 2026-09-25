<?php

namespace Core;

abstract class Controller
{
    protected function view($view, $data = [])
    {
        $viewPath = __DIR__ . "/../app/Views/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View {$view} not found");
        }

        extract($data);
        require $viewPath;
    }

    protected function jsonResponse($success, $message = "", $data = [], $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            "success" => $success,
            "message" => $message,
            "data" => $data
        ]);
        exit;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    /**
     * Body della richiesta: JSON se presente, altrimenti i campi del form.
     */
    protected function input(): array
    {
        $json = json_decode(file_get_contents('php://input'), true);
        return is_array($json) ? $json : $_POST;
    }

    protected function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
    }

    protected function isAdmin(): bool
    {
        return ($_SESSION['user_role'] ?? null) === 'ADMIN';
    }

    protected function currentUserId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    protected function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, "Unauthorized", [], 401);
            }
            $this->redirect('/login');
        }
    }

    protected function requireAdmin()
    {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            if ($this->isAjax()) {
                $this->jsonResponse(false, "Forbidden", [], 403);
            }
            $this->redirect('/dashboard');
        }
    }

    protected function verifyCsrfToken($token)
    {
        if (!isset($_SESSION['csrf_token']) || !is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $this->jsonResponse(false, "CSRF token validation failed", [], 403);
        }
    }
}
