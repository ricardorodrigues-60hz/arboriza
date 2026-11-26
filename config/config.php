<?php
// Configuração principal do projeto Arboriza Botucatu.
// Ajuste usuário/senha caso o seu MySQL do XAMPP seja diferente.
define('APP_NAME', 'Arboriza Botucatu');
define('APP_URL', 'http://localhost/arboriza');
define('DB_HOST', 'localhost');
define('DB_NAME', 'arboriza_botucatu');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('PUBLIC_UPLOAD_PATH', APP_URL . '/uploads/');

date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
