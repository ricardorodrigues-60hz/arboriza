<?php
/**
 * Menu lateral institucional baseado em permissões por perfil.
 * Arboriza Botucatu - ADR-005
 */
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');

$menuItems = [
    [
        'label' => 'Dashboard',
        'href'  => APP_URL . '/index.php',
        'file'  => 'index.php',
        'icon'  => 'icon-dashboard',
        'admin' => false,
    ],
    [
        'label' => 'Árvores',
        'href'  => APP_URL . '/arvores.php',
        'file'  => 'arvores.php',
        'icon'  => 'icon-tree',
        'admin' => false,
    ],
    [
        'label' => 'Bairros',
        'href'  => APP_URL . '/bairros.php',
        'file'  => 'bairros.php',
        'icon'  => 'icon-map-pin',
        'admin' => false,
    ],
    [
        'label' => 'Solicitações',
        'href'  => APP_URL . '/solicitacoes.php',
        'file'  => 'solicitacoes.php',
        'icon'  => 'icon-clipboard',
        'admin' => false,
    ],
    [
        'label' => 'Relatórios PDF',
        'href'  => APP_URL . '/relatorios.php',
        'file'  => 'relatorios.php',
        'icon'  => 'icon-file-text',
        'admin' => false,
    ],
    [
        'label' => 'Usuários',
        'href'  => APP_URL . '/usuarios.php',
        'file'  => 'usuarios.php',
        'icon'  => 'icon-users',
        'admin' => true,
    ],
];
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="brand-icon">
            <svg class="icon" width="24" height="24"><use href="#icon-tree"></use></svg>
        </div>
        <div>
            <div class="brand-title">Arboriza</div>
            <span class="brand-subtitle">Botucatu</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <?php foreach ($menuItems as $item): ?>
            <?php if ($item['admin'] && !isAdmin()) continue; ?>
            <?php $isActive = ($currentPage === $item['file']); ?>
            <a href="<?= e($item['href']) ?>" class="nav-item <?= $isActive ? 'active' : '' ?>">
                <svg class="icon"><use href="#<?= e($item['icon']) ?>"></use></svg>
                <span><?= e($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
