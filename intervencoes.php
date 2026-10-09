<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/models/Intervencao.php';
require_once __DIR__ . '/models/Arvore.php';
requireLogin();
$pageTitle = 'Histórico de Intervenções';

if (isset($_GET['delete'])) {
    if (!isAdmin()) {
        flash('error','Somente o administrador pode excluir intervenções.');
        redirect('intervencoes.php');
    }
    Intervencao::delete((int)$_GET['delete']);
    flash('success','Intervenção excluída.');
    redirect('intervencoes.php');
}

if (isset($_GET['edit']) && !isAdmin()) {
    flash('error','Somente o administrador pode editar intervenções.');
    redirect('intervencoes.php');
}

$edit = isset($_GET['edit']) ? Intervencao::find((int)$_GET['edit']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!canEditOperational()) {
        flash('error','Leitor pode apenas visualizar o histórico.');
        redirect('intervencoes.php');
    }
    $data = array(
        'arvore_id'=>(int)$_POST['arvore_id'],
        'usuario_id'=>!empty($_POST['id']) && isset($edit['usuario_id']) ? (int)$edit['usuario_id'] : (int)$_SESSION['usuario']['id'],
        'tipo'=>$_POST['tipo'],
        'responsavel'=>trim($_POST['responsavel']),
        'data_intervencao'=>$_POST['data_intervencao'],
        'custo_estimado'=>(float)$_POST['custo_estimado'],
        'descricao'=>trim($_POST['descricao'])
    );
    if (!empty($_POST['id'])) {
        if (!isAdmin()) {
            flash('error','Somente o administrador pode editar intervenções.');
            redirect('intervencoes.php');
        }
        Intervencao::update((int)$_POST['id'], $data);
        flash('success','Intervenção atualizada.');
    } else {
        Intervencao::create($data);
        flash('success','Intervenção registrada.');
    }
    redirect('intervencoes.php');
}
$arvores=Arvore::all(); $intervencoes=Intervencao::all();
$tipos = array('Poda corretiva','Poda preventiva','Tratamento fitossanitário','Remoção','Plantio');
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<?php if (canEditOperational()): ?>
<div class="grid grid-2"><div class="card"><h2><?= $edit ? 'Editar intervenção' : 'Nova intervenção' ?></h2>
        <form method="post"><input type="hidden" name="id" value="
        <?= e(isset($edit['id']) ? $edit['id'] : '') ?>"><label>Árvore<select name="arvore_id" required>
            <?php foreach($arvores as $a): ?>
                    <option value="<?= e($a['id']) ?>" <?= ((isset($edit['arvore_id']) ? $edit['arvore_id'] : '') == $a['id'])?'selected':'' ?>>
                    <?= e($a['codigo'].' - '.$a['especie']) ?></option><?php endforeach; ?>
                </select></label><div class="form-grid"><label>Tipo<select name="tipo">
                    <?php foreach($tipos as $tipo): ?>
                            <option <?= (isset($edit['tipo']) && $edit['tipo']===$tipo)?'selected':'' ?>><?= e($tipo) ?></option>
                                <?php endforeach; ?></select></label><label>Responsável<input name="responsavel" required value="<?= e(isset($edit['responsavel']) ? $edit['responsavel'] : '') ?>">
                                </label><label>Data<input type="date" name="data_intervencao" required value="<?= e(isset($edit['data_intervencao']) ? $edit['data_intervencao'] : date('Y-m-d')) ?>">
                                </label><label>Custo estimado<input type="number" step="0.01" name="custo_estimado" value="<?= e(isset($edit['custo_estimado']) ? $edit['custo_estimado'] : '0') ?>"></label></div>
            <label>Descrição<textarea name="descricao"><?= e(isset($edit['descricao']) ? $edit['descricao'] : '') ?></textarea></label>
            <button class="btn"><?= $edit ? 'Atualizar intervenção' : 'Registrar intervenção' ?></button><?php if ($edit): ?>
            <a class="btn secondary" href="intervencoes.php">Cancelar edição</a><?php endif; ?></form></div><div class="card">
<?php else: ?>
<section class="hero"><h2>Histórico de intervenções</h2><p>Seu perfil de Leitor permite acompanhar as ações realizadas nas árvores cadastradas.</p></section>
<?php endif; ?>
<div class="section-title"><h2>Histórico registrado</h2></div><div class="card table-wrap"><table><thead><tr><th>Data</th><th>Árvore</th><th>Tipo</th><th>Responsável</th><th>Custo</th><?php if (isAdmin()): ?><th>Ações</th><?php endif; ?></tr></thead><tbody><?php foreach($intervencoes as $i): ?><tr><td><?= date('d/m/Y', strtotime($i['data_intervencao'])) ?></td><td><?= e($i['codigo'].' - '.$i['especie']) ?></td><td><?= e($i['tipo']) ?></td><td><?= e($i['responsavel']) ?></td><td>R$ <?= number_format((float)$i['custo_estimado'],2,',','.') ?></td><?php if (isAdmin()): ?><td><a class="btn secondary" href="?edit=<?= e($i['id']) ?>">Editar</a> <a class="btn danger" data-confirm="Excluir esta intervenção?" href="?delete=<?= e($i['id']) ?>">Excluir</a></td><?php endif; ?></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
