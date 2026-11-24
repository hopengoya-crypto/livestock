<?php
$page_title = 'Livestock Vaccination Reminder System';
include 'includes/header.php';
?>
<div class="container">
    <div class="form-box" id="register form">
        <form action="">
            <h3>Register livestock</h3>
            <legend>select type</legend>
            <label for="species">species</label>
            <select id="species" name="species" required>
            <option value="">Choose a type</option>
            <option value="cattle">cattle</option>
            <option value="goats">goats</option>
            <option value="sheep">sheep</option>
            <option value="poultry">poultry</option>
            </select>
            <label for="breed">Breed</label>
            <input type="text" id="breed" name="breed" required>
            <label for="D.O.B">Date of Birth</label>
            <input type="date" id="D.O.B" name="date_of_birth" required>
            <label for="status">Status</label>
            <input type="text" id="status" name="status" required>
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
            <option value="">Choose a gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            </select>
            <label for="age">Age</label>
            <input type="number" id="age" name="age" required>
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>