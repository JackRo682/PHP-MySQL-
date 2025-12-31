<?php
require_once __DIR__ . '/db.php';

function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function login(string $username, string $password): bool
{
    start_session();
    $pdo = get_pdo();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
        ];
        return true;
    }
    return false;
}

function logout(): void
{
    start_session();
    $_SESSION = [];
    session_destroy();
}

function current_user(): ?array
{
    start_session();
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        header('Location: /login');
        exit;
    }
}

function require_admin(): void
{
    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

function is_admin(): bool
{
    $user = current_user();
    return $user && $user['role'] === 'admin';
}

function set_flash(string $key, string $message): void
{
    start_session();
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    start_session();
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}
