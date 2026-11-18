<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

    if (Auth::login($email, $senha)) {
        redirect('index.php');
    }
    flash('error', 'E-mail ou senha incorretos.');
}

$pageTitle = 'Entrar';
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Arboriza Botucatu</h1>
            <p>Inventário arbóreo municipal</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error" role="alert">
                <svg class="icon" width="16" height="16"><use href="#icon-alert"></use></svg>
                <span><?= e($msg) ?></span>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>E-mail
                <input type="email" name="email" required placeholder="seu.email@exemplo.com" autofocus>
            </label>
            <label>Senha
                <input type="password" name="senha" required placeholder="Digite sua senha">
            </label>
            <button class="btn" type="submit" style="width: 100%; justify-content: center; margin-top: 6px;">Entrar</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
