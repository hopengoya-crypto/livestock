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

$requestStatement = mysqli_prepare(
    $conn,
    'SELECT r.request_id, r.request_status, r.requested_at, u.full_name AS veterinarian_name
     FROM veterinarian_request r
     INNER JOIN veterinarian v ON v.vet_id = r.vet_id
     INNER JOIN `user` u ON u.user_id = v.user_id
     WHERE r.farmer_id = ?
     ORDER BY r.requested_at DESC, r.request_id DESC'
);
mysqli_stmt_bind_param($requestStatement, 'i', $farmerId);
mysqli_stmt_execute($requestStatement);
$requests = mysqli_stmt_get_result($requestStatement);

$scheduleStatement = mysqli_prepare(
    $conn,
    'SELECT s.schedule_id, s.schedule_date, s.schedule_status, s.vaccine_name,
            l.livestock_id, u.full_name AS veterinarian_name,
            r.vaccination_status, r.next_vaccination_date
     FROM schedule s
     INNER JOIN veterinarian v ON v.vet_id = s.vet_id
     INNER JOIN `user` u ON u.user_id = v.user_id
     LEFT JOIN livestock l ON l.livestock_id = s.livestock_id
     LEFT JOIN `record` r ON r.schedule_id = s.schedule_id
     WHERE s.farmer_id = ?
     ORDER BY s.schedule_date DESC, s.schedule_id DESC'
);
mysqli_stmt_bind_param($scheduleStatement, 'i', $farmerId);
mysqli_stmt_execute($scheduleStatement);
$schedules = mysqli_stmt_get_result($scheduleStatement);

$page_title = 'Veterinarian Requests and Schedules';
include '../includes/header.php';
?>
<div class="layout">
    <?php include __DIR__ . '/includes/sider-bar.php'; ?>
    <main class="container farmer-dashboard">
        <h1>Veterinarian Requests and Schedules</h1>
        <p>Review your veterinarian requests and vaccination schedule updates.</p>
        <?php render_flash_message(); ?>
        <div class="dashboard-actions">
            <a href="select.php">Request a veterinarian</a>
            <a href="invite-veterinarian.php">Invite a veterinarian</a>
        </div>

        <section class="content-panel">
            <h2>Veterinarian Requests</h2>
            <table>
                <thead><tr><th>Request ID</th><th>Veterinarian</th><th>Requested</th><th>Status</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($requests) === 0): ?>
                    <tr><td colspan="4">You have no veterinarian requests yet.</td></tr>
                <?php else: ?>
                    <?php while ($request = mysqli_fetch_assoc($requests)): ?>
                        <tr>
                            <td><?php echo escape_html($request['request_id']); ?></td>
                            <td><?php echo escape_html($request['veterinarian_name']); ?></td>
                            <td><?php echo escape_html($request['requested_at']); ?></td>
                            <td><?php echo escape_html($request['request_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="content-panel">
            <h2>Vaccination Schedules</h2>
            <table>
                <thead>
                    <tr><th>Date</th><th>Livestock ID</th><th>Vaccine</th><th>Veterinarian</th><th>Schedule Status</th><th>Record Status</th><th>Next Date</th></tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($schedules) === 0): ?>
                    <tr><td colspan="7">No vaccination schedules are available yet. Request a veterinarian to arrange one.</td></tr>
                <?php else: ?>
                    <?php while ($schedule = mysqli_fetch_assoc($schedules)): ?>
                        <tr>
                            <td><?php echo escape_html($schedule['schedule_date']); ?></td>
                            <td><?php echo escape_html($schedule['livestock_id'] ?? ''); ?></td>
                            <td><?php echo escape_html($schedule['vaccine_name']); ?></td>
                            <td><?php echo escape_html($schedule['veterinarian_name']); ?></td>
                            <td><?php echo escape_html($schedule['schedule_status']); ?></td>
                            <td><?php echo escape_html($schedule['vaccination_status'] ?? 'Not recorded'); ?></td>
                            <td><?php echo escape_html($schedule['next_vaccination_date'] ?? 'Not set'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
<?php
mysqli_stmt_close($requestStatement);
mysqli_stmt_close($scheduleStatement);
include '../includes/footer.php';
?>
