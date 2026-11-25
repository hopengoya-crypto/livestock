<?php
$page_title = 'Livestock Vaccination Reminder System';
include '../includes/header.php';
?>
<body>
    <div class="records-container">
        <h3>Vaccination Records</h3>
        <p>View your livestock vaccination history.</p>
        <input type="text"
               placeholder="Search by livestock ID or name">
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
                <!-- Records will be retrieved from database -->
            </tbody>
        </table>
    </div>
<?php include '../includes/footer.php'; ?>