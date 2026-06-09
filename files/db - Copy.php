<?php
$conn = new mysqli("localhost","root","","event_system");

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

function clean($text){
    return htmlspecialchars($text ?? "", ENT_QUOTES, "UTF-8");
}

function go_to($page){
    header("Location: " . $page);
    exit();
}

function set_message($text, $type = "success"){
    $_SESSION['message'] = $text;
    $_SESSION['message_type'] = $type;
}
?>
