<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/SimplePdf.php';
requireLogin();
$rows = Database::getConnection()->query('SELECT especie, nome_popular, COUNT(*) total, AVG(idade_estimada) media_idade FROM arvores GROUP BY especie, nome_popular ORDER BY total DESC')->fetchAll();
$table = array();
foreach ($rows as $r) { $table[] = array($r['especie'], $r['nome_popular'], $r['total'], number_format((float)$r['media_idade'],1,',','.')); }
$pdf = new SimplePdf('Relatório de espécies predominantes');
$pdf->table(array('Espécie', 'Nome popular', 'Total', 'Idade média'), $table);
$pdf->output('especies_predominantes.pdf');
