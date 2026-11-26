<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/models/Bairro.php';
requireLogin();
$pageTitle = 'Gestão de Bairros';

if (isset($_GET['delete'])) {
    if (!isAdmin()) {
        flash('error', 'Somente o administrador pode excluir bairros.');
        redirect('bairros.php');
    }
    Bairro::delete((int)$_GET['delete']);
    flash('success','Bairro removido.');
    redirect('bairros.php');
}

if (isset($_GET['edit']) && !isAdmin()) {
    flash('error', 'Somente o administrador pode editar bairros.');
    redirect('bairros.php');
}

$edit = isset($_GET['edit']) ? Bairro::find((int)$_GET['edit']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) {
        flash('error', 'Somente o administrador pode salvar bairros.');
        redirect('bairros.php');
    }
    if (!empty($_POST['id'])) {
        Bairro::update((int)$_POST['id'], trim($_POST['nome']), trim($_POST['zona']));
        flash('success','Bairro atualizado.');
    } else {
        Bairro::create(trim($_POST['nome']), trim($_POST['zona']));
        flash('success','Bairro cadastrado.');
    }
    redirect('bairros.php');
}
$bairros = Bairro::all();
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<section class="grid grid-2">
<?php if (isAdmin()): ?>
<div class="card"><h2><?= $edit ? 'Editar bairro' : 'Novo bairro' ?></h2><form method="post"><input type="hidden" name="id" value="<?= e(isset($edit['id']) ? $edit['id'] : '') ?>"><label>Nome<input name="nome" required value="<?= e(isset($edit['nome']) ? $edit['nome'] : '') ?>"></label><label>Zona<select name="zona"><?php foreach(array('Central','Norte','Sul','Leste','Oeste') as $zona): ?><option <?= (isset($edit['zona']) && $edit['zona']===$zona)?'selected':'' ?>><?= e($zona) ?></option><?php endforeach; ?></select></label><button class="btn"><?= $edit ? 'Atualizar bairro' : 'Salvar bairro' ?></button><?php if ($edit): ?><a class="btn secondary" href="bairros.php">Cancelar edição</a><?php endif; ?></form></div>
<?php endif; ?>
<div class="card"><h2>Finalidade</h2><p>Os bairros organizam a estatística territorial, permitindo gerar gráficos e relatórios por região da cidade.</p></div>
</section>
<div class="section-title"><h2>Bairros cadastrados</h2></div>
<div class="card table-wrap"><table><thead><tr><th>Nome</th><th>Zona</th><?php if (isAdmin()): ?><th>Ação</th><?php endif; ?></tr></thead><tbody><?php foreach($bairros as $b): ?><tr><td><?= e($b['nome']) ?></td><td><?= e($b['zona']) ?></td><?php if (isAdmin()): ?><td><a class="btn secondary" href="?edit=<?= e($b['id']) ?>">Editar</a> <a class="btn danger" data-confirm="Remover bairro?" href="?delete=<?= e($b['id']) ?>">Excluir</a></td><?php endif; ?></tr><?php endforeach; ?></tbody></table></div>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
