<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';

requireLogin();

$pageTitle = 'Relatórios em PDF';

$reports = [
    [
        'file'        => 'reports/arvores_por_bairro.php',
        'title'       => 'Árvores por bairro',
        'description' => 'Contagem e distribuição quantitativa de exemplares cadastrados em cada bairro do município.',
    ],
    [
        'file'        => 'reports/especies.php',
        'title'       => 'Espécies predominantes',
        'description' => 'Classificação das espécies botânicas mais frequentes no inventário com média de idade.',
    ],
    [
        'file'        => 'reports/solicitacoes.php',
        'title'       => 'Solicitações de manutenção',
        'description' => 'Histórico e status dos pedidos de poda, vistoria e remoção emitidos pela população.',
    ],
];
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<div class="app-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <div class="card">
            <h2>Relatórios oficiais em PDF</h2>
            <p class="card-description">Selecione o relatório desejado para gerar o documento oficial para download ou impressão.</p>

            <div style="display: flex; flex-direction: column; gap: 14px; margin-top: 16px;">
                <?php foreach ($reports as $r): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid var(--color-border); border-radius: var(--radius-md); background-color: var(--color-surface);">
                        <div>
                            <strong><?= e($r['title']) ?></strong>
                            <p class="card-description" style="margin: 4px 0 0;"><?= e($r['description']) ?></p>
                        </div>
                        <a target="_blank" class="btn btn-secondary" href="<?= e($r['file']) ?>">
                            <svg class="icon" width="16" height="16"><use href="#icon-file-text"></use></svg>
                            <span>Gerar PDF</span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

<?php include __DIR__ . '/partials/footer.php'; ?>
