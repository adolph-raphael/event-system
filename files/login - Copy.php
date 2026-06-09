<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Event Management System</title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth-page">

<div class="auth-box">
    <p class="system-name">Event Management System</p>
    <h2>Login</h2>
    <p class="small-text">Access your account to manage or register for events.</p>

    <?php include 'notification.php'; ?>

    <form method="POST" action="auth.php">
        <div class="field">
            <i class="bx bx-envelope field-icon"></i>
            <input type="email" name="email" placeholder="Email address" required>
        </div>

        <div class="field password-field">
            <i class="bx bx-lock-alt field-icon"></i>
            <input type="password" id="login_password" name="password" placeholder="Password" required>

            <button type="button" class="password-toggle" onclick="togglePassword('login_password', this)">
                <i class="bx bx-show"></i>
            </button>
        </div>

        <button name="login">
            <i class="bx bx-log-in"></i>
            Login
        </button>
    </form>

    <p class="bottom-link">
        Don't have an account?
        <a href="register.php">Create account</a>
    </p>
</div>

<script src="script.js"></script>
</body>
</html>
