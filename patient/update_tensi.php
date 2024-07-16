<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include("../config/database.php");

$data = json_decode(file_get_contents("php://input"));

$appointmentId = $data->appointmentId;
$tensi = $data->tensi;

if (!isset($appointmentId) || !isset($tensi)) {
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit();
}

$queryUpdate = "UPDATE janji_temu SET tensi = '$tensi' WHERE id_janji_temu = '$appointmentId'";

if (mysqli_query($conn, $queryUpdate)) {
    echo json_encode(["message" => "Data tensi berhasil diupdate"]);
} else {
    echo json_encode(["message" => "Gagal mengupdate data tensi"]);
}

mysqli_close($conn);
?>
