<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$id_janji_temu = $_GET['appointmentId'];

$query = "
    SELECT 
        jt.tanggal_janji,
        c.clinic_name,
        d.doctor_firstname,
        d.doctor_lastname,
        s.speciality_name AS doctor_speciality,
        rm.saran_dokter,
        dgn.nama_diagnosis,
        tl.deskripsi_tindak_lanjut,
        ob.nama_obat,
        dos.deskripsi_dosis,
        fre.deskripsi_frekuensi
    FROM 
        janji_temu jt
    LEFT JOIN 
        doctors d ON jt.doctor_id = d.doctor_id
    LEFT JOIN 
        clinics c ON d.clinic_id = c.clinic_id
    LEFT JOIN 
        speciality s ON d.doctor_speciality = s.speciality_id
    LEFT JOIN
        riwayat_medis rm ON jt.id_janji_temu = rm.id_janji_temu
    LEFT JOIN
        diagnosis_pasien d_pas ON rm.id_riwayat_medis = d_pas.id_riwayat_medis
    LEFT JOIN
        diagnosis dgn ON d_pas.diagnosis_id = dgn.id_diagnosis
    LEFT JOIN
        tindak_lanjut_pasien tlp ON rm.id_riwayat_medis = tlp.id_riwayat_medis
    LEFT JOIN
        tindak_lanjut tl ON tlp.id_tindak_lanjut = tl.id_tindak_lanjut
    LEFT JOIN
        obat_pasien obp ON rm.id_riwayat_medis = obp.id_riwayat_medis
    LEFT JOIN
        obat ob ON obp.id_obat = ob.id_obat
    LEFT JOIN
        dosis dos ON obp.id_dosis = dos.id_dosis
    LEFT JOIN
        frekuensi fre ON obp.id_frekuensi = fre.id_frekuensi
    WHERE 
        jt.id_janji_temu = '$id_janji_temu'
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
