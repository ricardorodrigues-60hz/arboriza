<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/SimplePdf.php';
requireLogin();
$db = Database::getConnection();
$rows = $db->query('SELECT b.nome, b.zona, COUNT(a.id) total FROM bairros b LEFT JOIN arvores a ON a.bairro_id=b.id GROUP BY b.id ORDER BY total DESC')->fetchAll();
$table = array();
foreach ($rows as $r) { $table[] = array($r['nome'], $r['zona'], $r['total']); }
$pdf = new SimplePdf('Relatório de árvores por bairro');
$pdf->table(array('Bairro', 'Zona', 'Total'), $table);
$pdf->output('arvores_por_bairro.pdf');
