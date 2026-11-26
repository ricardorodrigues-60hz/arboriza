<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/models/Arvore.php';
require_once __DIR__ . '/models/Bairro.php';
requireLogin();
$pageTitle = 'Cadastro de Árvores';

if (isset($_GET['delete'])) {
    if (!isAdmin()) {
        flash('error', 'Somente o administrador pode excluir árvores.');
        redirect('arvores.php');
    }
    Arvore::delete((int)$_GET['delete']);
    flash('success', 'Árvore removida com sucesso.');
    redirect('arvores.php');
}

if (isset($_GET['edit']) && isLeitor()) {
    flash('error', 'Leitor pode apenas visualizar as árvores.');
    redirect('arvores.php');
}

$edit = isset($_GET['edit']) ? Arvore::find((int)$_GET['edit']) : null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!canEditOperational()) {
        flash('error', 'Leitor pode apenas visualizar as árvores.');
        redirect('arvores.php');
    }
    $data = array(
        'codigo' => trim($_POST['codigo']),
        'especie' => trim($_POST['especie']),
        'nome_popular' => trim($_POST['nome_popular']),
        'idade_estimada' => (int)$_POST['idade_estimada'],
        'altura_metros' => (float)$_POST['altura_metros'],
        'diametro_cm' => (float)$_POST['diametro_cm'],
        'latitude' => $_POST['latitude'] ? $_POST['latitude'] : null,
        'longitude' => $_POST['longitude'] ? $_POST['longitude'] : null,
        'endereco' => trim($_POST['endereco']),
        'bairro_id' => (int)$_POST['bairro_id'],
        'estado_conservacao' => $_POST['estado_conservacao'],
        'observacoes' => trim($_POST['observacoes']),
    );
    if (!empty($_POST['id'])) {
        Arvore::update((int)$_POST['id'], $data);
        flash('success', 'Árvore atualizada com sucesso.');
    } else {
        Arvore::create($data);
        flash('success', 'Árvore cadastrada com sucesso.');
    }
    redirect('arvores.php');
}
$arvores = Arvore::all();
$bairros = Bairro::all();
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<?php if (canEditOperational()): ?>
<div class="grid grid-2">
    <div class="card">
        <h2><?= $edit ? 'Editar árvore' : 'Nova árvore' ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?= e(isset($edit['id']) ? $edit['id'] : '') ?>">
            <div class="form-grid">
                <label>Código<input name="codigo" required value="<?= e(isset($edit['codigo']) ? $edit['codigo'] : 'ARB-' . rand(1000, 9999)) ?>"></label>
                <label>Espécie<input name="especie" required value="<?= e(isset($edit['especie']) ? $edit['especie'] : '') ?>"></label>
                <label>Nome popular<input name="nome_popular" value="<?= e(isset($edit['nome_popular']) ? $edit['nome_popular'] : '') ?>"></label>
                <label>Bairro<select name="bairro_id" required><?php foreach ($bairros as $b): ?><option value="<?= e($b['id']) ?>" <?= ((isset($edit['bairro_id']) ? $edit['bairro_id'] : '') == $b['id']) ? 'selected' : '' ?>><?= e($b['nome']) ?></option><?php endforeach; ?></select></label>
                <label>Idade estimada<input type="number" name="idade_estimada" min="0" value="<?= e(isset($edit['idade_estimada']) ? $edit['idade_estimada'] : 3) ?>"></label>
                <label>Altura (m)<input type="number" step="0.01" name="altura_metros" value="<?= e(isset($edit['altura_metros']) ? $edit['altura_metros'] : 2.5) ?>"></label>
                <label>Diâmetro (cm)<input type="number" step="0.01" name="diametro_cm" value="<?= e(isset($edit['diametro_cm']) ? $edit['diametro_cm'] : 18) ?>"></label>
                <label>Estado<select name="estado_conservacao"><option <?= (isset($edit['estado_conservacao']) && $edit['estado_conservacao']==='Excelente')?'selected':'' ?>>Excelente</option><option <?= (isset($edit['estado_conservacao']) && $edit['estado_conservacao']==='Bom')?'selected':'' ?>>Bom</option><option <?= (isset($edit['estado_conservacao']) && $edit['estado_conservacao']==='Regular')?'selected':'' ?>>Regular</option><option <?= (isset($edit['estado_conservacao']) && $edit['estado_conservacao']==='Crítico')?'selected':'' ?>>Crítico</option></select></label>
                <label>Latitude<input name="latitude" value="<?= e(isset($edit['latitude']) ? $edit['latitude'] : '') ?>"></label>
                <label>Longitude<input name="longitude" value="<?= e(isset($edit['longitude']) ? $edit['longitude'] : '') ?>"></label>
            </div>
            <label>Endereço<input name="endereco" required value="<?= e(isset($edit['endereco']) ? $edit['endereco'] : '') ?>"></label>
            <label>Observações<textarea name="observacoes"><?= e(isset($edit['observacoes']) ? $edit['observacoes'] : '') ?></textarea></label>
            <button class="btn" type="submit"><?= $edit ? 'Atualizar árvore' : 'Salvar árvore' ?></button>
            <?php if ($edit): ?><a class="btn secondary" href="arvores.php">Cancelar edição</a><?php endif; ?>
        </form>
    </div>
</div>
<?php else: ?>
<section class="hero"><h2>Consulta de árvores</h2><p>Seu perfil de Leitor permite visualizar as árvores cadastradas no inventário urbano.</p></section>
<?php endif; ?>
<div class="section-title"><h2>Árvores cadastradas</h2></div>
<div class="card table-wrap">
    <table><thead><tr><th>Código</th><th>Espécie</th><th>Bairro</th><th>Endereço</th><th>Estado</th><?php if (canEditOperational()): ?><th>Ações</th><?php endif; ?></tr></thead><tbody>
    <?php foreach ($arvores as $a): ?><tr>
        <td><?= e($a['codigo']) ?></td><td><?= e($a['especie']) ?></td><td><?= e($a['bairro_nome']) ?></td><td><?= e($a['endereco']) ?></td><td><span class="badge"><?= e($a['estado_conservacao']) ?></span></td>
        <?php if (canEditOperational()): ?><td><a class="btn secondary" href="?edit=<?= e($a['id']) ?>">Editar</a><?php if (isAdmin()): ?> <a class="btn danger" data-confirm="Remover esta árvore?" href="?delete=<?= e($a['id']) ?>">Excluir</a><?php endif; ?></td><?php endif; ?>
    </tr><?php endforeach; ?>
    </tbody></table>
</div>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
