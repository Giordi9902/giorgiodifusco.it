<?php

// Server integrato di PHP (sviluppo): serve direttamente i file statici esistenti
if (PHP_SAPI === 'cli-server') {
    $staticFile = realpath(__DIR__ . '/..' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($staticFile && is_file($staticFile) && str_starts_with($staticFile, __DIR__ . DIRECTORY_SEPARATOR)) {
        return false;
    }
}

// Path richiesto: impostato da .htaccess in `url`, altrimenti dedotto dalla URI
$requestPath = '/' . trim($_GET['url'] ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');

// Custom PSR-4 style autoloader
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = __DIR__ . '/../';

    if (strncmp('Core\\', $class, 5) === 0) {
        $prefix = 'Core\\';
        $base_dir .= 'core/';
    } elseif (strncmp('App\\', $class, 4) === 0) {
        $prefix = 'App\\';
        $base_dir .= 'app/';
    }

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use Core\Router;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';

// Errori: in produzione vengono loggati e all'utente arriva una risposta generica
ini_set('display_errors', APP_DEBUG ? '1' : '0');
error_reporting(E_ALL);

set_exception_handler(function (\Throwable $e) {
    error_log((string) $e);

    if (!headers_sent()) {
        http_response_code(500);
    }

    $isApi = str_starts_with($GLOBALS['requestPath'], '/api/');
    $detail = APP_DEBUG ? get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : null;

    if ($isApi) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $detail ?? 'Errore interno del server', 'data' => []]);
        return;
    }

    echo '<!DOCTYPE html><html lang="it"><head><meta charset="UTF-8"><title>Errore</title></head>'
        . '<body style="font-family:system-ui,sans-serif;text-align:center;padding:4rem">'
        . '<h1>Si è verificato un errore</h1><p>Riprova tra qualche minuto.</p>'
        . ($detail ? '<pre style="text-align:left;white-space:pre-wrap">' . htmlspecialchars($detail) . '</pre>' : '')
        . '</body></html>';
});

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => IS_HTTPS,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$router = new Router();

// Routes definition
// Public homepage as entrypoint
$router->add('GET', '/', 'HomeController@index');
$router->add('GET', '/sitemap.xml', 'SitemapController@index');
$router->add('GET', '/blog', 'BlogController@index');
$router->add('GET', '/blog/{slug}', 'BlogController@show');
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('POST', '/logout', 'AuthController@logout');

$router->add('GET', '/dashboard', 'StudentController@dashboard');
$router->add('GET', '/dashboard/lessons', 'StudentController@lessonsPage');
$router->add('GET', '/dashboard/packages', 'StudentController@packagesPage');
$router->add('GET', '/dashboard/materials', 'StudentController@materialsPage');
$router->add('GET', '/dashboard/payments', 'StudentController@paymentsPage');
$router->add('GET', '/dashboard/report', 'StudentController@report');

$router->add('GET', '/admin', 'AdminController@index');
$router->add('GET', '/admin/students', 'AdminController@students');
$router->add('GET', '/admin/students/{id}', 'AdminController@studentDetail');
$router->add('GET', '/admin/lessons', 'AdminController@lessons');
$router->add('GET', '/admin/materials', 'AdminController@materials');
$router->add('GET', '/admin/payments', 'AdminController@payments');
$router->add('GET', '/admin/report', 'AdminController@report');

// CMS Blog routes (separated from LMS admin)
$router->add('GET', '/cms', 'CmsController@index');
$router->add('GET', '/cms/blog', 'CmsController@posts');
$router->add('GET', '/cms/blog/new', 'CmsController@postCreate');
$router->add('GET', '/cms/blog/edit/{id}', 'CmsController@postEdit');
$router->add('GET', '/cms/aree', 'CmsController@areas');

// Legacy redirects for old admin/blog/* URLs
$router->add('GET', '/admin/blog', 'AdminController@blog');
$router->add('GET', '/admin/blog/new', 'AdminController@blogCreate');
$router->add('GET', '/admin/blog/edit/{id}', 'AdminController@blogEdit');

// API Routes for Students (AJAX)
$router->add('GET', '/api/students', 'StudentController@index');
$router->add('POST', '/api/students', 'StudentController@store');
$router->add('PUT', '/api/students/{id}', 'StudentController@update');
$router->add('DELETE', '/api/students/{id}', 'StudentController@delete');

// API Routes for Lessons
$router->add('GET', '/api/lessons', 'LessonController@index');
$router->add('POST', '/api/lessons', 'LessonController@store');
$router->add('PUT', '/api/lessons/{id}', 'LessonController@update');
$router->add('DELETE', '/api/lessons/{id}', 'LessonController@delete');

// API Routes for Payments
$router->add('GET', '/api/payments', 'PaymentController@index');
$router->add('POST', '/api/payments', 'PaymentController@store');

// API Routes for Materials
$router->add('GET', '/api/materials', 'MaterialController@index');
$router->add('POST', '/api/materials', 'MaterialController@store');

// API Routes for Packages
$router->add('GET', '/api/packages', 'PackageController@index');
$router->add('POST', '/api/packages', 'PackageController@store');
$router->add('GET', '/api/packages/{id}', 'PackageController@show');

// API Routes for Student Notes
$router->add('GET', '/api/notes/{studentId}', 'StudentNoteController@index');
$router->add('POST', '/api/notes', 'StudentNoteController@store');

// API Routes for Blog (admin)
$router->add('GET', '/api/blog/posts', 'BlogController@apiIndex');
$router->add('POST', '/api/blog/posts', 'BlogController@apiStore');
$router->add('PUT', '/api/blog/posts/{id}', 'BlogController@apiUpdate');
$router->add('DELETE', '/api/blog/posts/{id}', 'BlogController@apiDelete');

// API Routes for Blog images (admin)
$router->add('POST', '/api/blog/images', 'BlogController@apiImagesStore');

// API Routes for Blog courses/subjects (admin)
$router->add('GET',    '/api/blog/courses',        'BlogController@apiCoursesIndex');
$router->add('POST',   '/api/blog/courses',        'BlogController@apiCoursesStore');
$router->add('PUT',    '/api/blog/courses/{id}',   'BlogController@apiCoursesUpdate');
$router->add('DELETE', '/api/blog/courses/{id}',   'BlogController@apiCoursesDelete');
$router->add('GET',    '/api/blog/subjects',       'BlogController@apiSubjectsIndex');
$router->add('POST',   '/api/blog/subjects',       'BlogController@apiSubjectsStore');
$router->add('PUT',    '/api/blog/subjects/{id}',  'BlogController@apiSubjectsUpdate');
$router->add('DELETE', '/api/blog/subjects/{id}',  'BlogController@apiSubjectsDelete');

$method = $_SERVER['REQUEST_METHOD'];

// Method overriding for PUT/DELETE
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

$router->dispatch($requestPath, $method);
