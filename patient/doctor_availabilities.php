<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$doctorId = $_GET['doctorId'];
$dayId = $_GET['dayId'];
$timeBlockId = $_GET['timeBlockId'];

if (!isset($doctorId) || !isset($dayId) || !isset($timeBlockId)) {
    echo json_encode([]);
    exit();
}

$query = "SELECT s.session_id, da.available, jt.status_periksa 
          FROM doctor_availabilities da
          JOIN sessions s ON da.session_id = s.session_id 
          LEFT JOIN janji_temu jt ON s.session_id = jt.session_id AND jt.tanggal_janji = CURDATE()
          WHERE da.doctor_id = $doctorId 
          AND da.day_id = $dayId 
          AND s.time_block_id = $timeBlockId";

$result = mysqli_query($conn, $query);

$numrow = mysqli_num_rows($result);

if($numrow > 0) {
    $arr = array();
    while($row = mysqli_fetch_assoc($result)) {
        $session_id = $row['session_id'];
        $available = $row['available'];
        $status_periksa = $row['status_periksa'];
        
        // Check if the session should be available or not
        $isAvailable = ($available == '1' && ($status_periksa === null || $status_periksa == '2' || $status_periksa == '3')) ? 1 : 0;
        
        $arr[] = [
            'session_id' => $session_id,
            'isAvailable' => $isAvailable
        ];
    }
    echo json_encode($arr);
    mysqli_close($conn);
} else {
    echo json_encode([]);
}
?>
