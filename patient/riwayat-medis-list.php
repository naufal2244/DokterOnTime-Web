<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$contentdata = file_get_contents("php://input");
$getdata = json_decode($contentdata);

$id = $getdata->patientID;
$status_periksa = $getdata->status_periksa;

$query = "
    SELECT 
        jt.id_janji_temu,
        jt.tanggal_janji,
        d.doctor_firstname,
        d.doctor_lastname,
        ds.speciality_name,
        c.clinic_name
    FROM 
        janji_temu jt
    JOIN 
        doctors d ON jt.doctor_id = d.doctor_id
    JOIN 
        speciality ds ON d.doctor_speciality = ds.speciality_id
    JOIN 
        clinics c ON d.clinic_id = c.clinic_id
    WHERE 
        jt.patient_id = '$id' AND jt.status_periksa = '$status_periksa'
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
