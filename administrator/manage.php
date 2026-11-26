<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('administrator');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$settingOptions = [
    'system_name' => ['Management System', 'System Name'],
    'user_registration' => ['Enabled', 'User Registration'],
    'email_notifications' => ['Enabled', 'Email Notifications'],
    'maintenance_mode' => ['Off', 'System Maintenance'],
    'backup_frequency' => ['Daily', 'Backup Frequency'],
];
$allowedValues = [
    'system_name' => null,
    'user_registration' => ['Enabled', 'Disabled'],
    'email_notifications' => ['Enabled', 'Disabled'],
    'maintenance_mode' => ['Off', 'On'],
    'backup_frequency' => ['Daily', 'Weekly', 'Monthly'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [];
    foreach ($settingOptions as $key => $setting) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
        if ($values[$key] === '' || strlen($values[$key]) > 150
            || ($allowedValues[$key] !== null && !in_array($values[$key], $allowedValues[$key], true))) {
            set_flash('error', 'Please provide valid system settings.');
            header('Location: manage.php');
            exit;
        }
    }

    try {
        $conn->begin_transaction();
        $find = mysqli_prepare($conn, 'SELECT setting_id FROM system_settings WHERE setting_name = ? ORDER BY setting_id LIMIT 1');
        $update = mysqli_prepare(
            $conn,
            'UPDATE system_settings SET setting_value = ?, updated_time = NOW() WHERE setting_name = ?'
        );
        $insert = mysqli_prepare(
            $conn,
            'INSERT INTO system_settings (setting_name, setting_value, updated_time) VALUES (?, ?, NOW())'
        );

        foreach ($values as $key => $value) {
            mysqli_stmt_bind_param($find, 's', $key);
            mysqli_stmt_execute($find);
            mysqli_stmt_store_result($find);
            $exists = mysqli_stmt_num_rows($find) > 0;
            mysqli_stmt_free_result($find);

            if ($exists) {
                mysqli_stmt_bind_param($update, 'ss', $value, $key);
                mysqli_stmt_execute($update);
            } else {
                mysqli_stmt_bind_param($insert, 'ss', $key, $value);
                mysqli_stmt_execute($insert);
            }
        }
        mysqli_stmt_close($find);
        mysqli_stmt_close($update);
        mysqli_stmt_close($insert);
        $conn->commit();
        set_flash('success', 'System settings were saved.');
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        error_log('System settings save failed: ' . $exception->getMessage());
        set_flash('error', 'System settings could not be saved.');
    }

    header('Location: manage.php');
    exit;
}

$settingsResult = mysqli_query(
    $conn,
    'SELECT setting_name, setting_value FROM system_settings ORDER BY updated_time DESC, setting_id DESC'
);
$settings = [];
while ($row = mysqli_fetch_assoc($settingsResult)) {
    if (!array_key_exists($row['setting_name'], $settings)) {
        $settings[$row['setting_name']] = $row['setting_value'];
    }
}

$page_title = 'System Settings Management';
include '../includes/header.php';
?>
<div class="layout">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="container">
        <section>
            <h2>System Monitoring</h2>
            <h3>System Status</h3>
            <p>Server Status: <strong>Online</strong></p>
            <p>Database Status: <strong>Connected</strong></p>
            <p>System Performance: <strong>Normal</strong></p>
            <h3>System Activity Monitoring</h3>
            <p>Activity history is not available in the current database schema.</p>
        </section>
        <section>
            <h2>System Settings Management</h2>
            <?php render_flash_message(); ?>
            <form action="manage.php" method="post">
                <label for="sysName">System Name</label>
                <input type="text" id="sysName" name="system_name" maxlength="150"
                       value="<?php echo escape_html($settings['system_name'] ?? $settingOptions['system_name'][0]); ?>" required>
                <label for="registration">User Registration</label>
                <select id="registration" name="user_registration">
                    <?php foreach ($allowedValues['user_registration'] as $value): ?>
                        <option value="<?php echo escape_html($value); ?>"
                            <?php echo (($settings['user_registration'] ?? 'Enabled') === $value) ? 'selected' : ''; ?>>
                            <?php echo escape_html($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="emailSettings">Email Notifications</label>
                <select id="emailSettings" name="email_notifications">
                    <?php foreach ($allowedValues['email_notifications'] as $value): ?>
                        <option value="<?php echo escape_html($value); ?>"
                            <?php echo (($settings['email_notifications'] ?? 'Enabled') === $value) ? 'selected' : ''; ?>>
                            <?php echo escape_html($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="maintenanceMode">System Maintenance</label>
                <select id="maintenanceMode" name="maintenance_mode">
                    <?php foreach ($allowedValues['maintenance_mode'] as $value): ?>
                        <option value="<?php echo escape_html($value); ?>"
                            <?php echo (($settings['maintenance_mode'] ?? 'Off') === $value) ? 'selected' : ''; ?>>
                            <?php echo escape_html($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="backup">Backup Frequency</label>
                <select id="backup" name="backup_frequency">
                    <?php foreach ($allowedValues['backup_frequency'] as $value): ?>
                        <option value="<?php echo escape_html($value); ?>"
                            <?php echo (($settings['backup_frequency'] ?? 'Daily') === $value) ? 'selected' : ''; ?>>
                            <?php echo escape_html($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Update Settings</button>
            </form>
        </section>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
