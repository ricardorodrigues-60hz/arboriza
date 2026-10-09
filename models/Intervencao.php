<?php
require_once __DIR__ . '/../config/database.php';

class Intervencao
{
    public static function all() {
        $sql = 'SELECT i.*, a.codigo, a.especie, u.nome AS usuario_nome, u.email AS usuario_email
                FROM intervencoes i
                INNER JOIN arvores a ON a.id = i.arvore_id
                INNER JOIN usuarios u ON u.id = i.usuario_id
                ORDER BY i.data_intervencao DESC';
        return Database::getConnection()->query($sql)->fetchAll();
    }

    public static function find($id) {
        $stmt = Database::getConnection()->prepare('SELECT i.*, u.nome AS usuario_nome, u.email AS usuario_email FROM intervencoes i INNER JOIN usuarios u ON u.id = i.usuario_id WHERE i.id = :id');
        $stmt->execute(array('id' => $id));
        return $stmt->fetch() ?: null;
    }

    public static function create($data) {
        $sql = 'INSERT INTO intervencoes (arvore_id, usuario_id, tipo, responsavel, data_intervencao, custo_estimado, descricao)
                VALUES (:arvore_id, :usuario_id, :tipo, :responsavel, :data_intervencao, :custo_estimado, :descricao)';
        Database::getConnection()->prepare($sql)->execute($data);
    }

    public static function update($id, $data) {
        $data['id'] = $id;
        $sql = 'UPDATE intervencoes SET arvore_id = :arvore_id, usuario_id = :usuario_id, tipo = :tipo, responsavel = :responsavel,
                data_intervencao = :data_intervencao, custo_estimado = :custo_estimado, descricao = :descricao
                WHERE id = :id';
        Database::getConnection()->prepare($sql)->execute($data);
    }

    public static function delete($id) {
        Database::getConnection()->prepare('DELETE FROM intervencoes WHERE id = :id')->execute(array('id' => $id));
    }
}
