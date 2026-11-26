<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('farmer');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$farmerId = role_profile_id($conn, 'farmer', current_user_id());
if ($farmerId === null) {
    http_response_code(409);
    exit('Your farmer profile is not available.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
        set_flash('error', 'Enter a valid email address (maximum 50 characters).');
    } else {
        try {
            $statement = mysqli_prepare(
                $conn,
                "INSERT INTO veterinarian_invitation (farmer_id, email_address, invitation_status)
                 VALUES (?, ?, 'pending')"
            );
            mysqli_stmt_bind_param($statement, 'is', $farmerId, $email);
            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);
            set_flash('success', 'The invitation request was saved. No email has been sent by the system.');
        } catch (mysqli_sql_exception $exception) {
            error_log('Veterinarian invitation failed: ' . $exception->getMessage());
            set_flash('error', 'The invitation request could not be saved.');
        }
    }

    header('Location: invite-veterinarian.php');
    exit;
}

$page_title = 'Invite Veterinarian';
include '../includes/header.php';
?>
<div class="layout">
    <?php include '../farmer/includes/sider-bar.php'; ?>
    <main class="container">
        <div class="form-box" id="invite-veterinarian-form">
            <?php render_flash_message(); ?>
            <form action="invite-veterinarian.php" method="post">
                <h3>Invite Veterinarian</h3>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="50" required>
                <button type="submit" name="invite">Send Invitation</button>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
