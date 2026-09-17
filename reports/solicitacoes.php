<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/SimplePdf.php';
requireLogin();
$sql='SELECT s.tipo, s.prioridade, s.status, s.solicitante, a.codigo FROM solicitacoes s JOIN arvores a ON a.id=s.arvore_id ORDER BY s.criado_em DESC';
$rows = Database::getConnection()->query($sql)->fetchAll();
$table = array();
foreach ($rows as $r) { $table[] = array($r['codigo'], $r['tipo'], $r['prioridade'], $r['status'], $r['solicitante']); }
$pdf = new SimplePdf('Relatório de solicitações de manutenção');
$pdf->table(array('Árvore', 'Tipo', 'Prioridade', 'Status', 'Solicitante'), $table);
$pdf->output('solicitacoes_manutencao.pdf');
