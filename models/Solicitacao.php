<?php
require_once __DIR__ . '/../config/database.php';

class Solicitacao
{
    public static function all() {
        $sql = "SELECT s.*, a.codigo, a.especie, b.nome AS bairro_nome, u.nome AS usuario_nome, u.email AS usuario_email
                FROM solicitacoes s
                INNER JOIN arvores a ON a.id = s.arvore_id
                INNER JOIN bairros b ON b.id = a.bairro_id
                INNER JOIN usuarios u ON u.id = s.usuario_id
                ORDER BY
                    CASE s.status
                        WHEN 'Aberta' THEN 1
                        WHEN 'Agendada' THEN 2
                        WHEN 'Cancelada' THEN 3
                        WHEN 'Em análise' THEN 4
                        WHEN 'Concluída' THEN 5
                        ELSE 6
                    END ASC,
                    CASE s.prioridade
                        WHEN 'Urgente' THEN 1
                        WHEN 'Alta' THEN 2
                        WHEN 'Média' THEN 3
                        WHEN 'Baixa' THEN 4
                        ELSE 5
                    END ASC,
                    s.criado_em DESC";
        return Database::getConnection()->query($sql)->fetchAll();
    }

    public static function create($data) {
        $sql = 'INSERT INTO solicitacoes (arvore_id, usuario_id, solicitante, telefone, tipo, prioridade, status, descricao)
                VALUES (:arvore_id, :usuario_id, :solicitante, :telefone, :tipo, :prioridade, :status, :descricao)';
        Database::getConnection()->prepare($sql)->execute($data);
    }

    public static function updateStatus($id, $status) {
        Database::getConnection()->prepare('UPDATE solicitacoes SET status = :status WHERE id = :id')->execute(array(
            'id' => $id,
            'status' => $status,
        ));
    }
}
