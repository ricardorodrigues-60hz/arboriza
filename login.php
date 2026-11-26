<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (Auth::login(isset($_POST['email']) ? $_POST['email'] : '', isset($_POST['senha']) ? $_POST['senha'] : '')) {
        redirect('index.php');
    }
    flash('error', 'E-mail ou senha inválidos.');
}
?>
<?php include __DIR__ . '/views/layout/header.php'; ?>
<div class="card auth-card">
    <div class="auth-header">
        <div class="brand-icon">
            <svg class="icon" aria-hidden="true"><use href="<?= APP_URL ?>/assets/icons/sprite.svg#tree"></use></svg>
        </div>
        <h1>Arboriza Botucatu</h1>
    </div>
    <form method="post">
        <?= csrf_field() ?>
        <label>E-mail
            <input type="email" name="email" required placeholder="Digite seu e-mail">
        </label>
        <label>Senha
            <input type="password" name="senha" required placeholder="Digite sua senha">
        </label>
        <button class="btn auth-action" type="submit">Entrar</button>
        <a class="btn secondary auth-action" href="<?= APP_URL ?>/cadastro.php">Criar conta</a>
    </form>
</div>
<?php include __DIR__ . '/views/layout/footer.php'; ?>
