<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/config/database.php';

requireAdmin();

$pageTitle = 'Usuários';
$db = Database::getConnection();

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT id, nome, email, perfil, ativo FROM usuarios WHERE id = :id');
    $stmt->execute(['id' => (int)$_GET['edit']]);
    $edit = $stmt->fetch() ?: null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'criar';

    if ($acao === 'excluir') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0 && $id !== (int)$_SESSION['usuario']['id']) {
            $stmt = $db->prepare('DELETE FROM usuarios WHERE id = :id');
            $stmt->execute(['id' => $id]);
            flash('success', 'Usuário removido com sucesso.');
        } else {
            flash('error', 'Não é possível remover o próprio usuário logado.');
        }
        redirect('usuarios.php');
    }

    $id     = (int)($_POST['id'] ?? 0);
    $nome   = trim($_POST['nome'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $senha  = $_POST['senha'] ?? '';
    $perfil = $_POST['perfil'] ?? 'Leitor';
    $ativo  = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '' || $email === '') {
        flash('error', 'Preencha o nome completo e o e-mail.');
        redirect('usuarios.php' . ($edit ? '?edit=' . (int)$edit['id'] : ''));
    }

    try {
        if ($acao === 'editar' && $id > 0) {
            if ($senha !== '') {
                $stmt = $db->prepare('UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, perfil = :perfil, ativo = :ativo WHERE id = :id');
                $stmt->execute([
                    'id'     => $id,
                    'nome'   => $nome,
                    'email'  => $email,
                    'senha'  => password_hash($senha, PASSWORD_DEFAULT),
                    'perfil' => $perfil,
                    'ativo'  => $ativo,
                ]);
            } else {
                $stmt = $db->prepare('UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, ativo = :ativo WHERE id = :id');
                $stmt->execute([
                    'id'     => $id,
                    'nome'   => $nome,
                    'email'  => $email,
                    'perfil' => $perfil,
                    'ativo'  => $ativo,
                ]);
            }

            if ($id === (int)$_SESSION['usuario']['id']) {
                $_SESSION['usuario']['nome']   = $nome;
                $_SESSION['usuario']['email']  = $email;
                $_SESSION['usuario']['perfil'] = $perfil;
            }
            flash('success', 'Cadastro de usuário atualizado.');
        } else {
            if ($senha === '') {
                flash('error', 'Informe uma senha inicial para criar o usuário.');
                redirect('usuarios.php');
            }
            $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES (:nome, :email, :senha, :perfil, :ativo)');
            $stmt->execute([
                'nome'   => $nome,
                'email'  => $email,
                'senha'  => password_hash($senha, PASSWORD_DEFAULT),
                'perfil' => $perfil,
                'ativo'  => $ativo,
            ]);
            flash('success', 'Novo usuário cadastrado com sucesso.');
        }
    } catch (Exception $e) {
        flash('error', 'Não foi possível salvar o usuário. Verifique se o e-mail já está em uso.');
    }
    redirect('usuarios.php');
}

$usuarios = $db->query('SELECT id, nome, email, perfil, ativo, criado_em FROM usuarios ORDER BY nome ASC')->fetchAll();
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<div class="app-shell">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/partials/topbar.php'; ?>

        <div class="grid grid-2">
            <!-- Formulário -->
            <div class="card">
                <h2><?= $edit ? 'Editar usuário' : 'Novo usuário' ?></h2>
                <form method="post">
                    <input type="hidden" name="acao" value="<?= $edit ? 'editar' : 'criar' ?>">
                    <input type="hidden" name="id" value="<?= e(isset($edit['id']) ? $edit['id'] : '') ?>">

                    <label>Nome completo *
                        <input type="text" name="nome" required placeholder="Ex.: Ana Santos" value="<?= e(isset($edit['nome']) ? $edit['nome'] : '') ?>">
                    </label>

                    <label>E-mail institucional *
                        <input type="email" name="email" required placeholder="ana.santos@botucatu.sp.gov.br" value="<?= e(isset($edit['email']) ? $edit['email'] : '') ?>">
                    </label>

                    <label><?= $edit ? 'Nova senha (deixe em branco para manter a atual)' : 'Senha de acesso *' ?>
                        <input type="password" name="senha" <?= $edit ? '' : 'required' ?> placeholder="<?= $edit ? 'Digite apenas se for alterar' : 'Senha de acesso' ?>">
                    </label>

                    <div class="form-grid">
                        <label>Perfil de permissão *
                            <select name="perfil">
                                <?php foreach (['Leitor', 'Técnico', 'Administrador'] as $perfil): ?>
                                    <option <?= (((isset($edit['perfil']) ? $edit['perfil'] : 'Leitor')) === $perfil) ? 'selected' : '' ?>>
                                        <?= e($perfil) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label>Situação da conta
                            <span style="display: flex; align-items: center; gap: 8px; margin-top: 8px;">
                                <input type="checkbox" name="ativo" <?= (!isset($edit['ativo']) || $edit['ativo']) ? 'checked' : '' ?> style="width: auto;">
                                Conta ativa
                            </span>
                        </label>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 6px;">
                        <button class="btn" type="submit"><?= $edit ? 'Atualizar usuário' : 'Salvar usuário' ?></button>
                        <?php if ($edit): ?>
                            <a class="btn btn-secondary" href="usuarios.php">Cancelar edição</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Listagem -->
            <div class="card">
                <h2>Usuários do sistema</h2>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Perfil</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><strong><?= e($u['nome']) ?></strong></td>
                                    <td><?= e($u['email']) ?></td>
                                    <td><span class="badge badge-neutral"><?= e($u['perfil']) ?></span></td>
                                    <td>
                                        <?php if ($u['ativo']): ?>
                                            <span class="badge badge-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 6px;">
                                            <a class="btn btn-secondary btn-sm" href="?edit=<?= (int)$u['id'] ?>">Editar</a>
                                            <?php if ((int)$u['id'] !== (int)$_SESSION['usuario']['id']): ?>
                                                <form method="post" style="display: inline;" onsubmit="return confirm('Deseja remover este usuário?');">
                                                    <input type="hidden" name="acao" value="excluir">
                                                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                                    <button class="btn btn-danger btn-sm" type="submit">Excluir</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

<?php include __DIR__ . '/partials/footer.php'; ?>
