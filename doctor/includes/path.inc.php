<?php
// Klinik
$HOME_PAGE = "index.php";
$BRAND_NAME = "DokterOnTime";
$PATH = "doclab/doctor";

switch ($_SERVER["SCRIPT_NAME"]) {
    case '/' . $PATH . '/login.php':
        $CURRENT_PAGE = "Masuk";
        $CURRENT_PATH = "Masuk";
        $PAGE_TITLE = "Masuk | $BRAND_NAME";
        break;

        // Pasien
    case '/' . $PATH . '/patient-list.php':
        $CURRENT_PAGE = "Daftar Pasien";
        $CURRENT_PATH = "Daftar Pasien";
        $PAGE_TITLE = "Pasien | $BRAND_NAME";
        break;

    case '/' . $PATH . '/patient-add.php':
        $CURRENT_PAGE = "Tambah Pasien";
        $CURRENT_PATH = "Tambah Pasien";
        $PAGE_TITLE = "Pasien | $BRAND_NAME";
        break;

    case '/' . $PATH . '/patient-view.php':
        $CURRENT_PAGE = "Profil Pasien";
        $CURRENT_PATH = "Lihat Pasien";
        $PAGE_TITLE = "Pasien | $BRAND_NAME";
        break;

        // Dokter
    case '/' . $PATH . '/doctor.php':
        $CURRENT_PAGE = "Profil Dokter";
        $CURRENT_PATH = "Dokter";
        $PAGE_TITLE = "Dokter | $BRAND_NAME";
        break;
    case '/' . $PATH . '/doctor-edit.php':
        $CURRENT_PAGE = "Edit Profil";
        $CURRENT_PATH = "Dokter";
        $PAGE_TITLE = "Dokter | $BRAND_NAME";
        break;

        // Klinik
    case '/' . $PATH . '/clinic-list.php':
        $CURRENT_PAGE = "Daftar Klinik";
        $CURRENT_PATH = "";
        $PAGE_TITLE = "Klinik | $BRAND_NAME";
        break;

    case '/' . $PATH . '/clinic-add.php':
        $CURRENT_PAGE = "Tambah Klinik";
        $CURRENT_PATH = "";
        $PAGE_TITLE = "Klinik | $BRAND_NAME";
        break;

    case '/' . $PATH . '/clinic-view.php':
        $CURRENT_PAGE = "Lihat Klinik";
        $CURRENT_PATH = "";
        $PAGE_TITLE = "Klinik | $BRAND_NAME";
        break;

    case '/' . $PATH . '/clinic.php':
        $CURRENT_PAGE = "Klinik";
        $CURRENT_PATH = "";
        $PAGE_TITLE = "Klinik | $BRAND_NAME";
        break;

        // Perawat
    case '/' . $PATH . '/perawat.php':
        $CURRENT_PAGE = "Perawat";
        $CURRENT_PATH = "";
        $PAGE_TITLE = "Perawat | $BRAND_NAME";
        break;

        // Janji
    case '/' . $PATH . '/appointment.php':
        $CURRENT_PAGE = "Janji";
        $CURRENT_PATH = "";
        $PAGE_TITLE = "Janji | $BRAND_NAME";
        break;

        // Jadwal
    case '/' . $PATH . '/schedule.php':
        $CURRENT_PAGE = "Jadwal";
        $CURRENT_PATH = "Jadwal";
        $PAGE_TITLE = "Jadwal | $BRAND_NAME";
        break;

    case '/' . $PATH . '/speciality.php':
        $CURRENT_PAGE = "Spesialisasi";
        $CURRENT_PATH = "";
        $PAGE_TITLE = "Spesialisasi | $BRAND_NAME";
        break;

    case '/' . $PATH . '/language.php':
        $CURRENT_PAGE = "Bahasa";
        $CURRENT_PATH = "Bahasa";
        $PAGE_TITLE = "Bahasa | $BRAND_NAME";
        break;

    case '/' . $PATH . '/treatment.php':
        $CURRENT_PAGE = "Perawatan";
        $CURRENT_PATH = "Perawatan";
        $PAGE_TITLE = "Perawatan | $BRAND_NAME";
        break;

    case '/' . $PATH . '/report.php':
        $CURRENT_PAGE = "Laporan";
        $CURRENT_PATH = "Laporan";
        $PAGE_TITLE = "Laporan | $BRAND_NAME";
        break;

    case '/' . $PATH . '/review.php':
        $CURRENT_PAGE = "Ulasan";
        $CURRENT_PATH = "Ulasan";
        $PAGE_TITLE = "Ulasan | $BRAND_NAME";
        break;

    case '/' . $PATH . '/password.php':
        $CURRENT_PAGE = "Reset Kata Sandi";
        $CURRENT_PATH = "Reset Kata Sandi";
        $PAGE_TITLE = "Reset Kata Sandi | $BRAND_NAME";
        break;

    case '/' . $PATH . '/reset.php':
        $CURRENT_PAGE = "Reset Kata Sandi";
        $CURRENT_PATH = "Reset Kata Sandi";
        $PAGE_TITLE = "Reset Kata Sandi | $BRAND_NAME";
        break;

    case '/' . $PATH . '/activate.php':
        $CURRENT_PAGE = "Aktivasi Akun Dokter";
        $CURRENT_PATH = "Aktivasi Akun Dokter";
        $PAGE_TITLE = "Aktivasi Akun Dokter | $BRAND_NAME";
        break;

        // Halaman Utama
    default:
        $CURRENT_PAGE = "Beranda";
        $PAGE_TITLE = "Beranda | $BRAND_NAME";
        break;
}

define('NAVIGATION', 'layouts/navigate.php');
define('HEADER', 'layouts/nav_header.php');
define('WIDGET', 'layouts/widget.php');
