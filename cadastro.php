<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/config/database.php';

// Visitante pode se cadastrar; quem já está logado não precisa
if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nome = trim(isset($_POST['nome']) ? $_POST['nome'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    if ($nome === '' || $email === '' || $senha === '') {
        flash('error', 'Preencha todos os campos para criar sua conta.');
        redirect('cadastro.php');
    }

    try {
        $db = Database::getConnection();
        $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha, perfil, ativo) VALUES (:nome, :email, :senha, :perfil, 1)');
        $stmt->execute(array(
            'nome' => $nome,
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'perfil' => 'Leitor'
        ));
        flash('success', 'Cadastro criado. Agora você já pode entrar no sistema.');
        redirect('login.php');
    } catch (Exception $e) {
        flash('error', 'Não foi possível criar o cadastro. Talvez este e-mail já esteja em uso.');
    }
}
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<div class="card auth-card">
    <div class="auth-header">
        <div class="brand-icon">
            <svg class="icon" aria-hidden="true"><use href="<?= APP_URL ?>/assets/icons/sprite.svg#users"></use></svg>
        </div>
        <h1>Novo usuário</h1>
    </div>
    <p class="auth-note">
        Sua conta será criada com perfil <strong>Leitor</strong>.
        Após o cadastro, um administrador pode alterar suas permissões.
    </p>
    <form method="post">
        <?= csrf_field() ?>
        <label>Nome completo
            <input type="text" name="nome" required placeholder="Seu nome">
        </label>
        <label>E-mail
            <input type="email" name="email" required placeholder="seuemail@email.com">
        </label>
        <label>Senha
            <input type="password" name="senha" required placeholder="Digite uma senha">
        </label>
        <button class="btn" type="submit">Criar cadastro</button>
        <a class="btn secondary" href="<?= APP_URL ?>/login.php" style="justify-content:center;">Voltar para o login</a>
    </form>
</div>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
