<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Event Management System</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-page">

<div class="auth-box">
    <p class="system-name">Event Management System</p>
    <h2>Create Account</h2>
    <p class="small-text">Register as a student to join available events.</p>

    <?php include 'notification.php'; ?>

    <form method="POST" action="auth.php">
        <div class="field">
            <i class="bx bx-user field-icon"></i>
            <input type="text" name="fullname" placeholder="Full name" required>
        </div>

        <div class="field">
            <i class="bx bx-envelope field-icon"></i>
            <input type="email" name="email" placeholder="Email address" required>
        </div>

        <div class="field password-field">
            <i class="bx bx-lock-alt field-icon"></i>
            <input type="password" id="register_password" name="password" placeholder="Password" required>

            <button type="button" class="password-toggle" onclick="togglePassword('register_password', this)">
                <i class="bx bx-show"></i>
            </button>
        </div>

        <div class="field password-field">
            <i class="bx bx-lock-alt field-icon"></i>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>

            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password', this)">
                <i class="bx bx-show"></i>
            </button>
        </div>

        <button name="register">
            <i class="bx bx-user-plus"></i>
            Register
        </button>
    </form>

    <p class="bottom-link">
        Already have an account?
        <a href="login.php">Login</a>
    </p>
</div>

<script src="script.js"></script>
</body>
</html>
