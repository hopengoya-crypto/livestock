<?php
$page_title = 'Livestock Vaccination Reminder System';
include 'includes/header.php';
?>
<main class="form-page">
    <h1>Vaccination Schedules</h1>
    <a href="veterinarian-dashboard.html">Back to Dashboard</a>
    <section class="content-panel">
        <h2>Create / Confirm Vaccination Schedule</h2>
        <form action="#" method="post">
            <label for="farmer">Farmer ID</label>
            <input type="text" id="farmer" name="farmer_id" required>
            <label for="livestock">Livestock ID</label>
            <input type="text" id="livestock" name="livestock_id" required>
            <label for="vaccine">Vaccine</label>
            <select id="vaccine" name="vaccine_id" required>
                <option value="">Select vaccine</option>
                <option value="newcastle">Newcastle Disease</option>
                <option value="fmd">Foot and Mouth Disease</option>
                <option value="anthrax">Anthrax</option>
                <option value="other">Other</option>
            </select>
            <label for="date">Scheduled Date</label>
            <input type="date" id="date" name="scheduled_date" required>
            <label for="time">Scheduled Time</label>
            <input type="time" id="time" name="scheduled_time">
            <label for="status">Schedule Status</label>
            <select id="status" name="status">
                <option value="scheduled">Scheduled</option>
                <option value="postponed">Postponed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes"></textarea>
            <button type="submit">Save Schedule</button>
        </form>
    </section>
    <section class="content-panel">
        <h2>Scheduled Vaccinations</h2>
        <table>
            <thead>
                <tr>
                    <th>Schedule ID</th>
                    <th>Livestock ID</th>
                    <th>Vaccine</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5">No records available yet.</td>
                </tr>
            </tbody>
        </table>
    </section>
</main>
<?php include 'includes/footer.php'; ?>