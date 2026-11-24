<?php
$page_title = 'Welcome to Ereteti';
include 'includes/header.php';
?>
<body><!-- USER ACCOUNT MANAGEMENT -->
 <section>
    <h2>Manage User Accounts</h2>

    <table>
        <tr>
            <th>User ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <tr>
            <td>001</td>
            <td>Mary Wanjiku</td>
            <td>mary@gmail.com</td>
            <td>User</td>
            <td id="status1">Active</td>
            <td><button onclick="changeStatus('status1', this)">Deactivate</button></td>
        </tr>

        <tr>
            <td>002</td>
            <td>Brian Otieno</td>
            <td>brian@gmail.com</td>
            <td>Staff</td>
            <td id="status2">Active</td>
            <td><button onclick="changeStatus('status2', this)">Deactivate</button></td>
        </tr>

        <tr>
            <td>003</td>
            <td>Faith Njeri</td>
            <td>faith@gmail.com</td>
            <td>User</td>
            <td id="status3">Inactive</td>
            <td><button onclick="changeStatus('status3', this)">Activate</button></td>
        </tr>
    </table>
</section>
<?php include 'includes/footer.php'; ?>