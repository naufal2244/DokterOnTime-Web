<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$contentdata = file_get_contents("php://input");
$getdata = json_decode($contentdata);

$patient_id = $getdata->patientID;

$query = "
    SELECT 
        jt.id_janji_temu,
        jt.tanggal_janji,
        d.doctor_firstname,
        d.doctor_lastname,
        ds.speciality_name,
        s.session_start,
        s.session_end,
        c.clinic_name,
        rm.id_riwayat_medis,
        MIN(op.status_pembuatan) AS status_pembuatan
    FROM 
        obat_pasien op
    JOIN 
        riwayat_medis rm ON op.id_riwayat_medis = rm.id_riwayat_medis
    JOIN 
        janji_temu jt ON rm.id_janji_temu = jt.id_janji_temu
    JOIN 
        doctors d ON jt.doctor_id = d.doctor_id
    JOIN 
        speciality ds ON d.doctor_speciality = ds.speciality_id
    JOIN 
        sessions s ON jt.session_id = s.session_id
    JOIN 
        clinics c ON d.clinic_id = c.clinic_id
    WHERE 
        jt.patient_id = '$patient_id' AND
        jt.tanggal_janji >= CURDATE() AND
        op.status_ambil = 0 -- Tambahkan kondisi ini untuk mengecualikan obat yang sudah diambil
    GROUP BY 
        rm.id_riwayat_medis
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
