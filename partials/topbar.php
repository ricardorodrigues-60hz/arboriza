<?php
/**
 * Partial: barra superior com título da página e menu de conta (ADR-005).
 */
if (!isLoggedIn()) {
    return;
}
?>
<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" type="button" aria-label="Abrir menu" aria-controls="sidebar" aria-expanded="false">
            <svg class="icon" aria-hidden="true"><use href="<?= APP_URL ?>/assets/icons/sprite.svg#menu"></use></svg>
        </button>
        <h1><?= isset($pageTitle) ? e($pageTitle) : e(APP_NAME) ?></h1>
    </div>
    <div class="account-menu">
        <button class="user-chip account-toggle" type="button" aria-haspopup="true" aria-expanded="false">
            <svg class="icon" aria-hidden="true"><use href="<?= APP_URL ?>/assets/icons/sprite.svg#user"></use></svg>
            <span class="account-name"><?= e(currentUserName()) ?></span>
            <svg class="icon" aria-hidden="true"><use href="<?= APP_URL ?>/assets/icons/sprite.svg#chevron-down"></use></svg>
        </button>
        <div class="account-dropdown" role="menu">
            <div class="account-info">
                <strong><?= e(currentUserName()) ?></strong>
                <span><?= e(currentUserProfile()) ?></span>
            </div>
            <a href="<?= APP_URL ?>/logout.php" role="menuitem" class="danger-link">
                <svg class="icon" aria-hidden="true"><use href="<?= APP_URL ?>/assets/icons/sprite.svg#log-out"></use></svg>
                <span>Sair</span>
            </a>
        </div>
    </div>
</header>
