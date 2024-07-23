<?php
    session_start(); // Mulai sesi
    
    unset($_SESSION['sess_adminid']);
    unset($_SESSION['sess_adminemail']);
    unset($_SESSION['admin_loggedin']);
    
    // Atau Anda bisa menggunakan session_unset() tanpa argumen untuk menghapus semua variabel sesi
    // session_unset();
    
    // Hancurkan sesi jika diperlukan
    // session_destroy();
    
    header("Location: login.php");
    exit(); // Tambahkan exit() setelah header untuk memastikan bahwa skrip berhenti
?>
