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
    $farmerId = filter_var($_POST['farmer_id'] ?? null, FILTER_VALIDATE_INT);
    $livestockId = filter_var($_POST['livestock_id'] ?? null, FILTER_VALIDATE_INT);
    $vaccineId = filter_var($_POST['vaccine_id'] ?? null, FILTER_VALIDATE_INT);
    $scheduleDate = trim((string) ($_POST['scheduled_date'] ?? ''));
    $scheduledTime = trim((string) ($_POST['scheduled_time'] ?? ''));
    $status = trim((string) ($_POST['status'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));

    if ($farmerId === false || $farmerId === null || $livestockId === false || $livestockId === null
        || $vaccineId === false || $vaccineId === null || !valid_iso_date($scheduleDate)
        || ($scheduledTime !== '' && !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $scheduledTime))
        || !in_array($status, ['scheduled', 'postponed', 'cancelled', 'pending'], true)) {
        set_flash('error', 'Please provide valid schedule details.');
    } else {
        try {
            $lookup = mysqli_prepare(
                $conn,
                "SELECT l.livestock_species, v.vaccine_name
                 FROM livestock l
                 INNER JOIN vaccine v ON v.vaccine_id = ?
                 WHERE l.livestock_id = ? AND l.farmer_id = ?
                   AND (v.target_species = 'multiple' OR v.target_species = l.livestock_species)"
            );
            mysqli_stmt_bind_param($lookup, 'iii', $vaccineId, $livestockId, $farmerId);
            mysqli_stmt_execute($lookup);
            mysqli_stmt_bind_result($lookup, $livestockSpecies, $vaccineName);
            $detailsFound = mysqli_stmt_fetch($lookup);
            mysqli_stmt_close($lookup);

            if (!$detailsFound) {
                set_flash('error', 'The selected livestock and vaccine do not match an available record.');
            } else {
                $timeValue = $scheduledTime === '' ? null : $scheduledTime;
                $statement = mysqli_prepare(
                    $conn,
                    'INSERT INTO schedule
                        (farmer_id, livestock_id, vet_id, vaccine_id, vaccine_name, schedule_date, schedule_status, scheduled_time, notes)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                mysqli_stmt_bind_param(
                    $statement,
                    'iiiisssss',
                    $farmerId,
                    $livestockId,
                    $vetId,
                    $vaccineId,
                    $vaccineName,
                    $scheduleDate,
                    $status,
                    $timeValue,
                    $notes
                );
                mysqli_stmt_execute($statement);
                mysqli_stmt_close($statement);
                set_flash('success', 'Vaccination schedule saved successfully.');
            }
        } catch (mysqli_sql_exception $exception) {
            error_log('Vaccination schedule save failed: ' . $exception->getMessage());
            set_flash('error', 'Vaccination schedule could not be saved.');
        }
    }

    header('Location: schedule.php');
    exit;
}

$farmers = mysqli_query(
    $conn,
    "SELECT f.farmer_id, u.full_name FROM farmer f
     INNER JOIN `user` u ON u.user_id = f.user_id
     WHERE u.account_status = 'active' ORDER BY u.full_name"
);
$vaccines = mysqli_query($conn, 'SELECT vaccine_id, vaccine_name, target_species FROM vaccine ORDER BY vaccine_name');
$scheduleStatement = mysqli_prepare(
    $conn,
    'SELECT schedule_id, livestock_id, vaccine_name, schedule_date, schedule_status
     FROM schedule WHERE vet_id = ? ORDER BY schedule_date DESC, schedule_id DESC'
);
mysqli_stmt_bind_param($scheduleStatement, 'i', $vetId);
mysqli_stmt_execute($scheduleStatement);
$schedules = mysqli_stmt_get_result($scheduleStatement);

$page_title = 'Vaccination Schedules';
include '../includes/header.php';
?>
<div class="layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="container form-page">
        <h1>Vaccination Schedules</h1>
        <a href="veterinarian-dashboard.php">Back to Dashboard</a>
        <?php render_flash_message(); ?>
        <section class="content-panel">
            <h2>Create / Confirm Vaccination Schedule</h2>
            <div class="form-box" id="register-form">
                <form action="schedule.php" method="post">
                    <label for="farmer">Farmer</label>
                    <select id="farmer" name="farmer_id" required>
                        <option value="">Select farmer</option>
                        <?php while ($farmer = mysqli_fetch_assoc($farmers)): ?>
                            <option value="<?php echo escape_html($farmer['farmer_id']); ?>">
                                <?php echo escape_html($farmer['full_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label for="livestock">Livestock ID</label>
                    <input type="number" id="livestock" name="livestock_id" min="1" required>
                    <label for="vaccine">Vaccine</label>
                    <select id="vaccine" name="vaccine_id" required>
                        <option value="">Select vaccine</option>
                        <?php while ($vaccine = mysqli_fetch_assoc($vaccines)): ?>
                            <option value="<?php echo escape_html($vaccine['vaccine_id']); ?>">
                                <?php echo escape_html($vaccine['vaccine_name'] . ' (' . $vaccine['target_species'] . ')'); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label for="date">Scheduled Date</label>
                    <input type="date" id="date" name="scheduled_date" required>
                    <label for="time">Scheduled Time</label>
                    <input type="time" id="time" name="scheduled_time">
                    <label for="status">Schedule Status</label>
                    <select id="status" name="status" required>
                        <option value="scheduled">Scheduled</option>
                        <option value="postponed">Postponed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="pending">Pending</option>
                    </select>
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes"></textarea>
                    <button type="submit">Save Schedule</button>
                </form>
            </div>
        </section>
        <section class="content-panel">
            <h2>Scheduled Vaccinations</h2>
            <table>
                <thead><tr><th>Schedule ID</th><th>Livestock ID</th><th>Farmer ID</th><th>Vaccine</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($schedules) === 0): ?>
                    <tr><td colspan="5">No records available yet.</td></tr>
                <?php else: ?>
                    <?php while ($schedule = mysqli_fetch_assoc($schedules)): ?>
                        <tr>
                            <td><?php echo escape_html($schedule['schedule_id']); ?></td>
                            <td><?php echo escape_html($schedule['livestock_id'] ?? ''); ?></td>
                            <td><?php echo escape_html($schedule['farmer_id']?? ''); ?></td>
                            <td><?php echo escape_html($schedule['vaccine_name']); ?></td>
                            <td><?php echo escape_html($schedule['schedule_date']); ?></td>
                            <td><?php echo escape_html($schedule['schedule_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
<?php
mysqli_stmt_close($scheduleStatement);
include '../includes/footer.php';
?>
