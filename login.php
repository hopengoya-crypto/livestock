<?php
$page_title = 'Livestock Vaccination Reminder System';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
include 'includes/header.php';
?>
<div class="container">
    <div class="form-box" id="login form">
        <form action="process-login.php" method="post">
            <h2>login</h2>
            <?php render_flash_message(); ?>
             <label for="email">Email</label>
            <input type="email" name="email" maxlength="50" placeholder="email" required>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="password" required>
            <span onclick="togglePassword()">
            </span>
            <label for="forgot-password">Forgot Password?</label>
            <button type="submit" name="login">login</button>
            <p>Please login to access your account.</p>
            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>