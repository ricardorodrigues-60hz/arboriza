<?php
/**
 * Configuração principal do projeto Arboriza Botucatu.
 */

define('APP_NAME', 'Arboriza Botucatu');
define('APP_URL', 'http://localhost/arboriza');

// Carrega configurações locais não versionadas se existirem
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

define('DB_HOST', defined('DB_HOST_LOCAL') ? DB_HOST_LOCAL : 'localhost');
define('DB_NAME', defined('DB_NAME_LOCAL') ? DB_NAME_LOCAL : 'arboriza_botucatu');
define('DB_USER', defined('DB_USER_LOCAL') ? DB_USER_LOCAL : 'root');
define('DB_PASS', defined('DB_PASS_LOCAL') ? DB_PASS_LOCAL : '');

define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('PUBLIC_UPLOAD_PATH', APP_URL . '/uploads/');

date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
