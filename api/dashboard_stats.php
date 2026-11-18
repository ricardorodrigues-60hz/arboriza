<?php
/**
 * Endpoint de dados para os gráficos do dashboard.
 * Arboriza Botucatu - ADR-006
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../config/database.php';

requireLogin();

header('Content-Type: application/json; charset=utf-8');

$db = Database::getConnection();

function dataset(PDO $db, string $sql, array $params = []): array {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'labels' => array_column($rows, 'label'),
        'values' => array_map('intval', array_column($rows, 'total')),
    ];
}

// 1. Árvores por bairro (Top bairros)
$bairros = dataset($db, '
    SELECT b.nome AS label, COUNT(a.id) AS total
    FROM bairros b
    LEFT JOIN arvores a ON a.bairro_id = b.id
    GROUP BY b.id, b.nome
    ORDER BY total DESC, b.nome ASC
    LIMIT 8
');

// 2. Estado de conservação (ordenado do melhor ao crítico)
$conservacao = dataset($db, '
    SELECT estado_conservacao AS label, COUNT(*) AS total
    FROM arvores
    GROUP BY estado_conservacao
    ORDER BY FIELD(estado_conservacao, "Excelente", "Bom", "Regular", "Crítico")
');

// 3. Solicitações por status
$solicitacoes = dataset($db, '
    SELECT status AS label, COUNT(*) AS total
    FROM solicitacoes
    GROUP BY status
    ORDER BY FIELD(status, "Aberta", "Em análise", "Agendada", "Concluída", "Cancelada")
');

echo json_encode([
    'bairros'      => $bairros,
    'conservacao'  => $conservacao,
    'solicitacoes' => $solicitacoes,
], JSON_UNESCAPED_UNICODE);
