<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('veterinarian');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$vetId = role_profile_id($conn, 'veterinarian', current_user_id());
if ($vetId === null) {
    http_response_code(409);
    exit('Your veterinarian profile is not available.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['vaccine_name'] ?? ''));
    $disease = trim((string) ($_POST['disease_prevented'] ?? ''));
    $species = trim((string) ($_POST['target_species'] ?? ''));
    $dosage = trim((string) ($_POST['recommended_dosage'] ?? ''));
    $manufacturer = trim((string) ($_POST['manufacturer'] ?? ''));
    $storage = trim((string) ($_POST['storage_requirements'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));

    if ($name === '' || strlen($name) > 100 || $disease === '' || strlen($disease) > 100
        || !in_array($species, ['cattle', 'goats', 'sheep', 'poultry', 'pigs', 'multiple'], true)
        || strlen($dosage) > 50 || strlen($manufacturer) > 100 || strlen($storage) > 150) {
        set_flash('error', 'Please provide valid vaccine details.');
    } else {
        try {
            $statement = mysqli_prepare(
                $conn,
                'INSERT INTO vaccine
                    (vaccine_name, disease_prevented, target_species, recommended_dosage, manufacturer, storage_requirements, notes)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param($statement, 'sssssss', $name, $disease, $species, $dosage, $manufacturer, $storage, $notes);
            mysqli_stmt_execute($statement);
            mysqli_stmt_close($statement);
            set_flash('success', 'Vaccine details saved successfully.');
        } catch (mysqli_sql_exception $exception) {
            error_log('Vaccine save failed: ' . $exception->getMessage());
            set_flash('error', 'Vaccine details could not be saved. Check that the vaccine name is not already in use.');
        }
    }

    header('Location: vaccine.php');
    exit;
}

$vaccines = mysqli_query($conn, 'SELECT vaccine_name, disease_prevented, target_species, recommended_dosage FROM vaccine ORDER BY vaccine_name');
$page_title = 'Vaccine Details';
include '../includes/header.php';
?>
<div class="layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="container form-page">
        <h1>Vaccine Details</h1>
        <a href="veterinarian-dashboard.php">Back to Dashboard</a>
        <?php render_flash_message(); ?>
        <section class="content-panel">
            <h2>Add Vaccine Information</h2>
            <div class="form-box" id="register-form">
                <form action="vaccine.php" method="post">
                    <label for="name">Vaccine Name</label>
                    <input type="text" id="name" name="vaccine_name" maxlength="100" required>
                    <label for="disease">Disease Prevented</label>
                    <input type="text" id="disease" name="disease_prevented" maxlength="100" required>
                    <label for="species">Target Species</label>
                    <select id="species" name="target_species" required>
                        <option value="">Select species</option>
                        <option value="cattle">Cattle</option>
                        <option value="goats">Goats</option>
                        <option value="sheep">Sheep</option>
                        <option value="poultry">Poultry</option>
                        <option value="pigs">Pigs</option>
                        <option value="multiple">Multiple species</option>
                    </select>
                    <label for="dosage">Recommended Dosage</label>
                    <input type="text" id="dosage" name="recommended_dosage" maxlength="50">
                    <label for="manufacturer">Manufacturer</label>
                    <input type="text" id="manufacturer" name="manufacturer" maxlength="100">
                    <label for="storage">Storage Requirements</label>
                    <input type="text" id="storage" name="storage_requirements" maxlength="150">
                    <label for="notes">Additional Information</label>
                    <textarea id="notes" name="notes"></textarea>
                    <button type="submit">Save Vaccine Details</button>
                </form>
            </div>
        </section>
        <section class="content-panel">
            <h2>Available Vaccines</h2>
            <table>
                <thead><tr><th>Name</th><th>Disease Prevented</th><th>Target Species</th><th>Recommended Dosage</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($vaccines) === 0): ?>
                    <tr><td colspan="4">No vaccines are available yet.</td></tr>
                <?php else: ?>
                    <?php while ($vaccine = mysqli_fetch_assoc($vaccines)): ?>
                        <tr>
                            <td><?php echo escape_html($vaccine['vaccine_name']); ?></td>
                            <td><?php echo escape_html($vaccine['disease_prevented']); ?></td>
                            <td><?php echo escape_html($vaccine['target_species']); ?></td>
                            <td><?php echo escape_html($vaccine['recommended_dosage'] ?? ''); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
