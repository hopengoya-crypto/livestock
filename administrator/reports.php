<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('administrator');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$reportTypes = [
    'User Accounts Report',
    'System Activity Report',
    'Security Report',
    'System Performance Report',
    'Full System Report',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportType = trim((string) ($_POST['report_type'] ?? ''));
    $startDate = trim((string) ($_POST['start_date'] ?? ''));
    $endDate = trim((string) ($_POST['end_date'] ?? ''));

    if (!in_array($reportType, $reportTypes, true) || !valid_iso_date($startDate)
        || !valid_iso_date($endDate) || $startDate > $endDate) {
        set_flash('error', 'Choose a report type and a valid date range.');
    } else {
        try {
            $conn->begin_transaction();
            $settingName = 'report_request';
            $setting = mysqli_prepare(
                $conn,
                'INSERT INTO system_settings (setting_name, setting_value, updated_time) VALUES (?, ?, NOW())'
            );
            mysqli_stmt_bind_param($setting, 'ss', $settingName, $reportType);
            mysqli_stmt_execute($setting);
            $settingId = (int) mysqli_insert_id($conn);
            mysqli_stmt_close($setting);

            $status = 'requested';
            $userId = current_user_id();
            $report = mysqli_prepare(
                $conn,
                'INSERT INTO system_reports
                    (setting_id, report_type, generated_date, start_date, end_date, report_status, generated_by)
                 VALUES (?, ?, CURDATE(), ?, ?, ?, ?)'
            );
            mysqli_stmt_bind_param($report, 'issssi', $settingId, $reportType, $startDate, $endDate, $status, $userId);
            mysqli_stmt_execute($report);
            mysqli_stmt_close($report);
            $conn->commit();
            set_flash('success', 'Your report request has been saved.');
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            error_log('Report request failed: ' . $exception->getMessage());
            set_flash('error', 'The report request could not be saved.');
        }
    }

    header('Location: reports.php');
    exit;
}

$reports = mysqli_query(
    $conn,
    'SELECT r.report_id, r.report_type, r.generated_date, r.start_date, r.end_date, r.report_status,
            u.full_name AS generated_by
     FROM system_reports r
     LEFT JOIN `user` u ON u.user_id = r.generated_by
     ORDER BY r.report_id DESC'
);

$page_title = 'System Reports';
include '../includes/header.php';
?>
<div class="layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="container">
        <section>
            <h2>Request System Report</h2>
            <p>Choose the type of report and the period you want to review.</p>
            <?php render_flash_message(); ?>
            <form action="reports.php" method="post">
                <label for="reportType">Report Type</label>
                <select id="reportType" name="report_type" required>
                    <option value="">Select report type</option>
                    <?php foreach ($reportTypes as $reportType): ?>
                        <option value="<?php echo escape_html($reportType); ?>"><?php echo escape_html($reportType); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="startDate">Start Date</label>
                <input type="date" id="startDate" name="start_date" required>
                <label for="endDate">End Date</label>
                <input type="date" id="endDate" name="end_date" required>
                <button type="submit">Request Report</button>
            </form>
        </section>
        <section>
            <h2>Report History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Report Type</th>
                        <th>Date Requested</th>
                        <th>Period</th>
                        <th>Requested By</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($reports) === 0): ?>
                    <tr><td colspan="6">No report requests yet.</td></tr>
                <?php else: ?>
                    <?php while ($report = mysqli_fetch_assoc($reports)): ?>
                        <tr>
                            <td><?php echo escape_html($report['report_id']); ?></td>
                            <td><?php echo escape_html($report['report_type']); ?></td>
                            <td><?php echo escape_html($report['generated_date']); ?></td>
                            <td><?php echo escape_html(($report['start_date'] ?? '') . ' - ' . ($report['end_date'] ?? '')); ?></td>
                            <td><?php echo escape_html($report['generated_by'] ?? 'Unknown'); ?></td>
                            <td><?php echo escape_html($report['report_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
