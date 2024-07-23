<?php
require_once('../config/autoload.php');

if (isset($_POST['id_riwayat_medis']) && isset($_POST['status_data'])) {
    $status_data = $_POST['status_data'];

    $conn->autocommit(FALSE); // Nonaktifkan autocommit

    foreach ($status_data as $status) {
        $id_obat_pasien = $status['id_obat_pasien'];
        $status_pembuatan = $status['status_pembuatan'];

        $sql = "UPDATE obat_pasien SET status_pembuatan = ? WHERE id_obat_pasien = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo 'error';
            $conn->rollback(); // Kembalikan perubahan jika ada kesalahan
            exit;
        }
        $stmt->bind_param('ii', $status_pembuatan, $id_obat_pasien);
        if (!$stmt->execute()) {
            echo 'error';
            $stmt->close();
            $conn->rollback(); // Kembalikan perubahan jika ada kesalahan
            exit;
        }
        $stmt->close();
    }

    $conn->commit(); // Komit perubahan jika semua berhasil
    echo 'success';
}
?>
