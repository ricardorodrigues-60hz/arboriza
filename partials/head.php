<?php
/**
 * Cabeçalho HTML e inclusão de recursos locais.
 * Arboriza Botucatu - ADR-003, ADR-004 e ADR-005
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/helpers.php';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' . e(APP_NAME) : e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body>
<?php
// Inclui o sprite de ícones SVG inline para uso via <use href="#icon-id"/>
if (file_exists(__DIR__ . '/../assets/icons/sprite.svg')) {
    include __DIR__ . '/../assets/icons/sprite.svg';
}
?>
