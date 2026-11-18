<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/models/Arvore.php';
require_once __DIR__ . '/models/Bairro.php';

requireLogin();

$pageTitle = 'Árvores';

if (isset($_GET['delete'])) {
    if (!isAdmin()) {
        flash('error', 'Apenas o administrador pode excluir registros.');
        redirect('arvores.php');
    }
    Arvore::delete((int)$_GET['delete']);
    flash('success', 'Árvore removida do inventário.');
    redirect('arvores.php');
}

if (isset($_GET['edit']) && isLeitor()) {
    flash('error', 'Perfil com permissão apenas de leitura.');
    redirect('arvores.php');
}

$edit = isset($_GET['edit']) ? Arvore::find((int)$_GET['edit']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!canEditOperational()) {
        flash('error', 'Perfil com permissão apenas de leitura.');
        redirect('arvores.php');
    }

    $codigo = trim($_POST['codigo'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $bairroId = (int)($_POST['bairro_id'] ?? 0);

    if ($codigo === '' || $especie === '' || $endereco === '' || $bairroId <= 0) {
        flash('error', 'Preencha todos os campos obrigatórios (código, espécie, bairro e endereço).');
        redirect('arvores.php' . ($edit ? '?edit=' . (int)$edit['id'] : ''));
    }

    $data = [
        'codigo'             => $codigo,
        'especie'            => $especie,
        'nome_popular'       => trim($_POST['nome_popular'] ?? ''),
        'idade_estimada'     => (int)($_POST['idade_estimada'] ?? 0),
        'altura_metros'      => (float)($_POST['altura_metros'] ?? 0),
        'diametro_cm'        => (float)($_POST['diametro_cm'] ?? 0),
        'latitude'           => !empty($_POST['latitude']) ? trim($_POST['latitude']) : null,
        'longitude'          => !empty($_POST['longitude']) ? trim($_POST['longitude']) : null,
        'endereco'           => $endereco,
        'bairro_id'          => $bairroId,
        'estado_conservacao' => $_POST['estado_conservacao'] ?? 'Bom',
        'observacoes'        => trim($_POST['observacoes'] ?? ''),
    ];

    try {
        if (!empty($_POST['id'])) {
            Arvore::update((int)$_POST['id'], $data);
            flash('success', 'Árvore atualizada com sucesso.');
        } else {
            Arvore::create($data);
            flash('success', 'Árvore cadastrada no inventário.');
        }
    } catch (Exception $e) {
        flash('error', 'Não foi possível salvar o registro. Verifique se o código da árvore já existe.');
    }

    redirect('arvores.php');
}

$arvores = Arvore::all();
$bairros = Bairro::all();
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<div class="app-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <?php if (canEditOperational()): ?>
            <div class="card" style="margin-bottom: 24px;">
                <h2><?= $edit ? 'Editar árvore' : 'Nova árvore' ?></h2>
                <form method="post">
                    <input type="hidden" name="id" value="<?= e(isset($edit['id']) ? $edit['id'] : '') ?>">

                    <div class="form-grid">
                        <label>Código do exemplar *
                            <input name="codigo" required value="<?= e(isset($edit['codigo']) ? $edit['codigo'] : 'ARB-' . rand(1000, 9999)) ?>">
                        </label>
                        <label>Espécie botânica *
                            <input name="especie" required placeholder="Ex.: Handroanthus albus" value="<?= e(isset($edit['especie']) ? $edit['especie'] : '') ?>">
                        </label>
                        <label>Nome popular
                            <input name="nome_popular" placeholder="Ex.: Ipê-amarelo" value="<?= e(isset($edit['nome_popular']) ? $edit['nome_popular'] : '') ?>">
                        </label>
                        <label>Bairro *
                            <select name="bairro_id" required>
                                <option value="">Selecione um bairro</option>
                                <?php foreach ($bairros as $b): ?>
                                    <option value="<?= e($b['id']) ?>" <?= ((isset($edit['bairro_id']) ? $edit['bairro_id'] : '') == $b['id']) ? 'selected' : '' ?>>
                                        <?= e($b['nome']) ?> (<?= e($b['zona']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>Idade estimada (anos)
                            <input type="number" name="idade_estimada" min="0" value="<?= e(isset($edit['idade_estimada']) ? $edit['idade_estimada'] : 5) ?>">
                        </label>
                        <label>Altura (metros)
                            <input type="number" step="0.01" min="0" name="altura_metros" value="<?= e(isset($edit['altura_metros']) ? $edit['altura_metros'] : 3.0) ?>">
                        </label>
                        <label>Diâmetro do tronco (cm)
                            <input type="number" step="0.01" min="0" name="diametro_cm" value="<?= e(isset($edit['diametro_cm']) ? $edit['diametro_cm'] : 20.0) ?>">
                        </label>
                        <label>Estado de conservação
                            <select name="estado_conservacao">
                                <?php foreach (['Excelente', 'Bom', 'Regular', 'Crítico'] as $est): ?>
                                    <option <?= ((isset($edit['estado_conservacao']) ? $edit['estado_conservacao'] : 'Bom') === $est) ? 'selected' : '' ?>><?= e($est) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>Latitude
                            <input name="latitude" placeholder="-22.8859" value="<?= e(isset($edit['latitude']) ? $edit['latitude'] : '') ?>">
                        </label>
                        <label>Longitude
                            <input name="longitude" placeholder="-48.4450" value="<?= e(isset($edit['longitude']) ? $edit['longitude'] : '') ?>">
                        </label>
                    </div>

                    <label>Endereço e ponto de referência *
                        <input name="endereco" required placeholder="Ex.: Praça Emílio Peduti, em frente ao nº 100" value="<?= e(isset($edit['endereco']) ? $edit['endereco'] : '') ?>">
                    </label>

                    <label>Observações técnicas
                        <textarea name="observacoes" placeholder="Condições da copa, fiação aérea, raízes..."><?= e(isset($edit['observacoes']) ? $edit['observacoes'] : '') ?></textarea>
                    </label>

                    <div style="display: flex; gap: 10px;">
                        <button class="btn" type="submit"><?= $edit ? 'Atualizar árvore' : 'Salvar árvore' ?></button>
                        <?php if ($edit): ?>
                            <a class="btn btn-secondary" href="arvores.php">Cancelar edição</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Inventário de árvores</h2>

            <?php if (empty($arvores)): ?>
                <div class="empty-state">
                    <p>Nenhuma árvore cadastrada no inventário.</p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Espécie</th>
                                <th>Bairro</th>
                                <th>Endereço</th>
                                <th>Estado de conservação</th>
                                <?php if (canEditOperational()): ?>
                                    <th>Ações</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arvores as $a): ?>
                                <tr>
                                    <td class="tabular"><strong><?= e($a['codigo']) ?></strong></td>
                                    <td>
                                        <strong><?= e($a['especie']) ?></strong>
                                        <?php if (!empty($a['nome_popular'])): ?>
                                            <br><small class="card-description"><?= e($a['nome_popular']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= e($a['bairro_nome']) ?></td>
                                    <td><?= e($a['endereco']) ?></td>
                                    <td>
                                        <span class="badge <?= badgeClassConservacao($a['estado_conservacao']) ?>">
                                            <?= e($a['estado_conservacao']) ?>
                                        </span>
                                    </td>
                                    <?php if (canEditOperational()): ?>
                                        <td>
                                            <div style="display: flex; gap: 6px;">
                                                <a class="btn btn-secondary btn-sm" href="?edit=<?= (int)$a['id'] ?>">Editar</a>
                                                <?php if (isAdmin()): ?>
                                                    <a class="btn btn-danger btn-sm" data-confirm="Deseja remover esta árvore permanentemente?" href="?delete=<?= (int)$a['id'] ?>">Excluir</a>
                                                <?php endif; ?>
                                            </div>
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
