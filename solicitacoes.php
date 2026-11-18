<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/models/Solicitacao.php';
require_once __DIR__ . '/models/Arvore.php';

requireLogin();

$pageTitle = 'Solicitações';

if (isset($_POST['status_id'])) {
    if (!canEditOperational()) {
        flash('error', 'Perfil com permissão apenas de leitura.');
        redirect('solicitacoes.php');
    }
    Solicitacao::updateStatus((int)$_POST['status_id'], $_POST['status']);
    flash('success', 'Status da solicitação atualizado.');
    redirect('solicitacoes.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arvore_id'])) {
    $arvoreId = (int)$_POST['arvore_id'];
    $solicitante = trim($_POST['solicitante'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($arvoreId <= 0 || $solicitante === '' || $descricao === '') {
        flash('error', 'Preencha a árvore, o nome do solicitante e a descrição.');
        redirect('solicitacoes.php');
    }

    Solicitacao::create([
        'arvore_id'   => $arvoreId,
        'usuario_id'  => (int)$_SESSION['usuario']['id'],
        'solicitante' => $solicitante,
        'telefone'    => trim($_POST['telefone'] ?? ''),
        'tipo'        => $_POST['tipo'] ?? 'Poda',
        'prioridade'  => $_POST['prioridade'] ?? 'Média',
        'status'      => 'Aberta',
        'descricao'   => $descricao,
    ]);

    flash('success', 'Solicitação de serviço cadastrada.');
    redirect('solicitacoes.php');
}

$arvores = Arvore::all();
$solicitacoes = Solicitacao::all();
$statusOptions = ['Aberta', 'Em análise', 'Agendada', 'Concluída', 'Cancelada'];
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<div class="app-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <div class="card" style="margin-bottom: 24px;">
            <h2>Nova solicitação de serviço</h2>
            <form method="post">
                <label>Árvore do inventário *
                    <select name="arvore_id" required>
                        <option value="">Selecione o exemplar</option>
                        <?php foreach ($arvores as $a): ?>
                            <option value="<?= e($a['id']) ?>">
                                <?= e($a['codigo']) ?> — <?= e($a['especie']) ?> (<?= e($a['bairro_nome']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <div class="form-grid">
                    <label>Nome do solicitante *
                        <input name="solicitante" required placeholder="Ex.: Maria Oliveira">
                    </label>

                    <label>Telefone para contato
                        <input name="telefone" placeholder="(14) 99999-0000">
                    </label>

                    <label>Tipo de serviço *
                        <select name="tipo">
                            <option>Poda</option>
                            <option>Manutenção</option>
                            <option>Remoção</option>
                            <option>Vistoria</option>
                        </select>
                    </label>

                    <label>Prioridade da solicitação
                        <select name="prioridade">
                            <option>Baixa</option>
                            <option selected>Média</option>
                            <option>Alta</option>
                            <option>Urgente</option>
                        </select>
                    </label>
                </div>

                <label>Descrição do problema ou serviço necessário *
                    <textarea name="descricao" required placeholder="Descreva a situação (ex.: galhos secos sobre a via pública, raízes bloqueando calçada...)"></textarea>
                </label>

                <div style="margin-top: 6px;">
                    <button class="btn" type="submit">Cadastrar solicitação</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Solicitações registradas</h2>

            <?php if (empty($solicitacoes)): ?>
                <div class="empty-state">
                    <p>Nenhuma solicitação de serviço registrada.</p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Árvore</th>
                                <th>Tipo de serviço</th>
                                <th>Prioridade</th>
                                <th>Status</th>
                                <th>Solicitante</th>
                                <?php if (canEditOperational()): ?>
                                    <th>Atualizar status</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitacoes as $s): ?>
                                <tr>
                                    <td>
                                        <span class="tabular"><strong><?= e($s['codigo']) ?></strong></span> — <?= e($s['especie']) ?><br>
                                        <small class="card-description"><?= e($s['bairro_nome']) ?></small>
                                    </td>
                                    <td><?= e($s['tipo']) ?></td>
                                    <td>
                                        <span class="badge <?= badgeClassPrioridade($s['prioridade']) ?>">
                                            <?= e($s['prioridade']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= badgeClassStatus($s['status']) ?>">
                                            <?= e($s['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= e($s['solicitante']) ?>
                                        <?php if (!empty($s['telefone'])): ?>
                                            <br><small class="card-description tabular"><?= e($s['telefone']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <?php if (canEditOperational()): ?>
                                        <td>
                                            <form method="post" class="form-inline">
                                                <input type="hidden" name="status_id" value="<?= (int)$s['id'] ?>">
                                                <select name="status" style="width: auto; padding: 4px 8px; font-size: 13px;">
                                                    <?php foreach ($statusOptions as $st): ?>
                                                        <option <?= ($s['status'] === $st) ? 'selected' : '' ?>><?= e($st) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button class="btn btn-secondary btn-sm" type="submit">Atualizar</button>
                                            </form>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

<?php include __DIR__ . '/partials/footer.php'; ?>
