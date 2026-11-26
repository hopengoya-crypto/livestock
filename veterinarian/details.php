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
    $scheduleId = filter_var($_POST['schedule_id'] ?? null, FILTER_VALIDATE_INT);
    $vaccinationDate = trim((string) ($_POST['vaccination_date'] ?? ''));
    $dosage = trim((string) ($_POST['dosage'] ?? ''));
    $nextDate = trim((string) ($_POST['next_vaccination_date'] ?? ''));
    $status = trim((string) ($_POST['vaccination_status'] ?? ''));
    $remarks = trim((string) ($_POST['remarks'] ?? ''));

    if ($scheduleId === false || $scheduleId === null || !valid_iso_date($vaccinationDate)
        || $dosage === '' || strlen($dosage) > 50
        || ($nextDate !== '' && (!valid_iso_date($nextDate) || $nextDate < $vaccinationDate))
        || !in_array($status, ['completed', 'postponed', 'missed'], true)) {
        set_flash('error', 'Please provide valid vaccination record details.');
    } else {
        try {
            $conn->begin_transaction();
            $lookup = mysqli_prepare(
                $conn,
                'SELECT s.schedule_id FROM schedule s
                 LEFT JOIN `record` r ON r.schedule_id = s.schedule_id
                 WHERE s.schedule_id = ? AND s.vet_id = ? AND r.record_id IS NULL
                 FOR UPDATE'
            );
            mysqli_stmt_bind_param($lookup, 'ii', $scheduleId, $vetId);
            mysqli_stmt_execute($lookup);
            mysqli_stmt_store_result($lookup);
            $scheduleAvailable = mysqli_stmt_num_rows($lookup) === 1;
            mysqli_stmt_close($lookup);

            if (!$scheduleAvailable) {
                $conn->rollback();
                set_flash('error', 'The selected schedule is unavailable or already has a record.');
            } else {
                $nextDateValue = $nextDate === '' ? null : $nextDate;
                $insert = mysqli_prepare(
                    $conn,
                    'INSERT INTO `record`
                        (schedule_id, vaccination_date, dosage, next_vaccination_date, vaccination_status, remarks)
                     VALUES (?, ?, ?, ?, ?, ?)'
                );
                mysqli_stmt_bind_param(
                    $insert,
                    'isssss',
                    $scheduleId,
                    $vaccinationDate,
                    $dosage,
                    $nextDateValue,
                    $status,
                    $remarks
                );
                mysqli_stmt_execute($insert);
                mysqli_stmt_close($insert);

                $update = mysqli_prepare($conn, 'UPDATE schedule SET schedule_status = ? WHERE schedule_id = ? AND vet_id = ?');
                mysqli_stmt_bind_param($update, 'sii', $status, $scheduleId, $vetId);
                mysqli_stmt_execute($update);
                mysqli_stmt_close($update);
                $conn->commit();
                set_flash('success', 'Vaccination record saved successfully.');
            }
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            error_log('Vaccination record save failed: ' . $exception->getMessage());
            set_flash('error', 'Vaccination record could not be saved.');
        }
    }

    header('Location: details.php');
    exit;
}

$scheduleStatement = mysqli_prepare(
    $conn,
    'SELECT s.schedule_id, s.vaccine_name, s.schedule_date, s.livestock_id, u.full_name AS farmer_name
     FROM schedule s
     INNER JOIN farmer f ON f.farmer_id = s.farmer_id
     INNER JOIN `user` u ON u.user_id = f.user_id
     LEFT JOIN `record` r ON r.schedule_id = s.schedule_id
     WHERE s.vet_id = ? AND r.record_id IS NULL
     ORDER BY s.schedule_date, s.schedule_id'
);
mysqli_stmt_bind_param($scheduleStatement, 'i', $vetId);
mysqli_stmt_execute($scheduleStatement);
$availableSchedules = mysqli_stmt_get_result($scheduleStatement);

$page_title = 'Record Vaccination Details';
include '../includes/header.php';
?>
<div class="layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="container form-page">
        <h1>Record Vaccination Details</h1>
        <a href="veterinarian-dashboard.php">Back to Dashboard</a>
        <?php render_flash_message(); ?>
        <section class="content-panel">
            <h2>Vaccination Record</h2>
            <div class="form-box" id="register-form">
                <form action="details.php" method="post">
                    <label for="schedule">Vaccination Schedule</label>
                    <select id="schedule" name="schedule_id" required>
                        <option value="">Select a schedule</option>
                        <?php while ($schedule = mysqli_fetch_assoc($availableSchedules)): ?>
                            <option value="<?php echo escape_html($schedule['schedule_id']); ?>">
                                <?php echo escape_html(
                                    'Schedule #' . $schedule['schedule_id'] . ' — ' . $schedule['farmer_name']
                                    . ', livestock #' . $schedule['livestock_id'] . ', ' . $schedule['vaccine_name']
                                    . ' (' . $schedule['schedule_date'] . ')'
                                ); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <label for="dosage">Dosage Administered</label>
                    <input type="text" id="dosage" name="dosage" maxlength="50" placeholder="e.g. 2 ml" required>
                    <label for="date">Date Administered</label>
                    <input type="date" id="date" name="vaccination_date" required>
                    <label for="next-date">Next Vaccination Date</label>
                    <input type="date" id="next-date" name="next_vaccination_date">
                    <label for="status">Vaccination Status</label>
                    <select id="status" name="vaccination_status" required>
                        <option value="">Select status</option>
                        <option value="completed">Completed</option>
                        <option value="postponed">Postponed</option>
                        <option value="missed">Missed</option>
                    </select>
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" placeholder="Enter additional observations"></textarea>
                    <button type="submit">Save Vaccination Record and Status</button>
                </form>
            </div>
        </section>
    </main>
</div>
<?php
mysqli_stmt_close($scheduleStatement);
include '../includes/footer.php';
?>
