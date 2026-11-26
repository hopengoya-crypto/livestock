<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    set_flash('error', 'Enter a valid email and password.');
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

try {
    $statement = mysqli_prepare(
        $conn,
        'SELECT user_id, password_hash, role, account_status
         FROM `user` WHERE email_address = ? LIMIT 2'
    );
    mysqli_stmt_bind_param($statement, 's', $email);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($statement);

    if (count($users) !== 1 || !password_verify($password, $users[0]['password_hash'])) {
        set_flash('error', 'Invalid email or password.');
        header('Location: login.php');
        exit;
    }

    $user = $users[0];
    if (strtolower((string) $user['account_status']) !== 'active') {
        set_flash('error', 'This account is not active. Contact an administrator.');
        header('Location: login.php');
        exit;
    }

    $role = strtolower((string) $user['role']);
    if ($role === 'admin') {
        $role = 'administrator';
    }

    $destinations = [
        'farmer' => 'farmer/farmer-dashboard.php',
        'veterinarian' => 'veterinarian/veterinarian-dashboard.php',
        'administrator' => 'administrator/administrator-dashboard.php',
    ];
    if (!isset($destinations[$role])) {
        set_flash('error', 'This account has an unsupported role.');
        header('Location: login.php');
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['user_id'];
    $_SESSION['role'] = $role;
    header('Location: ' . $destinations[$role]);
    exit;
} catch (mysqli_sql_exception $exception) {
    error_log('Login database error: ' . $exception->getMessage());
    set_flash('error', 'Sign-in is temporarily unavailable. Please try again.');
    header('Location: login.php');
    exit;
}