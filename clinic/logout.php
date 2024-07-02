<?php
session_start(); // Memulai sesi

if (isset($_SESSION)) {
    session_unset(); // Menghapus semua variabel sesi
    session_destroy(); // Menghancurkan sesi
}

header("Location: login.php"); // Mengarahkan ke halaman login
exit();
?>
