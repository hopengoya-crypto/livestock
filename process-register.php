<?php
// post, because ... (you finish this sentence in Step 5)
$page_title = 'Application received';
include 'includes/header.php';
if (isset($_POST['first_name'])) {
// the form was sent: read each value by its name
$first = $_POST['first_name'];
$last = $_POST['last_name'];
$phone = $_POST['phone'];
$role = $_POST['role'];
$email = $_POST['email'];
echo '<h2>Thank you, ' . $first . ' ' . $last . '</h2>';
echo '<p>You have been registered as a ' . $role . '.</p>';
} else {
// nothing was sent: the page was opened directly
echo '<h2>No application</h2>';
echo '<p>This page answers the registration form. ';
echo '<a href="register.php">Fill it in first.</a></p>';
}
echo '</main>';
include 'includes/footer.php';
?>