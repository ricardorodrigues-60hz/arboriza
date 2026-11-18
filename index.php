<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/config/database.php';

requireLogin();

$pageTitle = 'Dashboard';
$db = Database::getConnection();

// 1. Ações prioritárias no topo (ADR-006)
// Árvores em estado crítico
$arvoresCriticas = $db->query("
    SELECT a.id, a.codigo, a.especie, a.endereco, b.nome AS bairro
    FROM arvores a
    JOIN bairros b ON b.id = a.bairro_id
    WHERE a.estado_conservacao = 'Crítico'
    ORDER BY a.criado_em DESC
")->fetchAll();

// Solicitações urgentes e abertas
$solicitacoesUrgentes = $db->query("
    SELECT s.id, s.tipo, s.solicitante, s.criado_em, a.codigo, a.especie, b.nome AS bairro
    FROM solicitacoes s
    JOIN arvores a ON a.id = s.arvore_id
    JOIN bairros b ON b.id = a.bairro_id
    WHERE s.prioridade = 'Urgente' AND s.status = 'Aberta'
    ORDER BY s.criado_em ASC
")->fetchAll();

// 2. Linha discreta de totais (ADR-006)
$totais = [
    'arvores'      => (int)$db->query('SELECT COUNT(*) FROM arvores')->fetchColumn(),
    'bairros'      => (int)$db->query('SELECT COUNT(*) FROM bairros')->fetchColumn(),
    'solicitacoes' => (int)$db->query('SELECT COUNT(*) FROM solicitacoes')->fetchColumn(),
];
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<div class="app-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <!-- Listas Acionáveis no Topo (ADR-006) -->
        <div class="grid grid-2">
            <!-- Árvores em Estado Crítico -->
            <div class="card urgent-banner">
                <h2>Árvores em estado crítico</h2>
                <?php if (empty($arvoresCriticas)): ?>
                    <p class="card-description">Nenhuma árvore em estado crítico no momento.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Espécie</th>
                                    <th>Bairro</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($arvoresCriticas as $ac): ?>
                                    <tr>
                                        <td class="tabular"><strong><?= e($ac['codigo']) ?></strong></td>
                                        <td><?= e($ac['especie']) ?></td>
                                        <td><?= e($ac['bairro']) ?></td>
                                        <td>
                                            <a href="arvores.php?edit=<?= (int)$ac['id'] ?>" class="btn btn-secondary btn-sm">Ver árvore</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Solicitações Urgentes em Aberto -->
            <div class="card urgent-banner">
                <h2>Solicitações urgentes em aberto</h2>
                <?php if (empty($solicitacoesUrgentes)): ?>
                    <p class="card-description">Nenhuma solicitação urgente pendente.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Árvore</th>
                                    <th>Serviço</th>
                                    <th>Solicitante</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($solicitacoesUrgentes as $su): ?>
                                    <tr>
                                        <td>
                                            <span class="tabular"><strong><?= e($su['codigo']) ?></strong></span><br>
                                            <small class="card-description"><?= e($su['bairro']) ?></small>
                                        </td>
                                        <td><span class="badge badge-prioridade-urgente"><?= e($su['tipo']) ?></span></td>
                                        <td><?= e($su['solicitante']) ?></td>
                                        <td>
                                            <a href="solicitacoes.php" class="btn btn-secondary btn-sm">Tratar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Linha Discreta de Totais (ADR-006) -->
        <div class="metrics-row">
            <div class="metric-item"><strong><?= $totais['arvores'] ?></strong> árvores cadastradas</div>
            <div>·</div>
            <div class="metric-item"><strong><?= $totais['bairros'] ?></strong> bairros monitorados</div>
            <div>·</div>
            <div class="metric-item"><strong><?= $totais['solicitacoes'] ?></strong> solicitações registradas</div>
        </div>

        <!-- Gráficos com Significado (ADR-006) -->
        <div class="grid grid-3">
            <div class="card chart-box">
                <h2>Árvores por bairro</h2>
                <canvas id="chartBairros"></canvas>
            </div>
            <div class="card chart-box">
                <h2>Estado de conservação</h2>
                <canvas id="chartConservacao"></canvas>
            </div>
            <div class="card chart-box">
                <h2>Solicitações por status</h2>
                <canvas id="chartSolicitacoes"></canvas>
            </div>
        </div>

        <script src="<?= APP_URL ?>/assets/js/dashboard-charts.js"></script>
<?php include __DIR__ . '/partials/footer.php'; ?>
