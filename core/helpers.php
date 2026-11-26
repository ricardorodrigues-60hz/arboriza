<?php
/**
 * Funções auxiliares do sistema Arboriza Botucatu.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Retorna ou gera o token CSRF da sessão atual.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/**
 * Gera o campo hidden HTML com o token CSRF.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Valida o token CSRF enviado via POST e encerra com 403 se inválido.
 */
function csrf_check(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(403);
        die('Requisição inválida (CSRF).');
    }
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    $base = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
    $path = ltrim($path, '/');

    if (preg_match('#^https?://#i', $path)) {
        $url = $path;
    } else {
        $url = $base . '/' . $path;
    }

    header('Location: ' . $url);
    exit;
}

function flash(string $type, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$type] = $message;
        return null;
    }

    if (!isset($_SESSION['_flash'][$type])) {
        return null;
    }

    $value = $_SESSION['_flash'][$type];
    unset($_SESSION['_flash'][$type]);
    return $value;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['usuario']) && !empty($_SESSION['usuario']['id']);
}

function currentUserName(): string
{
    return isset($_SESSION['usuario']['nome']) ? (string)$_SESSION['usuario']['nome'] : '';
}

function currentUserProfile(): string
{
    return isset($_SESSION['usuario']['perfil']) ? (string)$_SESSION['usuario']['perfil'] : '';
}

function normalizedProfile(): string
{
    return function_exists('mb_strtolower')
        ? mb_strtolower(trim(currentUserProfile()), 'UTF-8')
        : strtolower(trim(currentUserProfile()));
}

function isAdmin(): bool
{
    return normalizedProfile() === 'administrador';
}

function isLeitor(): bool
{
    return normalizedProfile() === 'leitor';
}

function canEditOperational(): bool
{
    $profile = normalizedProfile();
    return $profile === 'administrador' || $profile === 'técnico' || $profile === 'tecnico';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();

    if (!isAdmin()) {
        flash('error', 'Acesso permitido somente ao administrador.');
        redirect('index.php');
    }
}

/**
 * Retorna a classe CSS semântica para o estado de conservação da árvore.
 */
function badgeClassEstado(?string $estado): string
{
    $e = function_exists('mb_strtolower')
        ? mb_strtolower(trim((string)$estado), 'UTF-8')
        : strtolower(trim((string)$estado));

    switch ($e) {
        case 'excelente':
            return 'estado-excelente';
        case 'bom':
            return 'estado-bom';
        case 'regular':
            return 'estado-regular';
        case 'crítico':
        case 'critico':
            return 'estado-critico';
        default:
            return 'badge-neutral';
    }
}

function badgeClassPrioridade(?string $prioridade): string
{
    $p = function_exists('mb_strtolower')
        ? mb_strtolower(trim((string)$prioridade), 'UTF-8')
        : strtolower(trim((string)$prioridade));

    switch ($p) {
        case 'urgente':
            return 'prioridade-urgente';
        case 'alta':
            return 'prioridade-alta';
        case 'média':
        case 'media':
            return 'prioridade-media';
        case 'baixa':
            return 'prioridade-baixa';
        default:
            return 'badge-neutral';
    }
}

function badgeClassStatus(?string $status): string
{
    $s = function_exists('mb_strtolower')
        ? mb_strtolower(trim((string)$status), 'UTF-8')
        : strtolower(trim((string)$status));

    switch ($s) {
        case 'concluída':
        case 'concluida':
            return 'badge-success';
        case 'cancelada':
            return 'badge-danger';
        case 'em análise':
        case 'em analise':
        case 'agendada':
            return 'badge-warning';
        default:
            return 'badge-neutral';
    }
}
