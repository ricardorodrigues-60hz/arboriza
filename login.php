<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Auth::login(isset($_POST['email']) ? $_POST['email'] : '', isset($_POST['senha']) ? $_POST['senha'] : '')) {
        redirect('index.php');
    }
    flash('error', 'E-mail ou senha inválidos.');
}
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<div class="card auth-card">
    <div class="auth-logo">🌳</div>
    <h1>Arboriza Botucatu</h1>
    <p>Entre para gerenciar árvores, solicitações, intervenções, gráficos e relatórios em PDF.</p>
    <form method="post">
        <label>E-mail
            <input type="email" name="email" required placeholder="Digite seu e-mail">
        </label>
        <label>Senha
            <input type="password" name="senha" required placeholder="Digite sua senha">
        </label>
        <button class="btn secondary auth-action" type="submit">Entrar no sistema</button>
        <a class="btn secondary auth-action" href="<?= APP_URL ?>/cadastro.php">Criar novo cadastro</a>
    </form>
</div>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
