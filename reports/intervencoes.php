<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/SimplePdf.php';
requireLogin();
$sql='SELECT i.data_intervencao, i.tipo, i.responsavel, i.custo_estimado, a.codigo FROM intervencoes i JOIN arvores a ON a.id=i.arvore_id ORDER BY i.data_intervencao DESC';
$rows = Database::getConnection()->query($sql)->fetchAll();
$table = array();
foreach ($rows as $r) { $table[] = array(date('d/m/Y', strtotime($r['data_intervencao'])), $r['codigo'], $r['tipo'], $r['responsavel'], 'R$ '.number_format((float)$r['custo_estimado'],2,',','.')); }
$pdf = new SimplePdf('Relatório de histórico de intervenções');
$pdf->table(array('Data', 'Árvore', 'Tipo', 'Responsável', 'Custo'), $table);
$pdf->output('historico_intervencoes.pdf');
