<?php
$page_title = 'Livestock Vaccination Reminder System';
include '../includes/header.php';
?>
<main class="form-page">
    <h1>Vaccine Details</h1>
    <a href="veterinarian-dashboard.php">Back to Dashboard</a>
    <section class="content-panel">
        <h2>Add Vaccine Information</h2>
        <div class="form-box" id="register-form">
        <form action="#" method="post">
            <label for="name">Vaccine Name</label>
            <input type="text" id="name" name="vaccine_name" required>
            <label for="disease">Disease Prevented</label>
            <input type="text" id="disease" name="disease_prevented" required>
            <label for="species">Target Species</label>
            <select id="species" name="target_species" required>
                <option value="">Select species</option>
                <option value="cattle">Cattle</option>
                <option value="goats">Goats</option>
                <option value="sheep">Sheep</option>
                <option value="poultry">Poultry</option>
                <option value="pigs">Pigs</option>
                <option value="multiple">Multiple species</option>
            </select>
            <label for="dosage">Recommended Dosage</label>
            <input type="text" id="dosage" name="recommended_dosage">
            <label for="manufacturer">Manufacturer</label>
            <input type="text" id="manufacturer" name="manufacturer">
            <label for="storage">Storage Requirements</label>
            <input type="text" id="storage" name="storage_requirements">
            <label for="notes">Additional Information</label>
            <textarea id="notes" name="notes"></textarea>
            <button type="submit">Save Vaccine Details</button>
        </form>
    </section>
</main>
<?php include 'includes/footer.php'; ?>