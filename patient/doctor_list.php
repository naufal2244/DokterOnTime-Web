<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$data = json_decode(file_get_contents("php://input"));

$hospitalId = $data->hospitalId;
$specialityId = $data->specialityId;

$query = "SELECT * FROM doctors WHERE clinic_id = ? AND doctor_speciality = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $hospitalId, $specialityId);
$stmt->execute();
$result = $stmt->get_result();

$num = $result->num_rows;

if ($num > 0) {
    $doctors_arr = array();
    while ($row = $result->fetch_assoc()) {
        array_push($doctors_arr, $row);
    }
    echo json_encode($doctors_arr);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
