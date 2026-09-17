<?php
require_once __DIR__ . '/config.php';

class Database
{
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $tentativas = array_unique([DB_PASS, '', 'root']);

            $ultimoErro = null;

            foreach ($tentativas as $senha) {
                try {
                    self::$connection = new PDO($dsn, DB_USER, $senha, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    return self::$connection;
                } catch (PDOException $e) {
                    $ultimoErro = $e;
                }
            }

            die('Erro ao conectar no banco de dados. Confira se o MySQL está ligado no XAMPP, se o banco arboriza_botucatu foi importado e se o arquivo config/config.php está correto.<br><b>Detalhe:</b> ' . htmlspecialchars($ultimoErro->getMessage()));
        }

        return self::$connection;
    }
}
