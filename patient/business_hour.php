<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../config/database.php");

$contentdata = file_get_contents("php://input");
$getdata = json_decode($contentdata);

$id = $getdata->clinicID;

$query = "SELECT * FROM business_hour WHERE clinic_id = '$id'";
$result = mysqli_query($conn, $query);

$numrow = mysqli_num_rows($result);

if ($numrow > 0) {
    $arr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $arr[] = $row;
    }
    echo json_encode($arr);
    mysqli_close($conn);
} else {
    echo json_encode(null);
}
?>
