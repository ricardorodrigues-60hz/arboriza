<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/models/Solicitacao.php';
require_once __DIR__ . '/models/Arvore.php';
requireLogin();
$pageTitle = 'Solicitações de Poda e Manutenção';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
}

if (isset($_POST['status_id'])) {
    if (!canEditOperational()) {
        flash('error','Leitor pode apenas visualizar solicitações.');
        redirect('solicitacoes.php');
    }
    Solicitacao::updateStatus((int)$_POST['status_id'], $_POST['status']);
    flash('success','Status atualizado.');
    redirect('solicitacoes.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['arvore_id'])) {
    Solicitacao::create(array(
        'arvore_id'=>(int)$_POST['arvore_id'],
        'solicitante'=>trim($_POST['solicitante']),
        'telefone'=>trim($_POST['telefone']),
        'tipo'=>$_POST['tipo'],
        'prioridade'=>$_POST['prioridade'],
        'status'=>'Aberta',
        'descricao'=>trim($_POST['descricao'])
    ));
    flash('success','Solicitação cadastrada.');
    redirect('solicitacoes.php');
}
$arvores = Arvore::all(); $solicitacoes = Solicitacao::all();
$statusOptions = array('Aberta','Em análise','Agendada','Concluída','Cancelada');
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<div class="card">
    <h2>Nova solicitação</h2>
    <form method="post">
        <?= csrf_field() ?>
        <label>Árvore<select name="arvore_id" required><?php foreach($arvores as $a): ?><option value="<?= e($a['id']) ?>"><?= e($a['codigo'].' - '.$a['especie'].' / '.$a['bairro_nome']) ?></option><?php endforeach; ?></select></label>
        <div class="form-grid">
            <label>Solicitante<input name="solicitante" required></label>
            <label>Telefone<input name="telefone"></label>
            <label>Tipo<select name="tipo"><option>Poda</option><option>Manutenção</option><option>Remoção</option><option>Vistoria</option></select></label>
            <label>Prioridade<select name="prioridade"><option>Baixa</option><option>Média</option><option>Alta</option><option>Urgente</option></select></label>
        </div>
        <label>Descrição<textarea name="descricao" required></textarea></label>
        <div><button class="btn" type="submit">Nova solicitação</button></div>
    </form>
</div>
<div class="section-title"><h2>Solicitações registradas</h2></div>
<div class="card table-wrap">
    <?php if (empty($solicitacoes)): ?>
        <p class="empty-state">Nenhuma solicitação registrada.</p>
    <?php else: ?>
    <table><thead><tr><th>Árvore</th><th>Tipo</th><th>Prioridade</th><th>Status</th><th>Solicitante</th><?php if (canEditOperational()): ?><th>Atualizar</th><?php endif; ?></tr></thead><tbody><?php foreach($solicitacoes as $s): ?><tr><td><?= e($s['codigo'].' - '.$s['especie']) ?><br><small><?= e($s['bairro_nome']) ?></small></td><td><?= e($s['tipo']) ?></td><td><span class="badge <?= e(badgeClassPrioridade($s['prioridade'])) ?>"><?= e($s['prioridade']) ?></span></td><td><span class="badge <?= e(badgeClassStatus($s['status'])) ?>"><?= e($s['status']) ?></span></td><td><?= e($s['solicitante']) ?></td><?php if (canEditOperational()): ?><td><form method="post" class="inline-form"><?= csrf_field() ?><input type="hidden" name="status_id" value="<?= e($s['id']) ?>"><select name="status"><?php foreach($statusOptions as $st): ?><option <?= $s['status']===$st?'selected':'' ?>><?= e($st) ?></option><?php endforeach; ?></select><button class="btn secondary btn-sm" type="submit">Salvar</button></form></td><?php endif; ?></tr><?php endforeach; ?></tbody></table>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
