<?php
/**
 * Tela de autocadastro público de usuários.
 * Arboriza Botucatu - Perfil padrão obrigatório: Leitor
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/config/database.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$nomeDigitado = '';
$emailDigitado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome             = trim($_POST['nome'] ?? '');
    $email            = strtolower(trim($_POST['email'] ?? ''));
    $senha            = $_POST['senha'] ?? '';
    $senhaConfirmacao = $_POST['senha_confirmacao'] ?? '';

    $nomeDigitado  = $nome;
    $emailDigitado = $email;

    if ($nome === '' || $email === '' || $senha === '' || $senhaConfirmacao === '') {
        flash('error', 'Preencha todos os campos obrigatórios.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Informe um endereço de e-mail válido.');
    } elseif (strlen($senha) < 6) {
        flash('error', 'A senha deve conter no mínimo 6 caracteres.');
    } elseif ($senha !== $senhaConfirmacao) {
        flash('error', 'As senhas digitadas não coincidem.');
    } else {
        try {
            $db = Database::getConnection();

            // Verifica se o e-mail já existe
            $checkStmt = $db->prepare('SELECT id FROM usuarios WHERE LOWER(email) = :email LIMIT 1');
            $checkStmt->execute(['email' => $email]);

            if ($checkStmt->fetch()) {
                flash('error', 'Este e-mail já está em uso por outra conta.');
            } else {
                // Insere com perfil rigidamente definido como 'Leitor' e ativo = 1
                $insertStmt = $db->prepare('
                    INSERT INTO usuarios (nome, email, senha, perfil, ativo)
                    VALUES (:nome, :email, :senha, "Leitor", 1)
                ');
                $insertStmt->execute([
                    'nome'  => $nome,
                    'email' => $email,
                    'senha' => password_hash($senha, PASSWORD_DEFAULT),
                ]);

                flash('success', 'Cadastro realizado com sucesso. Acesse sua conta.');
                redirect('login.php');
            }
        } catch (Exception $e) {
            flash('error', 'Não foi possível concluir o cadastro no momento. Tente novamente.');
        }
    }
}

$pageTitle = 'Criar conta';
?>
<?php include __DIR__ . '/partials/head.php'; ?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Arboriza Botucatu</h1>
            <p>Criar conta no inventário arbóreo</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-error" role="alert">
                <svg class="icon" width="16" height="16"><use href="#icon-alert"></use></svg>
                <span><?= e($msg) ?></span>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Nome completo *
                <input type="text" name="nome" required placeholder="Seu nome completo" value="<?= e($nomeDigitado) ?>" autofocus>
            </label>

            <label>E-mail *
                <input type="email" name="email" required placeholder="seu.email@exemplo.com" value="<?= e($emailDigitado) ?>">
            </label>

            <label>Senha *
                <input type="password" name="senha" required placeholder="Mínimo de 6 caracteres">
            </label>

            <label>Confirmar senha *
                <input type="password" name="senha_confirmacao" required placeholder="Repita a senha">
            </label>

            <p style="font-size: 12px; color: var(--color-text-secondary); line-height: 1.4; margin: 2px 0 8px;">
                Sua conta será criada com o perfil <strong>Leitor</strong>. Um administrador poderá ajustar suas permissões posteriormente.
            </p>

            <button class="btn" type="submit" style="width: 100%; justify-content: center; margin-top: 6px;">Criar cadastro</button>
        </form>

        <div style="margin-top: 18px; text-align: center; font-size: 13px;">
            <span style="color: var(--color-text-secondary);">Já possui uma conta?</span>
            <a href="login.php" style="font-weight: 600; margin-left: 4px;">Entrar</a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
