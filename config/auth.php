<?php
require_once __DIR__ . '/database.php';

function require_login(?string $role = null): void {
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
    if ($role !== null && ($_SESSION['user']['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('Access denied.');
    }
}

function current_user(): array {
    return $_SESSION['user'] ?? [];
}

function login_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
    }
    session_destroy();
}
