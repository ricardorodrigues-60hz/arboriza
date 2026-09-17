<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
requireLogin();
$pageTitle = 'Relatórios Dinâmicos em PDF';
$reports = [
    ['reports/arvores_por_bairro.php','Árvores por bairro','Quantidade de árvores cadastradas em cada bairro.'],
    ['reports/especies.php','Espécies predominantes','Ranking das espécies registradas no inventário.'],
    ['reports/solicitacoes.php','Solicitações de manutenção','Lista e indicadores dos pedidos de poda/manutenção.'],
    ['reports/intervencoes.php','Histórico de intervenções','Registros de podas, tratamentos, remoções e custos.'],
];
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<div class="card"><h2>PDFs gerados dinamicamente</h2><div class="report-list"><?php foreach($reports as $r): ?><div class="report-item"><div><strong><?= e($r[1]) ?></strong><p><?= e($r[2]) ?></p></div><a target="_blank" class="btn" href="<?= e($r[0]) ?>">Gerar PDF</a></div><?php endforeach; ?></div></div>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
