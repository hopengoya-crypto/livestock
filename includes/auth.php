<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function consume_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function current_user_id(): int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
}

function require_role(string $role): void
{
    if (current_user_id() === 0 || !isset($_SESSION['role'])) {
        set_flash('error', 'Please sign in to continue.');
        header('Location: ../login.php');
        exit;
    }

    if (strtolower((string) $_SESSION['role']) !== strtolower($role)) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }
}
