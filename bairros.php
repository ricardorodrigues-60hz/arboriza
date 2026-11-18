<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/models/Bairro.php';

requireLogin();

$pageTitle = 'Bairros';

if (isset($_GET['delete'])) {
    if (!isAdmin()) {
        flash('error', 'Apenas o administrador pode excluir bairros.');
        redirect('bairros.php');
    }

    try {
        Bairro::delete((int)$_GET['delete']);
        flash('success', 'Bairro removido com sucesso.');
    } catch (Exception $e) {
        flash('error', 'Não é possível remover este bairro porque existem árvores vinculadas a ele.');
    }

    redirect('bairros.php');
}

if (isset($_GET['edit']) && !isAdmin()) {
    flash('error', 'Apenas o administrador pode editar bairros.');
    redirect('bairros.php');
}

$edit = isset($_GET['edit']) ? Bairro::find((int)$_GET['edit']) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isAdmin()) {
        flash('error', 'Apenas o administrador pode cadastrar ou alterar bairros.');
        redirect('bairros.php');
    }

    $nome = trim($_POST['nome'] ?? '');
    $zona = trim($_POST['zona'] ?? '');

    if ($nome === '' || $zona === '') {
        flash('error', 'Preencha o nome do bairro e a zona.');
        redirect('bairros.php' . ($edit ? '?edit=' . (int)$edit['id'] : ''));
    }

    try {
        if (!empty($_POST['id'])) {
            Bairro::update((int)$_POST['id'], $nome, $zona);
            flash('success', 'Bairro atualizado com sucesso.');
        } else {
            Bairro::create($nome, $zona);
            flash('success', 'Bairro cadastrado com sucesso.');
        }
    } catch (Exception $e) {
        flash('error', 'Não foi possível salvar. Verifique se o nome do bairro já está cadastrado.');
    }

    redirect('bairros.php');
}

$bairros = Bairro::all();
$zonasDisponiveis = ['Central', 'Norte', 'Sul', 'Leste', 'Oeste'];
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<div class="app-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <?php if (isAdmin()): ?>
            <div class="card" style="margin-bottom: 24px;">
                <h2><?= $edit ? 'Editar bairro' : 'Novo bairro' ?></h2>
                <form method="post">
                    <input type="hidden" name="id" value="<?= e(isset($edit['id']) ? $edit['id'] : '') ?>">

                    <div class="form-grid">
                        <label>Nome do bairro *
                            <input name="nome" required placeholder="Ex.: Jardim Paraíso" value="<?= e(isset($edit['nome']) ? $edit['nome'] : '') ?>">
                        </label>

                        <label>Região / Zona *
                            <select name="zona" required>
                                <option value="">Selecione a região</option>
                                <?php foreach ($zonasDisponiveis as $z): ?>
                                    <option value="<?= e($z) ?>" <?= ((isset($edit['zona']) ? $edit['zona'] : '') === $z) ? 'selected' : '' ?>>
                                        <?= e($z) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 6px;">
                        <button class="btn" type="submit"><?= $edit ? 'Atualizar bairro' : 'Salvar bairro' ?></button>
                        <?php if ($edit): ?>
                            <a class="btn btn-secondary" href="bairros.php">Cancelar edição</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Bairros cadastrados</h2>

            <?php if (empty($bairros)): ?>
                <div class="empty-state">
                    <p>Nenhum bairro cadastrado.</p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome do bairro</th>
                                <th>Região / Zona</th>
                                <?php if (isAdmin()): ?>
                                    <th>Ações</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bairros as $b): ?>
                                <tr>
                                    <td><strong><?= e($b['nome']) ?></strong></td>
                                    <td><span class="badge badge-neutral"><?= e($b['zona']) ?></span></td>
                                    <?php if (isAdmin()): ?>
                                        <td>
                                            <div style="display: flex; gap: 6px;">
                                                <a class="btn btn-secondary btn-sm" href="?edit=<?= (int)$b['id'] ?>">Editar</a>
                                                <a class="btn btn-danger btn-sm" data-confirm="Deseja remover este bairro?" href="?delete=<?= (int)$b['id'] ?>">Excluir</a>
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
