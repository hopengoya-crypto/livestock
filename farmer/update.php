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
    $livestockId = filter_var($_POST['livestock_id'] ?? null, FILTER_VALIDATE_INT);
    $species = trim((string) ($_POST['species'] ?? ''));
    $healthStatus = trim((string) ($_POST['health'] ?? ''));

    if ($livestockId === false || $livestockId === null
        || !in_array($species, ['cattle', 'goats', 'sheep', 'poultry'], true)
        || !in_array($healthStatus, ['Healthy', 'Needs attention', 'Under treatment'], true)) {
        set_flash('error', 'Please provide a valid livestock ID, species, and health status.');
    } else {
        try {
            $check = mysqli_prepare($conn, 'SELECT livestock_id FROM livestock WHERE livestock_id = ? AND farmer_id = ?');
            mysqli_stmt_bind_param($check, 'ii', $livestockId, $farmerId);
            mysqli_stmt_execute($check);
            mysqli_stmt_store_result($check);
            $ownsLivestock = mysqli_stmt_num_rows($check) === 1;
            mysqli_stmt_close($check);

            if (!$ownsLivestock) {
                set_flash('error', 'That livestock record was not found in your account.');
            } else {
                $statement = mysqli_prepare(
                    $conn,
                    'UPDATE livestock SET livestock_species = ?, health_status = ?
                     WHERE livestock_id = ? AND farmer_id = ?'
                );
                mysqli_stmt_bind_param($statement, 'ssii', $species, $healthStatus, $livestockId, $farmerId);
                mysqli_stmt_execute($statement);
                mysqli_stmt_close($statement);
                set_flash('success', 'Livestock information updated successfully.');
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('Livestock update failed: ' . $exception->getMessage());
            set_flash('error', 'Livestock information could not be updated.');
        }
    }

    header('Location: update.php');
    exit;
}

$page_title = 'Update Livestock Information';
include '../includes/header.php';
?>
<div class="layout">
<?php include '../farmer/includes/sider-bar.php'; ?>
<main class="container">
    <div class="form-box" id="update-form">
        <?php render_flash_message(); ?>
        <form action="update.php" method="post">
            <h3>Update Livestock Information</h3>
            <label for="livestock-id">Livestock ID</label>
            <input type="number" id="livestock-id" name="livestock_id" min="1" required>
            <label for="species">Species</label>
            <select id="species" name="species" required>
                <option value="">Choose a type</option>
                <option value="cattle">Cattle</option>
                <option value="goats">Goats</option>
                <option value="sheep">Sheep</option>
                <option value="poultry">Poultry</option>
            </select>
            <label for="health">Health Status</label>
            <select id="health" name="health" required>
                <option value="Healthy">Healthy</option>
                <option value="Needs attention">Needs attention</option>
                <option value="Under treatment">Under treatment</option>
            </select>
            <button type="submit" name="update">Update information</button>
        </form>
    </div>
</main>
</div>
<?php include '../includes/footer.php'; ?>
