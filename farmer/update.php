<?php
$page_title = 'Livestock Vaccination Reminder System';
include '../includes/header.php';
?>
<div class="container">
    <div class="form-box" id="register form">
        <form action="">
            <h3>Update Livestock Information</h3>
            <legend>select livestock type</legend>
            <label for="species">species</label>
            <select id="species" name="species" required>
            <option value="">Choose a type</option>
            <option value="cattle">cattle</option>
            <option value="goats">goats</option>
            <option value="sheep">sheep</option>
            <option value="poultry">poultry</option>
            </select>
            <label for="livestock-id">Livestock ID</label>
            <input type="text" id="livestock-id" name="livestock_id" required>
            <label for="health">Health Status</label>
            <input type="text" id="health" name="health" required>
            <button type="submit" name="update">update information</button>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>