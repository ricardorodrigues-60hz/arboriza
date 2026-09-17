<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();
header('Content-Type: application/json; charset=utf-8');
$db = Database::getConnection();
function dataset($db, string $sql) {
    $rows = $db->query($sql)->fetchAll();
    return [
        'labels' => array_column($rows, 'label'),
        'values' => array_map('intval', array_column($rows, 'total')),
    ];
}
echo json_encode([
    'bairros' => dataset($db, 'SELECT b.nome label, COUNT(a.id) total FROM bairros b LEFT JOIN arvores a ON a.bairro_id = b.id GROUP BY b.id ORDER BY total DESC LIMIT 8'),
    'especies' => dataset($db, 'SELECT especie label, COUNT(*) total FROM arvores GROUP BY especie ORDER BY total DESC LIMIT 6'),
    'conservacao' => dataset($db, 'SELECT estado_conservacao label, COUNT(*) total FROM arvores GROUP BY estado_conservacao ORDER BY total DESC'),
    'solicitacoes' => dataset($db, 'SELECT status label, COUNT(*) total FROM solicitacoes GROUP BY status ORDER BY total DESC'),
], JSON_UNESCAPED_UNICODE);
