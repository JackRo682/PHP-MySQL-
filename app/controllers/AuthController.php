<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../csrf.php';

function show_login(): void
{
    $error = get_flash('error');
    include __DIR__ . '/../views/login.php';
}

function handle_login(): void
{
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        header('Location: /receipts');
        exit;
    }
    set_flash('error', 'Invalid credentials');
    header('Location: /login');
}

function handle_logout(): void
{
    logout();
    header('Location: /login');
}
