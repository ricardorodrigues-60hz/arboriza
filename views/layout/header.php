<?php require_once __DIR__ . '/../../config/config.php'; require_once __DIR__ . '/../../core/helpers.php'; ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <?php if (isLoggedIn()): ?>
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">🌳</div>
                <div>
                    <strong>Arboriza</strong>
                    <span>Botucatu</span>
                </div>
            </div>
            <nav>
                <a href="<?= APP_URL ?>/index.php">Dashboard</a>
                <a href="<?= APP_URL ?>/arvores.php">Árvores</a>
                <a href="<?= APP_URL ?>/bairros.php">Bairros</a>
                <?php if (isAdmin()): ?><a href="<?= APP_URL ?>/usuarios.php">Cadastros</a><?php endif; ?>
                <a href="<?= APP_URL ?>/solicitacoes.php">Solicitações</a>
                <a href="<?= APP_URL ?>/fotos.php">Fotos periódicas</a>
                <a href="<?= APP_URL ?>/intervencoes.php">Intervenções</a>
                <a href="<?= APP_URL ?>/relatorios.php">Relatórios PDF</a>
            </nav>
        </aside>
    <?php endif; ?>
    <main class="main-content <?= isLoggedIn() ? '' : 'auth-main' ?>">
        <?php if (isLoggedIn()): ?>
            <header class="topbar">
                <div>
                    <h1><?= isset($pageTitle) ? $pageTitle : APP_NAME ?></h1>
                </div>
                <div class="account-menu">
                    <button class="user-chip account-toggle" type="button" aria-haspopup="true" aria-expanded="false">
                        <span class="avatar">👤</span>
                        <span class="account-name"><?= e(currentUserName()) ?></span>
                        <span class="chevron">▾</span>
                    </button>
                    <div class="account-dropdown" role="menu">
                        <div class="account-info">
                            <strong><?= e(currentUserName()) ?></strong>
                            <span><?= e(currentUserProfile()) ?></span>
                        </div>
                        <a href="<?= APP_URL ?>/logout.php" role="menuitem">🔄 Trocar usuário</a>
                        <a href="<?= APP_URL ?>/logout.php" role="menuitem" class="danger-link">🚪 Sair</a>
                    </div>
                </div>
            </header>
        <?php endif; ?>
        <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
