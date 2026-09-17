<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Auth.php';
Auth::logout();
header('Location: login.php');
exit;
