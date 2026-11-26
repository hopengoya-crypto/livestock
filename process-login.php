<?php
    // post, because the form was sent with method="post"
    $page_title = 'Application received';
    include 'includes/header.php';
    include 'includes/db.php';
    if (isset($_POST['login'])) {
     $email = $_POST['email'];
     $password = $_POST['password'];
$sql = "SELECT * FROM user WHERE email_address = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password_hash'])) {

            }

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'Farmer') {

                header("Location: farmer/dashboard.php");
                exit();

            } 
            elseif ($user['role'] == 'Veterinarian') {

                header("Location: veterinarian/dashboard.php");
                exit();

            } 
            elseif ($user['role'] == 'Administrator') {

                header("Location: admin/dashboard.php");
                exit();

            } 
            else {
                echo "Invalid user role.";
            }

        } else {
            echo "Incorrect password.";
        }

    } else {
        echo "User not found.";
    }

?>