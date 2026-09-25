<?php

use Core\Env;

require_once __DIR__ . '/../core/Env.php';

// Le credenziali vivono in `.env` (escluso da git). Vedi `.env.example`.
Env::load(__DIR__ . '/../.env');

define('APP_DEBUG', Env::bool('APP_DEBUG'));

define('DB_HOST', Env::require('DB_HOST'));
define('DB_USER', Env::require('DB_USER'));
define('DB_PASS', Env::get('DB_PASS', ''));
define('DB_NAME', Env::require('DB_NAME'));

// La richiesta corrente arriva in HTTPS? Dietro un proxy (es. Aruba) PHP può vedere
// la connessione come HTTP: si guardano anche gli header impostati dal proxy.
define('IS_HTTPS', ($_SERVER['HTTPS'] ?? '') === 'on'
    || ($_SERVER['SERVER_PORT'] ?? '') === '443'
    || strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
    || strtolower($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '') === 'on'
    || strtolower($_SERVER['REQUEST_SCHEME'] ?? '') === 'https');

// URL pubblico del sito, senza slash finale (es. https://www.example.com).
// Se la pagina è servita in HTTPS ma il .env dice http://, si allinea lo schema:
// altrimenti il browser blocca come "mixed content" le chiamate fetch() verso l'API.
$baseUrl = rtrim(Env::require('BASE_URL'), '/');
if (IS_HTTPS && str_starts_with($baseUrl, 'http://')) {
    $baseUrl = 'https://' . substr($baseUrl, 7);
}
define('BASE_URL', $baseUrl);
unset($baseUrl);

// Solo il path di BASE_URL (es. "" o "/sito"): gli script lo usano per chiamare
// l'API sulla stessa origine della pagina, qualunque sia lo schema o l'host.
define('BASE_PATH', rtrim((string) parse_url(BASE_URL, PHP_URL_PATH), '/'));

// Publisher ID Google AdSense (opzionale: se vuoto lo script non viene incluso)
define('ADSENSE_CLIENT', Env::get('ADSENSE_CLIENT', ''));
