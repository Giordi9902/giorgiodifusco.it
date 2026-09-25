<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect($this->homePathFor($_SESSION['user_role']));
        }
        $this->view('auth/login');
    }

    public function login()
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');

        $userModel = new User();
        $user = $email !== '' ? $userModel->findByEmail($email) : null;

        if ($user && password_verify($password, $user['password'])) {
            // Nuovo ID di sessione dopo il login (previene session fixation)
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            $home = $this->homePathFor($user['role']);
            if ($this->isAjax()) {
                $this->jsonResponse(true, "Login successful", ['redirect' => BASE_URL . $home]);
            }
            $this->redirect($home);
        }

        if ($this->isAjax()) {
            $this->jsonResponse(false, "Invalid credentials", [], 401);
        }

        // Fallback per richieste non AJAX: usa flash message e redirect alla pagina di login
        $_SESSION['flash_error'] = 'Invalid credentials. Try again';
        $this->redirect('/login');
    }

    public function logout()
    {
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');

        $_SESSION = [];
        session_destroy();
        $this->redirect('/login');
    }

    private function homePathFor(?string $role): string
    {
        return $role === 'ADMIN' ? '/admin' : '/dashboard';
    }
}
