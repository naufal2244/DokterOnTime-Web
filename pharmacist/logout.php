<?php
session_start(); // Memulai sesi

// Menghapus variabel sesi satu per satu
unset($_SESSION['ApotekerRoleID']);
unset($_SESSION['ApotekerRoleEmail']);
unset($_SESSION['ApotekerRoleLoggedIn']);

// Menghancurkan sesi
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
