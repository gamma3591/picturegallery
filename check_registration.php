<?php

$page_title = "Check registration";

include 'mysqli_connect.php';

include 'includes/header.html';

include 'includes/navbar.html';

if (isset($_SESSION['username'])) {
    header('location: index.php');
} else {
    
    $username = $_POST['username'];
    $password = $_POST['pass'];

    $stmt1 = mysqli_prepare($connection,
        "SELECT users_username, users_password 
         FROM users 
         WHERE users_username = ? AND users_password = ?"
    );
    mysqli_stmt_bind_param($stmt1, "ss", $username, $password);
    mysqli_stmt_execute($stmt1);
    $result1 = mysqli_stmt_get_result($stmt1);

    if (mysqli_num_rows($result1) == 0) {

        $stmt2 = mysqli_prepare($connection,
            "INSERT INTO users (users_username, users_password) 
             VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt2, "ss", $username, $password);

        if (mysqli_stmt_execute($stmt2)) {
            include 'includes/new_registration.php';
        } else {
            include 'includes/error.php';
        }

    } else {
        include 'includes/notregistered.php';
    }
}


mysqli_close ($connection);

include 'includes/footer.html';

?>