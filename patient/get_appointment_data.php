<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$response = array();

try {
    $appointmentId = $_GET['appointmentId'];

    // Validasi appointmentId
    if (!isset($appointmentId)) {
        throw new Exception("Appointment ID is required");
    }

    // Ambil data janji temu
    $query = "
        SELECT 
            jt.id_janji_temu,
            jt.patient_id,
            jt.doctor_id,
            jt.session_id,
            jt.nomor_antrian,
            jt.tanggal_janji,
            jt.status_periksa,
            d.clinic_id,
            d.doctor_firstname,
            d.doctor_lastname,
            ds.speciality_name AS doctor_speciality,
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
            jt.id_janji_temu = $appointmentId
    ";

    $result = $conn->query($query);
    if ($result) {
        $response = $result->fetch_assoc();
        
        // Ambil pasien yang dilayani berdasarkan doctor_id dan tanggal_janji
        $doctorId = $response['doctor_id'];
        $tanggalJanji = $response['tanggal_janji'];

        $queryPasienDilayani = "
            SELECT 
                jt.nomor_antrian
            FROM 
                janji_temu jt
            WHERE 
                jt.doctor_id = $doctorId AND 
                jt.tanggal_janji = '$tanggalJanji' AND 
                jt.status_periksa = 1
        ";
        
        $resultPasienDilayani = $conn->query($queryPasienDilayani);
        $pasienDilayani = [];
        
        while ($row = $resultPasienDilayani->fetch_assoc()) {
            $pasienDilayani[] = $row['nomor_antrian'];
        }

        $response['pasien_dilayani'] = $pasienDilayani;

        // Tambahkan query untuk nomor antrian sebelumnya
        $currentAntrianParts = explode('-', $response['nomor_antrian']);
        $previousSessionId = $currentAntrianParts[2] - 1;

        if ($previousSessionId > 0) {
            $queryPreviousSession = "
                SELECT 
                    session_start,
                    session_end
                FROM 
                    sessions
                WHERE 
                    session_id = $previousSessionId
            ";

            $resultPreviousSession = $conn->query($queryPreviousSession);
            if ($resultPreviousSession) {
                $previousSession = $resultPreviousSession->fetch_assoc();
                $response['previous_session_start'] = $previousSession['session_start'];
                $response['previous_session_end'] = $previousSession['session_end'];
            }
        } else {
            $response['previous_session_start'] = '-';
            $response['previous_session_end'] = '-';
        }

        echo json_encode($response);
    } else {
        throw new Exception("Error fetching appointment data");
    }

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
