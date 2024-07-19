<?php
session_start(); // Memulai sesi

// Menghapus variabel sesi satu per satu
unset($_SESSION['PerawatRoleID']);
unset($_SESSION['PerawatRoleEmail']);
unset($_SESSION['PerawatRoleLoggedIn']);

// Menghancurkan sesi
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();

