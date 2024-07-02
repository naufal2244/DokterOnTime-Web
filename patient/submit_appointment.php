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
$queryInsert = "INSERT INTO janji_temu (patient_id, doctor_id, session_id, nomor_antrian, status_periksa, keluhan, deskripsi_keluhan, tanggal_janji, nama_lengkap, email, no_hp) VALUES ('$patientId', '$doctorId', '$sessionId', '$nomorAntrian', '$statusPeriksa', '$keluhan', '$deskripsiKeluhan', '$tanggalJanji', '$namaLengkap', '$email', '$noHp')";

if (mysqli_query($conn, $queryInsert)) {
    echo json_encode(["message" => "Janji temu berhasil dibuat", "nomor_antrian" => $nomorAntrian]);
} else {
    echo json_encode(["message" => "Gagal membuat janji temu"]);
}

mysqli_close($conn);
?>
