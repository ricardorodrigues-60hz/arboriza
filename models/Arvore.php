<?php
require_once __DIR__ . '/../config/database.php';

class Arvore
{
    public static function all() {
        $sql = 'SELECT a.*, b.nome AS bairro_nome
                FROM arvores a
                INNER JOIN bairros b ON b.id = a.bairro_id
                ORDER BY a.criado_em DESC';
        return Database::getConnection()->query($sql)->fetchAll();
    }

    public static function find(int $id) {
        $stmt = Database::getConnection()->prepare('SELECT * FROM arvores WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data) {
        $sql = 'INSERT INTO arvores (codigo, especie, nome_popular, idade_estimada, altura_metros, diametro_cm, latitude, longitude, endereco, bairro_id, estado_conservacao, observacoes)
                VALUES (:codigo, :especie, :nome_popular, :idade_estimada, :altura_metros, :diametro_cm, :latitude, :longitude, :endereco, :bairro_id, :estado_conservacao, :observacoes)';
        Database::getConnection()->prepare($sql)->execute($data);
    }

    public static function update(int $id, array $data) {
        $data['id'] = $id;
        $sql = 'UPDATE arvores SET codigo = :codigo, especie = :especie, nome_popular = :nome_popular, idade_estimada = :idade_estimada,
                altura_metros = :altura_metros, diametro_cm = :diametro_cm, latitude = :latitude, longitude = :longitude, endereco = :endereco,
                bairro_id = :bairro_id, estado_conservacao = :estado_conservacao, observacoes = :observacoes WHERE id = :id';
        Database::getConnection()->prepare($sql)->execute($data);
    }

    public static function delete(int $id) {
        Database::getConnection()->prepare('DELETE FROM arvores WHERE id = :id')->execute(['id' => $id]);
    }
}
