<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$id_janji_temu = $_GET['appointmentId'];

$query = "
    SELECT 
        rm.saran_dokter,
        dp.nama_diagnosis,
        tlp.deskripsi_tindak_lanjut,
        d.clinic_name,
        d.doctor_firstname,
        d.doctor_lastname,
        ds.speciality_name AS doctor_speciality
    FROM 
        riwayat_medis rm
    LEFT JOIN 
        diagnosis_pasien dp ON rm.id_riwayat_medis = dp.id_riwayat_medis
    LEFT JOIN 
        tindak_lanjut_pasien tlp ON rm.id_riwayat_medis = tlp.id_riwayat_medis
    LEFT JOIN 
        janji_temu jt ON rm.id_janji_temu = jt.id_janji_temu
    LEFT JOIN 
        doctors d ON jt.doctor_id = d.doctor_id
    LEFT JOIN 
        speciality ds ON d.doctor_speciality = ds.speciality_id
    WHERE 
        rm.id_janji_temu = '$id_janji_temu'
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
