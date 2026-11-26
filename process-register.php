<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$nationalId = trim($_POST['national_id'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$role = strtolower(trim($_POST['role'] ?? ''));
$location = trim($_POST['location'] ?? '');
$registrationNumber = trim($_POST['registration_number'] ?? '');

$error = null;
if ($firstName === '' || $lastName === '' || $phone === '' || $nationalId === '' || $email === '') {
    $error = 'Complete all required fields.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
    $error = 'Enter a valid email address (maximum 50 characters).';
} elseif (strlen($firstName . ' ' . $lastName) > 100) {
    $error = 'Your full name must be 100 characters or fewer.';
} elseif (strlen($phone) > 20 || !preg_match('/^(07|01)\d{8}$/', $phone)) {
    $error = 'Enter a valid 10-digit Kenyan phone number starting with 07 or 01.';
} elseif (strlen($nationalId) > 30) {
    $error = 'Your national ID must be 30 characters or fewer.';
} elseif (!in_array($role, ['farmer', 'veterinarian'], true)) {
    $error = 'Choose a valid account role.';
} elseif ($password === '' || $password !== $confirmPassword) {
    $error = 'The passwords are required and must match.';
} elseif (strlen($password) < 8) {
    $error = 'Use a password with at least 8 characters.';
} elseif ($role === 'farmer' && ($location === '' || strlen($location) > 25)) {
    $error = 'Enter a farmer location of 25 characters or fewer.';
} elseif ($role === 'veterinarian' && ($registrationNumber === '' || strlen($registrationNumber) > 30)) {
    $error = 'Enter a veterinarian registration number of 30 characters or fewer.';
} elseif (!isset($_POST['rules'])) {
    $error = 'Accept the system rules to register.';
}

if ($error !== null) {
    set_flash('error', $error);
    header('Location: register.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$transactionStarted = false;
try {
    mysqli_begin_transaction($conn);
    $transactionStarted = true;

    $duplicate = mysqli_prepare(
        $conn,
        'SELECT user_id FROM `user` WHERE email_address = ? OR phone_no = ? OR national_id = ? LIMIT 1'
    );
    mysqli_stmt_bind_param($duplicate, 'sss', $email, $phone, $nationalId);
    mysqli_stmt_execute($duplicate);
    mysqli_stmt_store_result($duplicate);

    if (mysqli_stmt_num_rows($duplicate) > 0) {
        mysqli_stmt_close($duplicate);
        mysqli_rollback($conn);
        set_flash('error', 'That email, phone number, or national ID is already registered.');
        header('Location: register.php');
        exit;
    }
    mysqli_stmt_close($duplicate);

    $fullName = $firstName . ' ' . $lastName;
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    if ($passwordHash === false) {
        throw new RuntimeException('Password hashing failed.');
    }
    $accountStatus = 'active';

    $userInsert = mysqli_prepare(
        $conn,
        'INSERT INTO `user` (full_name, phone_no, national_id, email_address, password_hash, role, account_status)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    mysqli_stmt_bind_param(
        $userInsert,
        'sssssss',
        $fullName,
        $phone,
        $nationalId,
        $email,
        $passwordHash,
        $role,
        $accountStatus
    );
    mysqli_stmt_execute($userInsert);
    $userId = mysqli_insert_id($conn);
    mysqli_stmt_close($userInsert);

    if ($role === 'farmer') {
        $profileInsert = mysqli_prepare($conn, 'INSERT INTO farmer (user_id, location) VALUES (?, ?)');
        mysqli_stmt_bind_param($profileInsert, 'is', $userId, $location);
        mysqli_stmt_execute($profileInsert);
        mysqli_stmt_close($profileInsert);
    } elseif ($role === 'veterinarian') {
        $profileInsert = mysqli_prepare($conn, 'INSERT INTO veterinarian (user_id, reg_no) VALUES (?, ?)');
        mysqli_stmt_bind_param($profileInsert, 'is', $userId, $registrationNumber);
        mysqli_stmt_execute($profileInsert);
        mysqli_stmt_close($profileInsert);
    }

    mysqli_commit($conn);
    set_flash('success', 'Account created. You can now sign in.');
    header('Location: login.php');
    exit;
} catch (mysqli_sql_exception $exception) {
    if ($transactionStarted) {
        mysqli_rollback($conn);
    }
    error_log('Registration database error: ' . $exception->getMessage());
    set_flash('error', 'The account could not be created. Check that the details are valid and try again.');
    header('Location: register.php');
    exit;
} catch (RuntimeException $exception) {
    if ($transactionStarted) {
        mysqli_rollback($conn);
    }
    error_log('Registration error: ' . $exception->getMessage());
    set_flash('error', 'The account could not be created. Please try again.');
    header('Location: register.php');
    exit;
}