<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/models/Foto.php';
require_once __DIR__ . '/models/Arvore.php';
requireLogin();
$pageTitle = 'Registro de Fotos Periódicas';

if (isset($_GET['delete'])) {
    if (!canEditOperational()) {
        flash('error', 'Leitor não pode excluir fotos.');
        redirect('fotos.php');
    }
    Foto::delete((int)$_GET['delete']);
    flash('success', 'Foto removida do histórico.');
    redirect('fotos.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!canEditOperational()) {
        flash('error', 'Leitor não pode cadastrar fotos.');
        redirect('fotos.php');
    }
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        flash('error', 'Envie uma imagem válida.');
        redirect('fotos.php');
    }
    $permitidos = array('image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif');
    $mime = mime_content_type($_FILES['foto']['tmp_name']);
    if (!isset($permitidos[$mime])) {
        flash('error', 'Formato inválido. Use JPG, PNG ou GIF.');
        redirect('fotos.php');
    }
    if (!is_dir(UPLOAD_DIR)) { mkdir(UPLOAD_DIR, 0777, true); }
    $nome = 'arvore_' . (int)$_POST['arvore_id'] . '_' . time() . $permitidos[$mime];
    $destino = UPLOAD_DIR . $nome;
    move_uploaded_file($_FILES['foto']['tmp_name'], $destino);
    Foto::create(array(
        'arvore_id' => (int)$_POST['arvore_id'],
        'caminho' => $nome,
        'legenda' => trim($_POST['legenda']),
        'data_foto' => $_POST['data_foto']
    ));
    flash('success', 'Foto periódica registrada com sucesso.');
    redirect('fotos.php');
}

$arvores = Arvore::all();
$fotos = Foto::all();
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<?php if (canEditOperational()): ?>
<div class="card">
    <h2>Nova foto periódica</h2>
    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-grid">
            <label>Árvore
                <select name="arvore_id" required>
                    <?php foreach($arvores as $a): ?>
                        <option value="<?= e($a['id']) ?>"><?= e($a['codigo'].' - '.$a['especie'].' / '.$a['bairro_nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Data da foto<input type="date" name="data_foto" value="<?= date('Y-m-d') ?>" required></label>
        </div>
        <label>Imagem<input type="file" name="foto" accept="image/*" required></label>
        <label>Legenda<textarea name="legenda" placeholder="Ex.: Foto após poda, crescimento da copa, condição das folhas"></textarea></label>
        <div><button class="btn" type="submit">Registrar foto</button></div>
    </form>
</div>
<?php endif; ?>
<div class="section-title"><h2>Histórico de imagens</h2></div>
<?php if (empty($fotos)): ?>
    <div class="card"><p class="empty-state">Nenhuma foto registrada.</p></div>
<?php else: ?>
<div class="grid grid-3">
<?php foreach($fotos as $f): ?>
    <div class="card">
        <?php
$caminhoFoto = str_replace('\\', '/', $f['caminho']);

if (strpos($caminhoFoto, 'uploads/') === 0) {
    $urlFoto = APP_URL . '/' . $caminhoFoto;
} else {
    $urlFoto = PUBLIC_UPLOAD_PATH . basename($caminhoFoto);
}
?>
        <img class="photo-thumb" src="<?= e($urlFoto) ?>" alt="Foto da árvore <?= e($f['codigo']) ?>">
        <strong><?= e($f['codigo'].' - '.$f['especie']) ?></strong>
        <p><?= e($f['legenda']) ?></p>
        <small>Data: <?= date('d/m/Y', strtotime($f['data_foto'])) ?></small>
        <?php if (canEditOperational()): ?><div style="margin-top:10px;"><a class="btn danger btn-sm" data-confirm="Remover esta foto?" href="?delete=<?= e($f['id']) ?>">Excluir</a></div><?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
