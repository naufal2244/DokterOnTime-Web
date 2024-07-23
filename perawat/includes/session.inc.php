<?php

if ($_SESSION["PerawatRoleLoggedIn"] != 1) {
    header("Location: login.php");
    exit();
}

$sess_email = $_SESSION["PerawatRoleEmail"];

$stmt = $conn->prepare("SELECT * FROM perawat WHERE alamat_email = ?");
$stmt->bind_param("s", $sess_email);
$stmt->execute();
$perawat_result = $stmt->get_result();
$perawat_row = $perawat_result->fetch_assoc();

if (!$perawat_row) {
    die('Email perawat tidak valid.');
}