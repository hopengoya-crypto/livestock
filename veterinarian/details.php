<?php
$page_title = 'Livestock Vaccination Reminder System';
include 'includes/header.php';
?>
<main class="form-page">
    <h1>Record Vaccination Details</h1>
    <a href="veterinarian-dashboard.html">Back to Dashboard</a>

    <section class="content-panel">
        <h2>Vaccination Record</h2>

        <form action="#" method="post">

            <h3>Livestock and Schedule Information</h3>

            <label for="schedule">Vaccination Schedule ID</label>
            <input type="text" id="schedule" name="schedule_id" required>

            <label for="livestock">Livestock ID</label>
            <input type="text" id="livestock" name="livestock_id" required>

            <label for="farmer">Farmer ID</label>
            <input type="text" id="farmer" name="farmer_id">

            <label for="vaccine">Vaccine Name</label>
            <input type="text" id="vaccine" name="vaccine_name" required>

            <label for="dosage">Dosage Administered</label>
            <input type="text" id="dosage" name="dosage" placeholder="e.g. 2 ml" required>

            <label for="date">Date Administered</label>
            <input type="date" id="date" name="vaccination_date" required>

            <label for="next-date">Next Vaccination Date</label>
            <input type="date" id="next-date" name="next_vaccination_date">

            <h3>Update Vaccination Status</h3>

            <label for="status">Vaccination Status</label>
            <select id="status" name="vaccination_status" required>
                <option value="">Select status</option>
                <option value="completed">Completed</option>
                <option value="postponed">Postponed</option>
                <option value="missed">Missed</option>
            </select>

            <label for="remarks">Remarks</label>
            <textarea id="remarks" name="remarks"
                placeholder="Enter additional observations"></textarea>

            <button type="submit">
                Save Vaccination Record and Status
            </button>

        </form>
    </section>
</main>
<?php include 'includes/footer.php'; ?>