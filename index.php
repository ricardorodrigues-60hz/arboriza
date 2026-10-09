<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/config/database.php';
requireLogin();
$pageTitle = 'Dashboard Ambiental';
$db = Database::getConnection();
$stats = [
    'arvores' => $db->query('SELECT COUNT(*) FROM arvores')->fetchColumn(),
    'bairros' => $db->query('SELECT COUNT(*) FROM bairros')->fetchColumn(),
    'solicitacoes' => $db->query('SELECT COUNT(*) FROM solicitacoes')->fetchColumn(),
    'intervencoes' => $db->query('SELECT COUNT(*) FROM intervencoes')->fetchColumn(),
];
$recentes = $db->query('SELECT a.codigo, a.especie, a.estado_conservacao, b.nome bairro FROM arvores a JOIN bairros b ON b.id = a.bairro_id ORDER BY a.criado_em DESC LIMIT 5')->fetchAll();
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<section class="hero">
    <h2>Inventário inteligente da arborização urbana</h2>
</section>
<section class="grid grid-4" style="margin-top: 22px;">
    <div class="card stat"><div><span>Árvores cadastradas     </span><strong><?= e($stats['arvores']) ?></strong></div><div class="icon">🌲</div></div>
    <div class="card stat"><div><span>Bairros monitorados    </span><strong><?= e($stats['bairros']) ?></strong></div><div class="icon">📍</div></div>
    <div class="card stat"><div><span>Solicitações   </span><strong><?= e($stats['solicitacoes']) ?></strong></div><div class="icon">🛠️</div></div>
    <div class="card stat"><div><span>Intervenções   </span><strong><?= e($stats['intervencoes']) ?></strong></div><div class="icon">📋</div></div>
</section>
<div class="section-title"><h2>Gráficos dinâmicos exigidos</h2></div>
<section class="grid grid-2">
    <div class="card chart-card"><canvas id="chartBairros"></canvas></div>
    <div class="card chart-card"><canvas id="chartEspecies"></canvas></div>
    <div class="card chart-card"><canvas id="chartConservacao"></canvas></div>
    <div class="card chart-card"><canvas id="chartSolicitacoes"></canvas></div>
</section>
<div class="section-title"><h2>Últimas árvores cadastradas</h2><a class="btn secondary" href="arvores.php">Ver todas</a></div>
<div class="card table-wrap">
    <table>
        <thead><tr><th>Código</th><th>Espécie</th><th>Bairro</th><th>Estado</th></tr></thead>
        <tbody>
        <?php foreach ($recentes as $item): ?>
            <tr><td><?= e($item['codigo']) ?></td><td><?= e($item['especie']) ?></td><td><?= e($item['bairro']) ?></td><td><span class="badge"><?= e($item['estado_conservacao']) ?></span></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="<?= APP_URL ?>/assets/js/dashboard-charts.js"></script>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
