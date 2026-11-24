<?php
$page_title = 'Welcome to Ereteti';
include 'includes/header.php';
?>
<div class="container">
    <div class="form-box" id="invite-veterinarian-form">
        <form action="">
            <h3>Invite Veterinarian</h3>
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="email" required>
            <button type="submit" name="register">Send Invitation</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>