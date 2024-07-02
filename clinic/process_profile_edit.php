<?php
ob_start();
require_once('../config/autoload.php');
include('includes/session.inc.php');
include(SELECT_HELPER);

$clinic_id = $_SESSION['clinic_id'];
$clinic_row_result = mysqli_query($conn, "SELECT * FROM clinics WHERE clinic_id = $clinic_id");
$clinic_row = mysqli_fetch_assoc($clinic_row_result);

$errName = $errContact = $errEmail = $errURL = $errAddress = $errCity = $errState = $errZipcode = "";
$className = $classContact = $classEmail = $classURL = $classAddress = $classCity = $classState = $classZipcode = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["savebtn"])) {
        // Validasi dan proses data
        // ...

        if (multi_empty($errName, $errContact, $errURL, $errEmail, $errAddress, $errCity, $errState, $errZipcode)) {
            $clinicstmt = $conn->prepare("UPDATE clinics SET clinic_name = ?, clinic_email = ?, clinic_url = ?, clinic_contact = ?, clinic_address = ?, clinic_city = ?, clinic_state = ?, clinic_zipcode = ? WHERE clinic_id = ?");
            $clinicstmt->bind_param("ssssssssi", $clinic_name, $email, $url, $contact, $address, $city, $state, $zipcode, $clinic_row['clinic_id']);

            if ($clinicstmt->execute()) {
                // Update business hours
                // ...
                ob_end_clean();
                echo json_encode(['status' => 'success']);
                exit;
            } else {
                ob_end_clean();
                echo json_encode(['status' => 'error', 'message' => $clinicstmt->error]);
                exit;
            }
        } else {
            ob_end_clean();
            echo json_encode(['status' => 'validation_error', 'errors' => compact('errName', 'errContact', 'errURL', 'errEmail', 'errAddress', 'errCity', 'errState', 'errZipcode')]);
            exit;
        }
    } elseif (isset($_POST["uploadbtn"])) {
        // Proses upload file
        // ...
    }
}
?>
