<?php

if ($_SESSION["ApotekerRoleLoggedIn"] != 1) {
    header("Location: login.php");
    exit();
}

$sess_email = $_SESSION["ApotekerRoleEmail"];

$stmt = $conn->prepare("SELECT * FROM apoteker WHERE apoteker_email = ?");
$stmt->bind_param("s", $sess_email);
$stmt->execute();
$apoteker_result = $stmt->get_result();
$apoteker_row = $apoteker_result->fetch_assoc();

if (!$apoteker_row) {
    die('Email apoteker tidak valid.');
}


