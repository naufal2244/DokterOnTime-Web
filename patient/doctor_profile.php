<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$contentdata = file_get_contents("php://input");
$getdata = json_decode($contentdata);

$id = $getdata->doctorID;

// Update query to join with the clinics table
$query = "
    SELECT 
        doctors.*, 
        speciality.speciality_name, 
        clinics.clinic_name 
    FROM doctors 
    INNER JOIN speciality ON speciality.speciality_id = doctors.doctor_speciality 
    INNER JOIN clinics ON clinics.clinic_id = doctors.clinic_id 
    WHERE doctors.doctor_id = '$id'
";

$result = mysqli_query($conn, $query);

$numrow = mysqli_num_rows($result);

if($numrow > 0) {
    $arr = array();
    while($row = mysqli_fetch_assoc($result)) {
        $arr[] = $row;
    }

    echo json_encode($arr);
    mysqli_close($conn);
} else {
    echo json_encode(null);
}
	