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

// URL pubblico del sito, senza slash finale (es. https://www.example.com)
define('BASE_URL', rtrim(Env::require('BASE_URL'), '/'));

// Publisher ID Google AdSense (opzionale: se vuoto lo script non viene incluso)
define('ADSENSE_CLIENT', Env::get('ADSENSE_CLIENT', ''));
