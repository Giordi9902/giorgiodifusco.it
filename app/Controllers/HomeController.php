<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\BlogPost;

class HomeController extends Controller
{
    public function index()
    {
        // Se l'utente è già loggato, portalo direttamente alla dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect($this->isAdmin() ? '/admin' : '/dashboard');
        }

        $this->view('homepage', ['recentPosts' => $this->fetchRecentPostsSafely()]);
    }

    /**
     * Ultimi articoli per la sezione Blog in homepage.
     * La homepage pubblica deve restare raggiungibile anche se il database
     * non risponde: in quel caso la sezione viene semplicemente omessa.
     */
    private function fetchRecentPostsSafely(int $limit = 6): array
    {
        try {
            return (new BlogPost())->findPublished(null, null, null, $limit);
        } catch (\PDOException $e) {
            error_log('Homepage: blog non disponibile - ' . $e->getMessage());
            return [];
        }
    }
}
