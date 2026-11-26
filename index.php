<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/config/database.php';
requireLogin();
$pageTitle = 'Dashboard';
$db = Database::getConnection();

$criticas = $db->query("
    SELECT a.id, a.codigo, a.especie, b.nome AS bairro
    FROM arvores a
    JOIN bairros b ON b.id = a.bairro_id
    WHERE a.estado_conservacao = 'Crítico'
    ORDER BY a.criado_em DESC
    LIMIT 6
")->fetchAll();

$urgentes = $db->query("
    SELECT s.id, s.tipo, s.prioridade, s.status, a.codigo
    FROM solicitacoes s
    JOIN arvores a ON a.id = s.arvore_id
    WHERE s.status IN ('Aberta', 'Em análise')
       OR s.prioridade = 'Urgente'
    ORDER BY FIELD(s.prioridade, 'Urgente', 'Alta', 'Média', 'Baixa'), s.criado_em DESC
    LIMIT 6
")->fetchAll();

$stats = [
    'arvores'      => (int)$db->query('SELECT COUNT(*) FROM arvores')->fetchColumn(),
    'bairros'      => (int)$db->query('SELECT COUNT(*) FROM bairros')->fetchColumn(),
    'solicitacoes' => (int)$db->query('SELECT COUNT(*) FROM solicitacoes')->fetchColumn(),
    'fotos'        => (int)$db->query('SELECT COUNT(*) FROM fotos_arvores')->fetchColumn(),
    'intervencoes' => (int)$db->query('SELECT COUNT(*) FROM intervencoes')->fetchColumn(),
];

$recentes = $db->query('
    SELECT a.codigo, a.especie, a.estado_conservacao, b.nome AS bairro
    FROM arvores a
    JOIN bairros b ON b.id = a.bairro_id
    ORDER BY a.criado_em DESC
    LIMIT 5
')->fetchAll();
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>

<section class="grid grid-2">
    <div class="card">
        <div class="section-title" style="margin-top:0;">
            <h2>Árvores em estado crítico</h2>
            <a class="btn secondary btn-sm" href="arvores.php">Ver todas</a>
        </div>
        <?php if (empty($criticas)): ?>
            <p class="empty-state">Nenhuma árvore em estado crítico.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Código</th><th>Espécie</th><th>Bairro</th><?php if (canEditOperational()): ?><th>Ação</th><?php endif; ?></tr></thead>
                    <tbody>
                    <?php foreach ($criticas as $c): ?>
                        <tr>
                            <td><?= e($c['codigo']) ?></td>
                            <td><?= e($c['especie']) ?></td>
                            <td><?= e($c['bairro']) ?></td>
                            <?php if (canEditOperational()): ?>
                                <td><a class="btn secondary btn-sm" href="arvores.php?edit=<?= (int)$c['id'] ?>">Abrir</a></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="section-title" style="margin-top:0;">
            <h2>Solicitações urgentes e abertas</h2>
            <a class="btn secondary btn-sm" href="solicitacoes.php">Ver todas</a>
        </div>
        <?php if (empty($urgentes)): ?>
            <p class="empty-state">Nenhuma solicitação pendente. <a href="solicitacoes.php">Nova solicitação</a></p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Árvore</th><th>Tipo</th><th>Prioridade</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($urgentes as $u): ?>
                        <tr>
                            <td><?= e($u['codigo']) ?></td>
                            <td><?= e($u['tipo']) ?></td>
                            <td><span class="badge <?= e(badgeClassPrioridade($u['prioridade'])) ?>"><?= e($u['prioridade']) ?></span></td>
                            <td><span class="badge <?= e(badgeClassStatus($u['status'])) ?>"><?= e($u['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="stats-strip" aria-label="Totais do inventário">
    <div class="stat-item"><span>Árvores</span><strong><?= $stats['arvores'] ?></strong></div>
    <div class="stat-item"><span>Bairros</span><strong><?= $stats['bairros'] ?></strong></div>
    <div class="stat-item"><span>Solicitações</span><strong><?= $stats['solicitacoes'] ?></strong></div>
    <div class="stat-item"><span>Fotos</span><strong><?= $stats['fotos'] ?></strong></div>
    <div class="stat-item"><span>Intervenções</span><strong><?= $stats['intervencoes'] ?></strong></div>
</div>

<section class="grid grid-3">
    <div class="card chart-card">
        <h2>Árvores por bairro</h2>
        <div class="chart-wrap"><canvas id="chartBairros"></canvas></div>
    </div>
    <div class="card chart-card">
        <h2>Estado de conservação</h2>
        <div class="chart-wrap"><canvas id="chartConservacao"></canvas></div>
    </div>
    <div class="card chart-card">
        <h2>Solicitações por status</h2>
        <div class="chart-wrap"><canvas id="chartSolicitacoes"></canvas></div>
    </div>
</section>

<div class="section-title">
    <h2>Últimas árvores cadastradas</h2>
    <a class="btn secondary btn-sm" href="arvores.php">Ver todas</a>
</div>
<div class="card table-wrap">
    <?php if (empty($recentes)): ?>
        <p class="empty-state">Nenhuma árvore cadastrada. <?php if (canEditOperational()): ?><a href="arvores.php">Cadastrar árvore</a><?php endif; ?></p>
    <?php else: ?>
        <table>
            <thead><tr><th>Código</th><th>Espécie</th><th>Bairro</th><th>Estado</th></tr></thead>
            <tbody>
            <?php foreach ($recentes as $item): ?>
                <tr>
                    <td><?= e($item['codigo']) ?></td>
                    <td><?= e($item['especie']) ?></td>
                    <td><?= e($item['bairro']) ?></td>
                    <td><span class="badge <?= e(badgeClassEstado($item['estado_conservacao'])) ?>"><?= e($item['estado_conservacao']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="<?= APP_URL ?>/node_modules/chart.js/dist/chart.umd.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/dashboard-charts.js"></script>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
