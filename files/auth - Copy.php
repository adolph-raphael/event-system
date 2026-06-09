<?php
session_start();
include "db.php";

/* ===================== REGISTER ===================== */
if(isset($_POST['register'])){

    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if($pass !== $confirm){
        set_message("Passwords do not match.", "error");
        go_to("register.php");
    }

    // hash password
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    // default role = student
    $role = "student";

    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

    if($stmt->execute()){
        set_message("Account created successfully. You can now login.", "success");
        go_to("login.php");
    } else {
        set_message("Registration failed. Email may already exist.", "error");
        go_to("register.php");
    }
}


/* ===================== LOGIN ===================== */
if(isset($_POST['login'])){

    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows === 1){

        $user = $result->fetch_assoc();

        // verify password
        if(password_verify($pass, $user['password'])){

            $_SESSION['user'] = $user;

            // ROLE BASED REDIRECT
            if($user['role'] === "admin"){
                go_to("admin_dashboard.php");
            } else {
                go_to("user_dashboard.php");
            }

        } else {
            set_message("Wrong password. Please try again.", "error");
            go_to("login.php");
        }

    } else {
        set_message("User not found. Please check your email.", "error");
        go_to("login.php");
    }
}
?>
