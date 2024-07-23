<?php
require_once('../config/autoload.php');

if (isset($_POST['id_riwayat_medis']) && isset($_POST['status_ambil'])) {
    $id_riwayat_medis = $_POST['id_riwayat_medis'];
    $status_ambil = $_POST['status_ambil'];

    $sql = "UPDATE obat_pasien SET status_ambil = ? WHERE id_riwayat_medis = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo 'error';
        exit;
    }
    $stmt->bind_param('ii', $status_ambil, $id_riwayat_medis);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
}
?>
