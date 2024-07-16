<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$contentdata = file_get_contents("php://input");
$getdata = json_decode($contentdata);

$id = $getdata->patientID;

$query = "
    SELECT 
        jt.id_janji_temu,
        jt.tanggal_janji,
        jt.tensi,
        d.clinic_id,
        d.doctor_firstname,
        d.doctor_lastname,
        ds.speciality_name,
        s.session_start,
        s.session_end,
        c.clinic_name
    FROM 
        janji_temu jt
    JOIN 
        doctors d ON jt.doctor_id = d.doctor_id
    JOIN 
        speciality ds ON d.doctor_speciality = ds.speciality_id
    JOIN 
        sessions s ON jt.session_id = s.session_id
    JOIN 
        clinics c ON d.clinic_id = c.clinic_id
    WHERE 
        jt.patient_id = '$id' AND
        jt.tanggal_janji >= CURDATE()
    ORDER BY 
        jt.tanggal_janji ASC
";

$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo json_encode(null);
} else {
    $arr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $arr[] = $row;
    }

    echo json_encode($arr);
    mysqli_close($conn);
}
?>