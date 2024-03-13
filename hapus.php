<?php
session_start();
require_once("database.php");


if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: login.php?msg=belum_login");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_login'])) {
    $id_login = mysqli_real_escape_string($dbconnect, $_POST['id_login']);

    
    $deleteUserQuery = "DELETE FROM users WHERE id_login = '$id_login'";

    if (mysqli_query($dbconnect, $deleteUserQuery)) {
        echo "Successfully deleted user.";
    } else {
        echo "Error deleting user: " . mysqli_error($dbconnect);
    }
} else {
    echo "Invalid request.";
}
?>
