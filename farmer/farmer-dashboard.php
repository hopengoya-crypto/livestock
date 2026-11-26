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

$userStatement = mysqli_prepare($conn, 'SELECT full_name FROM `user` WHERE user_id = ?');
$userId = current_user_id();
mysqli_stmt_bind_param($userStatement, 'i', $userId);
mysqli_stmt_execute($userStatement);
$userResult = mysqli_stmt_get_result($userStatement);
$user = mysqli_fetch_assoc($userResult);
mysqli_stmt_close($userStatement);
$farmerName = $user['full_name'] ?? 'Farmer';

$livestockStatement = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM livestock WHERE farmer_id = ?');
mysqli_stmt_bind_param($livestockStatement, 'i', $farmerId);
mysqli_stmt_execute($livestockStatement);
$livestockCount = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($livestockStatement))['total'];
mysqli_stmt_close($livestockStatement);

$scheduleCountStatement = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total FROM schedule
     WHERE farmer_id = ? AND schedule_date >= CURDATE()
       AND LOWER(schedule_status) NOT IN ('cancelled', 'completed')"
);
mysqli_stmt_bind_param($scheduleCountStatement, 'i', $farmerId);
mysqli_stmt_execute($scheduleCountStatement);
$upcomingCount = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($scheduleCountStatement))['total'];
mysqli_stmt_close($scheduleCountStatement);

$requestCountStatement = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total FROM veterinarian_request
     WHERE farmer_id = ? AND LOWER(request_status) = 'pending'"
);
mysqli_stmt_bind_param($requestCountStatement, 'i', $farmerId);
mysqli_stmt_execute($requestCountStatement);
$pendingRequestCount = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($requestCountStatement))['total'];
mysqli_stmt_close($requestCountStatement);

$upcomingStatement = mysqli_prepare(
    $conn,
    "SELECT s.schedule_id, s.schedule_date, s.schedule_status, s.vaccine_name,
            v_user.full_name AS veterinarian_name
     FROM schedule s
     INNER JOIN veterinarian v ON v.vet_id = s.vet_id
     INNER JOIN `user` v_user ON v_user.user_id = v.user_id
     WHERE s.farmer_id = ? AND s.schedule_date >= CURDATE()
       AND LOWER(s.schedule_status) NOT IN ('cancelled', 'completed')
     ORDER BY s.schedule_date, s.schedule_id
     LIMIT 5"
);
mysqli_stmt_bind_param($upcomingStatement, 'i', $farmerId);
mysqli_stmt_execute($upcomingStatement);
$upcomingSchedules = mysqli_stmt_get_result($upcomingStatement);

$recentRecordsStatement = mysqli_prepare(
    $conn,
    'SELECT r.record_id, r.vaccination_date, r.vaccination_status, r.dosage,
            s.vaccine_name, l.livestock_id
     FROM `record` r
     INNER JOIN schedule s ON s.schedule_id = r.schedule_id
     LEFT JOIN livestock l ON l.livestock_id = s.livestock_id
     WHERE s.farmer_id = ?
     ORDER BY r.vaccination_date DESC, r.record_id DESC
     LIMIT 5'
);
mysqli_stmt_bind_param($recentRecordsStatement, 'i', $farmerId);
mysqli_stmt_execute($recentRecordsStatement);
$recentRecords = mysqli_stmt_get_result($recentRecordsStatement);

$page_title = 'Farmer Dashboard';
include '../includes/header.php';
?>
<div class="layout">
    <?php include __DIR__ . '/includes/sider-bar.php'; ?>
    <main class="container farmer-dashboard">
        <h1>Farmer Dashboard</h1>
        <p>Welcome, <?php echo escape_html($farmerName); ?>. Here is your livestock and vaccination overview.</p>
        <?php render_flash_message(); ?>

        <section class="dashboard-stats" aria-label="Farm summary">
            <article class="dashboard-card">
                <h2>Registered Livestock</h2>
                <strong><?php echo escape_html($livestockCount); ?></strong>
                <a href="livestock.php">Register livestock</a>
            </article>
            <article class="dashboard-card">
                <h2>Upcoming Vaccinations</h2>
                <strong><?php echo escape_html($upcomingCount); ?></strong>
                <a href="request.php">View schedules</a>
            </article>
            <article class="dashboard-card">
                <h2>Pending Vet Requests</h2>
                <strong><?php echo escape_html($pendingRequestCount); ?></strong>
                <a href="request.php">View requests</a>
            </article>
        </section>

        <section class="content-panel">
            <h2>Upcoming Vaccinations</h2>
            <table>
                <thead>
                    <tr><th>Date</th><th>Vaccine</th><th>Veterinarian</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($upcomingSchedules) === 0): ?>
                    <tr><td colspan="4">No upcoming vaccinations are scheduled.</td></tr>
                <?php else: ?>
                    <?php while ($schedule = mysqli_fetch_assoc($upcomingSchedules)): ?>
                        <tr>
                            <td><?php echo escape_html($schedule['schedule_date']); ?></td>
                            <td><?php echo escape_html($schedule['vaccine_name']); ?></td>
                            <td><?php echo escape_html($schedule['veterinarian_name']); ?></td>
                            <td><?php echo escape_html($schedule['schedule_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="content-panel">
            <h2>Recent Vaccination Records</h2>
            <table>
                <thead>
                    <tr><th>Date</th><th>Livestock ID</th><th>Vaccine</th><th>Dosage</th><th>Status</th></tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($recentRecords) === 0): ?>
                    <tr><td colspan="5">No vaccination records are available yet.</td></tr>
                <?php else: ?>
                    <?php while ($record = mysqli_fetch_assoc($recentRecords)): ?>
                        <tr>
                            <td><?php echo escape_html($record['vaccination_date']); ?></td>
                            <td><?php echo escape_html($record['livestock_id'] ?? ''); ?></td>
                            <td><?php echo escape_html($record['vaccine_name']); ?></td>
                            <td><?php echo escape_html($record['dosage']); ?></td>
                            <td><?php echo escape_html($record['vaccination_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <p><a href="records.php">View all vaccination records</a></p>
        </section>

        <section class="content-panel">
            <h2>Quick Actions</h2>
            <div class="dashboard-actions">
                <a href="livestock.php">Register livestock</a>
                <a href="select.php">Select a veterinarian</a>
                <a href="invite-veterinarian.php">Invite a veterinarian</a>
                <a href="update.php">Update livestock information</a>
            </div>
        </section>
    </main>
</div>
<?php
mysqli_stmt_close($upcomingStatement);
mysqli_stmt_close($recentRecordsStatement);
include '../includes/footer.php';
?>
