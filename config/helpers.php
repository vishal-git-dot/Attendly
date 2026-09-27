<?php
// For phone scanning, set APP_URL to your PC LAN URL when needed.
if (!defined('APP_URL')) define('APP_URL', '');
function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function base_url(): string {
    if (APP_URL !== '') return rtrim(APP_URL, '/');
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $https ? 'https' : 'http';
    return $scheme . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
}
