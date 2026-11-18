<?php
/**
 * Barra superior institucional e menu de usuário.
 * Arboriza Botucatu - ADR-005
 */
?>
<header class="topbar">
    <div class="topbar-title">
        <?= isset($pageTitle) ? e($pageTitle) : e(APP_NAME) ?>
    </div>
    <div class="account-menu">
        <button class="account-toggle" type="button" aria-haspopup="true" aria-expanded="false">
            <svg class="icon icon-user"><use href="#icon-user"></use></svg>
            <span class="account-name"><?= e(currentUserName()) ?></span>
            <svg class="chevron"><use href="#icon-chevron-down"></use></svg>
        </button>
        <div class="account-dropdown" role="menu">
            <div class="account-dropdown-header">
                <span class="account-dropdown-name"><?= e(currentUserName()) ?></span>
                <span class="account-dropdown-role">Perfil: <?= e(currentUserProfile()) ?></span>
            </div>
            <a href="<?= APP_URL ?>/logout.php" role="menuitem">
                <svg class="icon" width="16" height="16"><use href="#icon-user"></use></svg>
                <span>Trocar usuário</span>
            </a>
            <a href="<?= APP_URL ?>/logout.php" role="menuitem" class="danger-action">
                <svg class="icon" width="16" height="16"><use href="#icon-log-out"></use></svg>
                <span>Sair</span>
            </a>
        </div>
    </div>
</header>

<main class="main-content">
    <?php if ($msg = flash('success')): ?>
        <div class="alert alert-success" role="alert">
            <svg class="icon" width="18" height="18"><use href="#icon-check"></use></svg>
            <span><?= e($msg) ?></span>
        </div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert-error" role="alert">
            <svg class="icon" width="18" height="18"><use href="#icon-alert"></use></svg>
            <span><?= e($msg) ?></span>
        </div>
    <?php endif; ?>
