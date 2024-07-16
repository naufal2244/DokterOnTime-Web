<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$response = array();

try {
    // Ambil daftar penyakit
    $penyakitQuery = "SELECT id_penyakit as id, nama FROM penyakit";
    $result = $conn->query($penyakitQuery);
    $penyakit = $result->fetch_all(MYSQLI_ASSOC);
    $response['penyakit'] = $penyakit;

    // Ambil daftar penyakit untuk ortu (gunakan query yang sama atau sesuaikan jika berbeda)
    $ortuQuery = "SELECT id_penyakit as id, nama FROM penyakit";
    $result = $conn->query($ortuQuery);
    $ortu = $result->fetch_all(MYSQLI_ASSOC);
    $response['ortu'] = $ortu;

    // Ambil daftar operasi
    $operasiQuery = "SELECT id_operasi as id, nama FROM operasi";
    $result = $conn->query($operasiQuery);
    $operasi = $result->fetch_all(MYSQLI_ASSOC);
    $response['operasi'] = $operasi;

    // Ambil daftar alergi
    $alergiQuery = "SELECT id_alergi as id, nama FROM alergi";
    $result = $conn->query($alergiQuery);
    $alergi = $result->fetch_all(MYSQLI_ASSOC);
    $response['alergi'] = $alergi;

    // Ambil daftar pengobatan besar
    $pengobatanQuery = "SELECT id_pengobatan_besar as id, nama FROM pengobatan_besar";
    $result = $conn->query($pengobatanQuery);
    $pengobatan = $result->fetch_all(MYSQLI_ASSOC);
    $response['pengobatan'] = $pengobatan;

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
