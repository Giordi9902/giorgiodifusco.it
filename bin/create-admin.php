<?php

/**
 * Crea (o promuove) un utente amministratore.
 * Uso: php bin/create-admin.php "Nome Cognome" email@example.com
 * La password viene chiesta in modo interattivo.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'Core\\')) {
        require __DIR__ . '/../core/' . substr($class, 5) . '.php';
    } elseif (str_starts_with($class, 'App\\')) {
        require __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    }
});

require __DIR__ . '/../config/config.php';

[$script, $name, $email] = array_pad($argv, 3, null);
if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Uso: php {$script} \"Nome Cognome\" email@example.com\n");
    exit(1);
}

fwrite(STDOUT, 'Password (min 8 caratteri): ');
system('stty -echo 2>/dev/null');
$password = trim((string) fgets(STDIN));
system('stty echo 2>/dev/null');
fwrite(STDOUT, "\n");

if (strlen($password) < 8) {
    fwrite(STDERR, "Password troppo corta.\n");
    exit(1);
}

$users = new App\Models\User();
if ($existing = $users->findByEmail($email)) {
    $db = Core\Database::getInstance()->getConnection();
    $db->prepare("UPDATE users SET role = 'ADMIN', password = ? WHERE id = ?")
        ->execute([password_hash($password, PASSWORD_DEFAULT), $existing['id']]);
    fwrite(STDOUT, "Utente esistente promosso ad ADMIN.\n");
} else {
    $users->create($name, $email, $password, 'ADMIN');
    fwrite(STDOUT, "Admin creato.\n");
}
