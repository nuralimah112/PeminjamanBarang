<?php
require_once('database.php');
session_start();

if (isset($_POST['masuk'])) {
    // Handle login
    $login_result = cek_login($_POST['username'], $_POST['password']);

    if ($login_result) {
        $_SESSION['username'] = $login_result['username'];
        $_SESSION['status'] = "login";
        $_SESSION['role'] = $login_result['role'];

        $user_data = get_user_data($_SESSION['username']);

        if ($user_data) {
            $_SESSION['user_data'] = $user_data;
        } else {
            echo "Error: User data retrieval failed.";
            exit();
        }

        if ($_SESSION['role'] == "admin") {
            header("location: admin.php");
        } else {
            header("location: index.php");
        }
    } else {
        header("location: login.php?msg=gagal");
    }
} elseif (isset($_POST['signup'])) {
    // Handle signup
    $nis = $_POST['nis'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $nama = $_POST['nama'];
    $inputdata = "INSERT INTO users (nis, username, password, nama) VALUES ('$nis', '$username', '$password', '$nama')";

    if (inputdata($inputdata)) {
        $_SESSION['username'] = $username;
        $_SESSION['status'] = "login";
        header("location: login.php");
        exit();
    } else {
        header("location: signup.php?msg=gagal");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/loginpage.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<style>
    body {
        display: grid;
        height: 100%;
        width: 100%;
        place-items: center;
        background-image: url("image/kuromi.jpg");
        background-size: 25%;
        background-position: center;
        background-repeat: repeat;
    }
</style>
<body>
<div class="wrapper">
    <div class="title-text">
        <div class="title login">
            Login
        </div>
        <div class="title signup">
            Signup
        </div>
    </div>
    <div class="form-container">
        <div class="slide-controls">
            <input type="radio" name="slide" id="login" checked>
            <input type="radio" name="slide" id="signup">
            <label for="login" class="slide login">Login</label>
            <label for="signup" class="slide signup">Signup</label>
            <div class="slider-tab"></div>
        </div>
        <div class="form-inner">
            <!-- Updated form action and method -->
            <form autocomplete="off" action="" method="POST" class="login">
                <div class="field">
                    <input type="text" name="username" placeholder="Username" required="required" />
                </div>
                <div class="field">
                    <input type="password" name="password" placeholder="Password" required="required" />
                </div>
                <div class="field btn">
                    <div class="btn-layer"></div>
                    <input type="submit" value="Login" name="masuk">
                </div>
                <div class="signup-link">
                    Don't have an account yet? <a href="">Signup now</a>
                </div>
            </form>
            <!-- Updated form action and method -->
            <form autocomplete="off" action="" method="POST" class="signup">
                <div class="field">
                    <input type="text" name="nis" placeholder="NIS" required="required" />
                </div>
                <div class="field">
                    <input type="text" name="username" placeholder="Username" required="required" />
                </div>
                <div class="field">
                    <input type="password" name="password" placeholder="Password" required="required" />
                </div>
                <div class="field btn">
                    <div class="btn-layer"></div>
                    <input type="submit" value="Signup" name="signup">
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const loginText = document.querySelector(".title-text .login");
    const loginForm = document.querySelector("form.login");
    const signupForm = document.querySelector("form.signup");
    const loginBtn = document.querySelector("label.login");
    const signupBtn = document.querySelector("label.signup");
    const signupLink = document.querySelector("form .signup-link a");

    signupBtn.onclick = () => {
        loginForm.style.marginLeft = "-50%";
        loginText.style.marginLeft = "-50%";
    };

    loginBtn.onclick = () => {
        loginForm.style.marginLeft = "0%";
        loginText.style.marginLeft = "0%";
    };

    signupLink.onclick = (event) => {
        event.preventDefault();
        signupBtn.click();
    };
</script>
</body>
</html>
