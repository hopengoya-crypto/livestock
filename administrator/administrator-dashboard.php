<?php
$page_title = 'Livestock Vaccination Reminder System';
include 'includes/header.php';
?>
<body>
<div class="container">

    <!-- ADMINISTRATOR DASHBOARD -->
    <section>
        <h2>Administrator Dashboard</h2>
        <p>Welcome back, Administrator. Here is an overview of the system.</p>
    </section>

    <!-- SYSTEM OVERVIEW -->
    <section>
        <h2>System Overview</h2>

        <table>
            <tr>
                <th>Total Users</th>
                <th>Active Users</th>
                <th>Inactive Users</th>
                <th>System Status</th>
            </tr>

            <tr>
                <td>150</td>
                <td>125</td>
                <td>25</td>
                <td>Online</td>
            </tr>
        </table>
    </section>

    <!-- QUICK ADMIN LINKS -->
    <section>
        <h2>Administrator Functions</h2>

        <p>Manage user accounts, system settings, monitor activities and generate reports.</p>

        <button onclick="window.location.href='manage.html'">
            Manage Users & System Settings
        </button>

        <button onclick="window.location.href='reports.html'">
            View & Generate Reports
        </button>
    </section>

    <!-- RECENT ACTIVITIES -->
    <section>
        <h2>Recent System Activities</h2>

        <table>
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>User Login</td>
                    <td>Mary Wanjiku</td>
                    <td>22/11/2026</td>
                    <td>Successful</td>
                </tr>

                <tr>
                    <td>Account Updated</td>
                    <td>Administrator</td>
                    <td>22/11/2026</td>
                    <td>Completed</td>
                </tr>

                <tr>
                    <td>New User Registered</td>
                    <td>Brian Otieno</td>
                    <td>22/11/2026</td>
                    <td>Completed</td>
                </tr>

                <tr>
                    <td>System Settings Changed</td>
                    <td>Administrator</td>
                    <td>22/11/2026</td>
                    <td>Completed</td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- SYSTEM STATUS -->
    <section>
        <h2>System Status</h2>

        <p>Server Status: <strong>Online</strong></p>
        <p>Database Status: <strong>Connected</strong></p>
        <p>Backup Status: <strong>Completed</strong></p>
        <p>Security Status: <strong>Normal</strong></p>
    </section>

</div>
<?php include 'includes/footer.php'; ?>