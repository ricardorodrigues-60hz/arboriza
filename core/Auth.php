<?php
require_once __DIR__ . '/../config/database.php';

class Auth
{
    public static function login(string $email, string $senha): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = Database::getConnection();
        $email = strtolower(trim($email));

        $stmt = $db->prepare('
            SELECT *
            FROM usuarios
            WHERE LOWER(TRIM(email)) = :email
              AND (ativo = 1 OR ativo IS NULL)
            LIMIT 1
        ');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return false;
        }

        $hashSalvo = (string)($usuario['senha'] ?? '');
        $senhaCorreta = false;

        // Formato correto/novo: password_hash() + password_verify().
        if (password_verify($senha, $hashSalvo)) {
            $senhaCorreta = true;
        }

        // Compatibilidade temporária com senhas antigas salvas em MD5 no .sql.
        // Quando o login der certo, a senha é migrada automaticamente para password_hash().
        if (!$senhaCorreta && strlen($hashSalvo) === 32 && hash_equals(strtolower($hashSalvo), md5($senha))) {
            $senhaCorreta = true;

            $novoHash = password_hash($senha, PASSWORD_DEFAULT);
            $update = $db->prepare('UPDATE usuarios SET senha = :senha, ativo = 1 WHERE id = :id');
            $update->execute([
                'senha' => $novoHash,
                'id' => $usuario['id']
            ]);
        }

        if (!$senhaCorreta) {
            return false;
        }

        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'perfil' => $usuario['perfil'] ?? null,
        ];

        return true;
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['usuario']);
        session_destroy();
    }
}
