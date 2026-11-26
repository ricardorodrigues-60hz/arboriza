<?php
require_once __DIR__ . '/../../partials/head.php';
require_once __DIR__ . '/../../partials/sidebar.php';
?>
<main class="main-content <?= isLoggedIn() ? '' : 'auth-main' ?>">
<?php require_once __DIR__ . '/../../partials/topbar.php'; ?>
<?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
