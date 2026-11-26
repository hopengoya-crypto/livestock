<?php
$page_title = 'Livestock Vaccination Reminder System';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
include 'includes/header.php';
?>
<div class="container">
    <div class="form-box" id="register-form">
        <form action="process-register.php" method="post">
            <h2>Register</h2>
            <?php render_flash_message(); ?>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" maxlength="50" placeholder="email" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" minlength="8" placeholder="password" required>
            <label for="confirm-password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm-password" minlength="8" placeholder="confirm password" required>
            <label for="first-name">First name</label>
            <input type="text" id="first-name" placeholder="first name" name="first_name" maxlength="100" required>
            <label for="last-name">Last name</label>
            <input type="text" id="last-name" placeholder="last name" name="last_name" maxlength="100" required>
            <label for="national-id">National ID number</label>
            <input type="text" id="national-id" placeholder="national ID number" name="national_id" maxlength="30" required>
            <label for="phone">Phone number</label>
            <input type="tel" id="phone" name="phone"
            pattern="(07|01)[0-9]{8}"
            title="Ten digits, starting with 07 or 01"
            placeholder="07XXXXXXXX or 01XXXXXXXX"
            required>
            <fieldset>
            <legend>Your role</legend>
            <label for="role">Role</label>
            <select id="role" name="role" required>
            <option value="">Choose a role</option>
            <option value="farmer">Farmer</option>
            <option value="veterinarian">Veterinarian</option>
            </select>
            </fieldset>
            <div id="farmer-registration-fields">
                <label for="location">Farmer location</label>
                <input type="text" id="location" name="location" maxlength="25">
            </div>
            <div id="veterinarian-registration-fields">
                <label for="registration-number">Veterinarian registration number</label>
                <input type="text" id="registration-number" name="registration_number" maxlength="30">
            </div>
            <label for="rules">
            <input type="checkbox" id="rules" name="rules" required>
            I accept the system's rules
            </label>
            <button type="submit" name="register">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>