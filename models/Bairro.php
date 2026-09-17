<?php
require_once __DIR__ . '/../config/database.php';

class Bairro
{
    public static function all() {
        return Database::getConnection()->query('SELECT * FROM bairros ORDER BY nome')->fetchAll();
    }

    public static function find($id) {
        $stmt = Database::getConnection()->prepare('SELECT * FROM bairros WHERE id = :id');
        $stmt->execute(array('id' => $id));
        return $stmt->fetch() ?: null;
    }

    public static function create($nome, $zona) {
        $stmt = Database::getConnection()->prepare('INSERT INTO bairros (nome, zona) VALUES (:nome, :zona)');
        $stmt->execute(array('nome' => $nome, 'zona' => $zona));
    }

    public static function update($id, $nome, $zona) {
        $stmt = Database::getConnection()->prepare('UPDATE bairros SET nome = :nome, zona = :zona WHERE id = :id');
        $stmt->execute(array('id' => $id, 'nome' => $nome, 'zona' => $zona));
    }

    public static function delete($id) {
        Database::getConnection()->prepare('DELETE FROM bairros WHERE id = :id')->execute(array('id' => $id));
    }
}
