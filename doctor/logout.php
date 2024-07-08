<?php
session_start(); // Memulai sesi

// Menghapus variabel sesi satu per satu
unset($_SESSION['DoctorRoleID']);
unset($_SESSION['DoctorRoleEmail']);
unset($_SESSION['DoctorRoleLoggedIn']);

// Menghancurkan sesi
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
