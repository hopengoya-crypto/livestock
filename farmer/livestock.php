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
    $species = trim((string) ($_POST['species'] ?? ''));
    $breed = trim((string) ($_POST['breed'] ?? ''));
    $dateOfBirth = trim((string) ($_POST['date_of_birth'] ?? ''));
    $healthStatus = trim((string) ($_POST['status'] ?? ''));
    $gender = trim((string) ($_POST['gender'] ?? ''));

    if (!in_array($species, ['cattle', 'goats', 'sheep', 'poultry'], true)
        || $breed === '' || strlen($breed) > 10
        || !valid_iso_date($dateOfBirth)
        || !in_array($healthStatus, ['Healthy', 'Needs attention', 'Under treatment'], true)
        || !in_array($gender, ['male', 'female'], true)) {
        set_flash('error', 'Please provide valid livestock details.');
    } else {
        try {
            $statement = mysqli_prepare(
                $conn,
                'INSERT INTO livestock (farmer_id, livestock_species, livestock_breed, date_of_birth, health_status, gender)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param($statement, 'isssss', $farmerId, $species, $breed, $dateOfBirth, $healthStatus, $gender);
            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);
            set_flash('success', 'Livestock registered successfully.');
        } catch (mysqli_sql_exception $exception) {
            error_log('Livestock registration failed: ' . $exception->getMessage());
            set_flash('error', 'Livestock could not be registered. Check that the breed is not already in use.');
        }
    }

    header('Location: livestock.php');
    exit;
}

$page_title = 'Register Livestock';
include '../includes/header.php';
?>
<div class="layout">
<?php include '../farmer/includes/sider-bar.php'; ?>
<main class="container">
    <div class="form-box" id="register-form">
        <?php render_flash_message(); ?>
        <form action="livestock.php" method="post">
            <h3>Register livestock</h3>
            <legend>Select type</legend>
            <label for="species">Species</label>
            <select id="species" name="species" required>
                <option value="">Choose a type</option>
                <option value="cattle">Cattle</option>
                <option value="goats">Goats</option>
                <option value="sheep">Sheep</option>
                <option value="poultry">Poultry</option>
            </select>
            <label for="breed">Breed</label>
            <input type="text" id="breed" name="breed" maxlength="10" required>
            <label for="date-of-birth">Date of Birth</label>
            <input type="date" id="date-of-birth" name="date_of_birth" required>
            <label for="status">Health Status</label>
            <select id="status" name="status" required>
                <option value="Healthy">Healthy</option>
                <option value="Needs attention">Needs attention</option>
                <option value="Under treatment">Under treatment</option>
            </select>
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="">Choose a gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</main>
</div>
<?php include '../includes/footer.php'; ?>
