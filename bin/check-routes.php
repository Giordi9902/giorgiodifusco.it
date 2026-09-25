<?php

/**
 * Verifica che ogni rotta registrata in public/index.php punti a un
 * controller e a un metodo esistenti. Usato dalla CI.
 * Uso: php bin/check-routes.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
$source = file_get_contents($root . '/public/index.php');

preg_match_all(
    "/->add\(\s*'(GET|POST|PUT|DELETE)'\s*,\s*'([^']+)'\s*,\s*'(\w+)@(\w+)'\s*\)/",
    $source,
    $routes,
    PREG_SET_ORDER
);

if (!$routes) {
    fwrite(STDERR, "Nessuna rotta trovata in public/index.php\n");
    exit(1);
}

$errors = 0;
foreach ($routes as [, $method, $path, $controller, $action]) {
    $file = "{$root}/app/Controllers/{$controller}.php";
    if (!is_file($file)) {
        fwrite(STDERR, "✖ {$method} {$path}: controller {$controller} non trovato\n");
        $errors++;
        continue;
    }
    if (!preg_match('/public\s+function\s+' . preg_quote($action, '/') . '\s*\(/', file_get_contents($file))) {
        fwrite(STDERR, "✖ {$method} {$path}: metodo {$controller}::{$action} non trovato\n");
        $errors++;
    }
}

echo count($routes) . " rotte verificate, {$errors} errori\n";
exit($errors > 0 ? 1 : 0);
