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
    $vetId = filter_var($_POST['vet_id'] ?? null, FILTER_VALIDATE_INT);
    if ($vetId === false || $vetId === null || $vetId < 1) {
        set_flash('error', 'Choose a valid veterinarian.');
    } else {
        try {
            $check = mysqli_prepare($conn, 'SELECT vet_id FROM veterinarian WHERE vet_id = ?');
            mysqli_stmt_bind_param($check, 'i', $vetId);
            mysqli_stmt_execute($check);
            mysqli_stmt_store_result($check);
            $vetExists = mysqli_stmt_num_rows($check) === 1;
            mysqli_stmt_close($check);

            if (!$vetExists) {
                set_flash('error', 'The selected veterinarian is not available.');
            } else {
                $insert = mysqli_prepare(
                    $conn,
                    "INSERT INTO veterinarian_request (farmer_id, vet_id, request_status) VALUES (?, ?, 'pending')"
                );
                mysqli_stmt_bind_param($insert, 'ii', $farmerId, $vetId);
                mysqli_stmt_execute($insert);
                mysqli_stmt_close($insert);
                set_flash('success', 'Your veterinarian request has been saved.');
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('Veterinarian request failed: ' . $exception->getMessage());
            set_flash('error', 'Your veterinarian request could not be saved.');
        }
    }

    header('Location: select.php');
    exit;
}

$veterinarians = mysqli_query(
    $conn,
    "SELECT v.vet_id, u.full_name FROM veterinarian v
     INNER JOIN `user` u ON u.user_id = v.user_id
     WHERE u.account_status = 'active' ORDER BY u.full_name"
);
$page_title = 'Select Veterinarian';
include '../includes/header.php';
?>
<div class="layout">
    <?php include '../farmer/includes/sider-bar.php'; ?>
    <main class="container">
        <div class="form-box" id="select-veterinarian-form">
            <?php render_flash_message(); ?>
            <form action="select.php" method="post">
                <h3>Select Veterinarian</h3>
                <legend>Request a veterinarian</legend>
                <label for="veterinarian">Veterinarian</label>
                <select id="veterinarian" name="vet_id" required>
                    <option value="">Choose a veterinarian</option>
                    <?php while ($veterinarian = mysqli_fetch_assoc($veterinarians)): ?>
                        <option value="<?php echo escape_html($veterinarian['vet_id']); ?>">
                            <?php echo escape_html($veterinarian['full_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" name="request">Send request</button>
                <p>Once submitted, your request will be available to the veterinarian.</p>
                <p>Have your own veterinarian? Invite them to join the system.
                    <a href="invite-veterinarian.php">Invite Veterinarian</a>
                </p>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
