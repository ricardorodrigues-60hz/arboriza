<?php
/**
 * Partial: barra lateral de navegação controlada por perfil (ADR-005).
 */
if (!isLoggedIn()) {
    return;
}

$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');

$menuItems = [
    ['href' => 'index.php',        'icon' => 'dashboard',      'label' => 'Dashboard',        'perfil' => 'Leitor'],
    ['href' => 'arvores.php',      'icon' => 'tree',           'label' => 'Árvores',          'perfil' => 'Leitor'],
    ['href' => 'bairros.php',      'icon' => 'map-pin',        'label' => 'Bairros',          'perfil' => 'Leitor'],
    ['href' => 'solicitacoes.php', 'icon' => 'clipboard-list', 'label' => 'Solicitações',     'perfil' => 'Leitor'],
    ['href' => 'fotos.php',        'icon' => 'camera',         'label' => 'Fotos periódicas', 'perfil' => 'Leitor'],
    ['href' => 'intervencoes.php', 'icon' => 'wrench',         'label' => 'Intervenções',     'perfil' => 'Leitor'],
    ['href' => 'relatorios.php',   'icon' => 'file-text',      'label' => 'Relatórios PDF',   'perfil' => 'Leitor'],
    ['href' => 'usuarios.php',     'icon' => 'users',          'label' => 'Usuários',         'perfil' => 'Administrador'],
];
?>
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-icon">
            <svg class="icon" aria-hidden="true"><use href="<?= APP_URL ?>/assets/icons/sprite.svg#tree"></use></svg>
        </div>
        <div>
            <strong>Arboriza</strong>
            <span>Botucatu</span>
        </div>
    </div>
    <nav aria-label="Menu principal">
        <?php foreach ($menuItems as $item): ?>
            <?php if ($item['perfil'] === 'Administrador' && !isAdmin()) { continue; } ?>
            <?php $isActive = ($currentScript === $item['href']); ?>
            <a href="<?= APP_URL . '/' . e($item['href']) ?>"<?= $isActive ? ' class="active" aria-current="page"' : '' ?>>
                <svg class="icon" aria-hidden="true"><use href="<?= APP_URL ?>/assets/icons/sprite.svg#<?= e($item['icon']) ?>"></use></svg>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
