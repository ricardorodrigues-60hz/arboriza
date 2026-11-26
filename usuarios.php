<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/config/database.php';
requireAdmin();

$pageTitle = 'Cadastros de Usuários';
$db = Database::getConnection();

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare('SELECT id, nome, email, perfil, ativo FROM usuarios WHERE id = :id');
    $stmt->execute(array('id' => (int)$_GET['edit']));
    $edit = $stmt->fetch() ?: null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $acao = isset($_POST['acao']) ? $_POST['acao'] : 'criar';

    if ($acao === 'excluir') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0 && $id != $_SESSION['usuario']['id']) {
            $stmt = $db->prepare('DELETE FROM usuarios WHERE id = :id');
            $stmt->execute(array('id' => $id));
            flash('success', 'Cadastro removido com sucesso.');
        } else {
            flash('error', 'Não é possível remover o usuário logado.');
        }
        redirect('usuarios.php');
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';
    $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : 'Leitor';
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    if ($nome === '' || $email === '') {
        flash('error', 'Preencha nome e e-mail.');
        redirect('usuarios.php');
    }

    try {
        if ($acao === 'editar' && $id > 0) {
            if ($senha !== '') {
                $stmt = $db->prepare('UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, perfil = :perfil, ativo = :ativo WHERE id = :id');
                $stmt->execute(array('id'=>$id,'nome'=>$nome,'email'=>$email,'senha'=>password_hash($senha, PASSWORD_DEFAULT),'perfil'=>$perfil,'ativo'=>$ativo));
            } else {
                $stmt = $db->prepare('UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil, ativo = :ativo WHERE id = :id');
                $stmt->execute(array('id'=>$id,'nome'=>$nome,'email'=>$email,'perfil'=>$perfil,'ativo'=>$ativo));
            }
            if ($id == $_SESSION['usuario']['id']) {
                $_SESSION['usuario']['nome'] = $nome;
                $_SESSION['usuario']['email'] = $email;
                $_SESSION['usuario']['perfil'] = $perfil;
            }
            flash('success', 'Cadastro atualizado com sucesso.');
        } else {
            if ($senha === '') {
                flash('error', 'Informe uma senha para criar o cadastro.');
                redirect('usuarios.php');
            }
            $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES (:nome, :email, :senha, :perfil, :ativo)');
            $stmt->execute(array('nome'=>$nome,'email'=>$email,'senha'=>password_hash($senha, PASSWORD_DEFAULT),'perfil'=>$perfil,'ativo'=>$ativo));
            flash('success', 'Novo cadastro criado com sucesso.');
        }
    } catch (Exception $e) {
        flash('error', 'Não foi possível salvar. Verifique se o e-mail já existe.');
    }
    redirect('usuarios.php');
}

$usuarios = $db->query('SELECT id, nome, email, perfil, ativo, criado_em FROM usuarios ORDER BY nome')->fetchAll();
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>

<div class="grid grid-2">
    <div class="card">
        <h2><?= $edit ? 'Editar usuário' : 'Novo usuário' ?></h2>
        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="acao" value="<?= $edit ? 'editar' : 'criar' ?>">
            <input type="hidden" name="id" value="<?= e(isset($edit['id']) ? $edit['id'] : '') ?>">
            <label>Nome completo
                <input type="text" name="nome" required placeholder="Ex.: Técnico Ambiental" value="<?= e(isset($edit['nome']) ? $edit['nome'] : '') ?>">
            </label>
            <label>E-mail
                <input type="email" name="email" required placeholder="usuario@email.com" value="<?= e(isset($edit['email']) ? $edit['email'] : '') ?>">
            </label>
            <label><?= $edit ? 'Nova senha opcional' : 'Senha' ?>
                <input type="password" name="senha" <?= $edit ? '' : 'required' ?> placeholder="<?= $edit ? 'Deixe vazio para manter a atual' : 'Digite uma senha' ?>">
            </label>
            <div class="form-grid">
                <label>Perfil
                    <select name="perfil">
                        <?php foreach(array('Administrador','Técnico','Leitor') as $perfil): ?>
                            <option <?= (isset($edit['perfil']) && $edit['perfil']===$perfil)?'selected':'' ?>><?= e($perfil) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Status
                    <span style="display:flex;align-items:center;gap:10px;font-weight:400;">
                        <input type="checkbox" name="ativo" <?= (!isset($edit['ativo']) || $edit['ativo']) ? 'checked' : '' ?> style="width:auto;"> Ativo
                    </span>
                </label>
            </div>
            <button class="btn" type="submit"><?= $edit ? 'Atualizar cadastro' : 'Salvar cadastro' ?></button>
            <?php if ($edit): ?><a class="btn secondary" href="usuarios.php">Cancelar edição</a><?php endif; ?>
        </form>
    </div>

        <div class="card">
        <h2>Usuários cadastrados</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= e($u['nome']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><span class="badge"><?= e($u['perfil']) ?></span></td>
                        <td><?= $u['ativo'] ? '<span class="badge success">Ativo</span>' : '<span class="badge danger">Inativo</span>' ?></td>
                        <td>
                            <a class="btn secondary btn-sm" href="?edit=<?= (int)$u['id'] ?>">Editar</a>
                            <form method="post" onsubmit="return confirm('Remover este usuário?');" style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                                <button class="btn danger btn-sm" type="submit" <?= ((int)$u['id'] === (int)$_SESSION['usuario']['id']) ? 'disabled title="Não é possível excluir o usuário logado"' : '' ?>>Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/views/layout/footer.php'; ?>
