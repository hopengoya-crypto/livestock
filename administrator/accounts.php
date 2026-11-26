<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('administrator');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = filter_var($_POST['user_id'] ?? null, FILTER_VALIDATE_INT);
    $status = trim((string) ($_POST['account_status'] ?? ''));

    if ($userId === false || $userId === null || $userId < 1 || !in_array($status, ['active', 'inactive'], true)) {
        set_flash('error', 'Choose a valid user account and status.');
    } elseif ($userId === current_user_id() && $status !== 'active') {
        set_flash('error', 'You cannot deactivate your own administrator account.');
    } else {
        try {
            $statement = mysqli_prepare($conn, 'UPDATE `user` SET account_status = ? WHERE user_id = ?');
            mysqli_stmt_bind_param($statement, 'si', $status, $userId);
            mysqli_stmt_execute($statement);
            $updated = mysqli_stmt_affected_rows($statement);
            mysqli_stmt_close($statement);
            set_flash($updated === 1 ? 'success' : 'error', $updated === 1
                ? 'Account status updated successfully.'
                : 'The requested user account was not found or was unchanged.');
        } catch (mysqli_sql_exception $exception) {
            error_log('Account status update failed: ' . $exception->getMessage());
            set_flash('error', 'Account status could not be updated.');
        }
    }

    header('Location: accounts.php');
    exit;
}

$users = mysqli_query(
    $conn,
    'SELECT user_id, full_name, email_address, role, account_status
     FROM `user` ORDER BY user_id'
);
$page_title = 'Manage User Accounts';
include '../includes/header.php';
?>
<div class="layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="container">
        <section>
            <h2>Manage User Accounts</h2>
            <?php render_flash_message(); ?>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($users) === 0): ?>
                    <tr><td colspan="6">No user accounts found.</td></tr>
                <?php else: ?>
                    <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <?php $active = strtolower((string) $user['account_status']) === 'active'; ?>
                        <tr>
                            <td><?php echo escape_html($user['user_id']); ?></td>
                            <td><?php echo escape_html($user['full_name']); ?></td>
                            <td><?php echo escape_html($user['email_address']); ?></td>
                            <td><?php echo escape_html($user['role']); ?></td>
                            <td><?php echo $active ? 'Active' : 'Inactive'; ?></td>
                            <td>
                                <?php if ((int) $user['user_id'] === current_user_id()): ?>
                                    Current account
                                <?php else: ?>
                                    <form action="accounts.php" method="post">
                                        <input type="hidden" name="user_id" value="<?php echo escape_html($user['user_id']); ?>">
                                        <input type="hidden" name="account_status" value="<?php echo $active ? 'inactive' : 'active'; ?>">
                                        <button type="submit"><?php echo $active ? 'Deactivate' : 'Activate'; ?></button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
