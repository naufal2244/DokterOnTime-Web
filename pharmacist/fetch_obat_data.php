<?php
require_once('../config/autoload.php');

if (isset($_POST['id_riwayat_medis'])) {
    $id_riwayat_medis = $_POST['id_riwayat_medis'];
    $sql = "
        SELECT 
            obat_pasien.id_obat_pasien,
            obat.nama_obat, 
            CONCAT(dosis.deskripsi_dosis, ', ', frekuensi.deskripsi_frekuensi) AS dosis,
            obat_pasien.status_pembuatan
        FROM obat_pasien
        JOIN obat ON obat_pasien.id_obat = obat.id_obat
        JOIN dosis ON obat_pasien.id_dosis = dosis.id_dosis
        JOIN frekuensi ON obat_pasien.id_frekuensi = frekuensi.id_frekuensi
        WHERE obat_pasien.id_riwayat_medis = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_riwayat_medis);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        die("Error: " . $conn->error);
    }

    $output = '';
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $checked = $row['status_pembuatan'] == 1 ? 'checked' : '';
        $output .= "<tr>";
        $output .= "<td>{$no}</td>";
        $output .= "<td>{$row['nama_obat']}</td>";
        $output .= "<td>{$row['dosis']}</td>";
        $output .= "<td><input type='checkbox' name='status' value='{$row['id_obat_pasien']}' {$checked}></td>";
        $output .= "</tr>";
        $no++;
    }

    echo $output;
    $stmt->close();
}
?>
