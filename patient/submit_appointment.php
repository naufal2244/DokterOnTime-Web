<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include("../config/database.php");

$data = json_decode(file_get_contents("php://input"));

$doctorId = $data->doctor_id;
$patientId = $data->patient_id;
$sessionId = $data->session_id;
$tanggalJanji = $data->tanggal_janji;
$statusPeriksa = 0; // Default status: belum periksa
$keluhan = $data->keluhan;
$deskripsiKeluhan = $data->deskripsi_keluhan;
$namaLengkap = $data->nama_lengkap;
$email = $data->email;
$noHp = $data->no_hp;
$tinggiBadan = $data->tinggi_badan;
$beratBadan = $data->berat_badan;
$tanggalLahir = $data->tanggal_lahir;

// Validasi data yang diperlukan
if (!isset($doctorId) || !isset($patientId) || !isset($sessionId) || !isset($tanggalJanji)) {
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit();
}

// Dapatkan kode dokter
$queryKodeDokter = "SELECT kode_dokter FROM doctors WHERE doctor_id = $doctorId";
$resultKodeDokter = mysqli_query($conn, $queryKodeDokter);
$kodeDokter = mysqli_fetch_assoc($resultKodeDokter)['kode_dokter'];

// Nomor antrian akan berdasarkan session_id langsung
$nomorAntrian = sprintf("%s-%s-%03d", $kodeDokter, date('dm', strtotime($tanggalJanji)), $sessionId);

// Simpan data janji temu ke database
$queryInsert = "INSERT INTO janji_temu (patient_id, doctor_id, session_id, nomor_antrian, status_periksa, keluhan, deskripsi_keluhan, tanggal_janji, nama_lengkap, email, no_hp, tinggi_badan, berat_badan, tanggal_lahir) VALUES ('$patientId', '$doctorId', '$sessionId', '$nomorAntrian', '$statusPeriksa', '$keluhan', '$deskripsiKeluhan', '$tanggalJanji', '$namaLengkap', '$email', '$noHp', '$tinggiBadan', '$beratBadan', '$tanggalLahir')";

if (mysqli_query($conn, $queryInsert)) {
    $appointmentId = mysqli_insert_id($conn); // Dapatkan ID janji temu yang baru saja dibuat

      // Pastikan data janji temu telah tersimpan sebelum mengirimkan response
      sleep(1); // Tambahkan delay untuk memastikan data telah tersimpan

    // Simpan data riwayat penyakit pasien
    foreach ($data->selectedPenyakit as $penyakit) {
        $penyakitQuery = "INSERT INTO riwayat_penyakit_pasien (id_janji_temu, id_penyakit) VALUES ('$appointmentId', '{$penyakit->id}')";
        mysqli_query($conn, $penyakitQuery);
    }
    
    // Simpan data riwayat penyakit ortu
    foreach ($data->selectedOrtu as $ortu) {
        $ortuQuery = "INSERT INTO riwayat_ortu_pasien (id_janji_temu, id_penyakit) VALUES ('$appointmentId', '{$ortu->id}')";
        mysqli_query($conn, $ortuQuery);
    }

    // Simpan data riwayat alergi
    foreach ($data->selectedAlergi as $alergi) {
        $alergiQuery = "INSERT INTO riwayat_alergi_pasien (id_janji_temu, id_alergi) VALUES ('$appointmentId', '{$alergi->id}')";
        mysqli_query($conn, $alergiQuery);
    }

    // Simpan data riwayat operasi
    foreach ($data->selectedOperasi as $operasi) {
        $operasiQuery = "INSERT INTO riwayat_operasi_pasien (id_janji_temu, id_operasi) VALUES ('$appointmentId', '{$operasi->id}')";
        mysqli_query($conn, $operasiQuery);
    }

    // Simpan data riwayat pengobatan besar
    foreach ($data->selectedPengobatan as $pengobatan) {
        $pengobatanQuery = "INSERT INTO riwayat_pengobatan_besar_pasien (id_janji_temu, id_pengobatan_besar) VALUES ('$appointmentId', '{$pengobatan->id}')";
        mysqli_query($conn, $pengobatanQuery);
    }

  
    echo json_encode(["message" => "Janji temu berhasil dibuat", "appointmentId" => $appointmentId, "nomor_antrian" => $nomorAntrian]);
} else {
    echo json_encode(["message" => "Gagal membuat janji temu"]);
}

mysqli_close($conn);
?>
