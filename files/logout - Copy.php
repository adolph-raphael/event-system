<?php
session_start();
include 'db.php';

session_destroy();

go_to("login.php");
?>
