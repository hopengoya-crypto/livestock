    <?php
    // post, because the form was sent with method="post"
    $page_title = 'Application received';
    include 'includes/header.php';
    include 'includes/db.php';
    if (isset($_POST['first_name'])) {
    // the form was sent: read each value by its name
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $full_name = $first . ' ' . $last;
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = password_hash($_POST[ 'password'], PASSWORD_DEFAULT);

    // insert the values into the database
    $sql = "INSERT INTO user (full_name, phone_no, email_address, password_hash, role) VALUES ('$full_name', '$phone', '$email', '$password', '$role')";
    if (mysqli_query($conn, $sql)) {
        echo '<p>Account created successfully 🎉</p>';
    } else {
        echo '<p>Error: ' . $sql . '<br>' . mysqli_error($conn) . '</p>';
    }
    
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