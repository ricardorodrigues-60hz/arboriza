<?php
require_once __DIR__ . '/../config/database.php';

class Foto
{
    public static function all()
    {
        $sql = 'SELECT f.*, a.codigo, a.especie
                FROM fotos_arvores f
                INNER JOIN arvores a ON a.id = f.arvore_id
                ORDER BY f.data_foto DESC, f.criado_em DESC';
        return Database::getConnection()->query($sql)->fetchAll();
    }

    public static function create($data)
    {
        $sql = 'INSERT INTO fotos_arvores (arvore_id, caminho, legenda, data_foto)
                VALUES (:arvore_id, :caminho, :legenda, :data_foto)';
        Database::getConnection()->prepare($sql)->execute($data);
    }

    public static function delete($id)
    {
        Database::getConnection()->prepare('DELETE FROM fotos_arvores WHERE id = :id')->execute(array('id' => $id));
    }
}
