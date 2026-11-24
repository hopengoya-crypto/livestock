<?php
$page_title = 'Welcome to Ereteti';
include 'includes/header.php';
?>
<div class="container">
    <div class="form-box" id="login form">
        <form action="">
            <h2>login</h2>
             <label for="email">Email</label>
            <input type="email" name="email" placeholder="email" required>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="password" required>
            <span onclick="togglePassword()">
            </span>
            <label for="forgot-password">Forgot Password?</label>

            <button type="submit" name="login">login</button>
            <p>Please login to access your account.</p>
            <p>Don't have an account? <a href="register.html">Register here</a>.</p>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>