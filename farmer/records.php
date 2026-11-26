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

$search = trim((string) ($_GET['search'] ?? ''));
$sql = 'SELECT r.record_id, l.livestock_id, s.vaccine_name, r.vaccination_date, r.dosage,
               u.full_name AS veterinarian_name, r.next_vaccination_date, r.vaccination_status
        FROM `record` r
        INNER JOIN schedule s ON s.schedule_id = r.schedule_id
        LEFT JOIN livestock l ON l.livestock_id = s.livestock_id
        INNER JOIN veterinarian v ON v.vet_id = s.vet_id
        INNER JOIN `user` u ON u.user_id = v.user_id
        WHERE s.farmer_id = ?';
if ($search !== '') {
    $sql .= ' AND (CAST(l.livestock_id AS CHAR) LIKE ? OR s.vaccine_name LIKE ? OR r.vaccination_status LIKE ?)';
}
$sql .= ' ORDER BY r.vaccination_date DESC, r.record_id DESC';

$statement = mysqli_prepare($conn, $sql);
if ($search !== '') {
    $searchPattern = '%' . $search . '%';
    mysqli_stmt_bind_param($statement, 'isss', $farmerId, $searchPattern, $searchPattern, $searchPattern);
} else {
    mysqli_stmt_bind_param($statement, 'i', $farmerId);
}
mysqli_stmt_execute($statement);
$records = mysqli_stmt_get_result($statement);

$page_title = 'Vaccination Records';
include '../includes/header.php';
?>
<div class="layout">
<?php include '../farmer/includes/sider-bar.php'; ?>
<main class="records-container">
    <h3>Vaccination Records</h3>
    <p>View your livestock vaccination history.</p>
    <?php render_flash_message(); ?>
    <form method="get" action="records.php">
        <label for="record-search">Search by livestock ID, vaccine, or status</label>
        <input id="record-search" type="search" name="search" value="<?php echo escape_html($search); ?>">
        <button type="submit">Search</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Record ID</th>
                <th>Livestock ID</th>
                <th>Vaccine Name</th>
                <th>Vaccination Date</th>
                <th>Dosage</th>
                <th>Veterinarian</th>
                <th>Next Vaccination</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($records) === 0): ?>
            <tr><td colspan="8">No vaccination records found.</td></tr>
        <?php else: ?>
            <?php while ($record = mysqli_fetch_assoc($records)): ?>
                <tr>
                    <td><?php echo escape_html($record['record_id']); ?></td>
                    <td><?php echo escape_html($record['livestock_id'] ?? ''); ?></td>
                    <td><?php echo escape_html($record['vaccine_name']); ?></td>
                    <td><?php echo escape_html($record['vaccination_date']); ?></td>
                    <td><?php echo escape_html($record['dosage']); ?></td>
                    <td><?php echo escape_html($record['veterinarian_name']); ?></td>
                    <td><?php echo escape_html($record['next_vaccination_date'] ?? 'Not set'); ?></td>
                    <td><?php echo escape_html($record['vaccination_status']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</div>
<?php
mysqli_stmt_close($statement);
include '../includes/footer.php';
?>
