<?php
$page_title = 'Livestock Vaccination Reminder System';
include 'includes/header.php';
?>
<div class="container">
    <div class="form-box" id="register form">
        <form action="process-register.php" method="post">
            <h2>Register</h2>
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="email" required>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="password" required>
            <label for="confirm-password">Confirm Password</label>
            <input type="password" name="confirm_password" placeholder="confirm password" required>
            <label for="first-name">First name</label>
            <input type="text" id="first-name" name="first_name" required>
            <label for="last-name">Last name</label>
            <input type="text" id="last-name" name="last_name" required>
            <label for="national-id">National ID number</label>
            <input type="text" id="national-id" name="national_id" required>
            <label for="phone">Phone number</label>
            <input type="tel" id="phone" name="phone"
            pattern="07[0-9]{8}"
            title="Ten digits, starting 07"
            placeholder="07XXXXXXXX"
            required>
            </fieldset>
            <fieldset>
            <legend>Your role</legend>
            <label for="role">Role</label>
            <select id="role" name="role" required>
            <option value="">Choose a role</option>
            <option value="farmer">Farmer</option>
            <option value="veterinarian">Veterinarian</option>
            <option value="admin">Administrator</option>
            </select>
            </fieldset>
            </fieldset><label for="rules">
            <input type="checkbox" id="rules" name="rules" required>
            I accept the system's rules
            </label>
            <button type="submit" name="register">Register</button>
            <p>Already have an account? <a href="login.html">Login here</a>.</p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>