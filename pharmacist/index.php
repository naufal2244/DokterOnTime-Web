<?php
require_once('../config/autoload.php');
include('includes/path.inc.php');
include('includes/session.inc.php');

// Mendapatkan clinic_id apoteker dari database
$apoteker_id = $_SESSION['ApotekerRoleID'];
$sql_clinic_id = "SELECT clinic_id FROM apoteker WHERE id_apoteker = ?";
$stmt_clinic_id = $conn->prepare($sql_clinic_id);
$stmt_clinic_id->bind_param('i', $apoteker_id);
$stmt_clinic_id->execute();
$result_clinic_id = $stmt_clinic_id->get_result();
$clinic_id_row = $result_clinic_id->fetch_assoc();
$clinic_id = $clinic_id_row['clinic_id'];
$stmt_clinic_id->close();

function fetchResepData($conn, $clinic_id) {
    $sql = "
        SELECT 
            janji_temu.nama_lengkap AS patient_name,
            CONCAT(
                LEFT(janji_temu.nomor_antrian, LOCATE('-', janji_temu.nomor_antrian) - 1),
                '-',
                RIGHT(janji_temu.nomor_antrian, LOCATE('-', REVERSE(janji_temu.nomor_antrian)) - 1)
            ) AS nomor_pasien,
            obat_pasien.id_riwayat_medis,
            CASE 
                WHEN MIN(obat_pasien.status_pembuatan) = 1 THEN 'Sudah Selesai'
                ELSE 'Belum Selesai'
            END AS status_pembuatan,
            MIN(obat_pasien.status_ambil) AS status_ambil_obat -- Pastikan untuk mengambil kolom ini
        FROM obat_pasien
        JOIN riwayat_medis ON obat_pasien.id_riwayat_medis = riwayat_medis.id_riwayat_medis
        JOIN janji_temu ON riwayat_medis.id_janji_temu = janji_temu.id_janji_temu
        JOIN doctors ON janji_temu.doctor_id = doctors.doctor_id
        WHERE doctors.clinic_id = ?
        GROUP BY riwayat_medis.id_riwayat_medis, janji_temu.nama_lengkap, janji_temu.nomor_antrian";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $clinic_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        die("Error: " . $conn->error);
    }

    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}


$resepData = fetchResepData($conn, $clinic_id);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="../assets/css/clinic/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Tambahkan baris ini -->
    <style>
        .modal-lg {
            max-width: 90%;
        }
        .status-box {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        .status-selesai {
            background-color: #87e7ae;
        }
        .status-belum {
            background-color: #FF4500;
        }
        .status-cell {
            vertical-align: middle;
        }
    </style>
</head>


<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>

        <div class="col-md-12">
            <!-- Card Content -->
            <div class="card patient-status-bar">
                <div class="card-body">
                    <div class="d-flex bd-highlight">
                        <div class="flex-fill bd-highlight">
                            <p class="text-muted text-center">Informasi Apoteker</p>
                            <h5 class="font-weight-bold text-center"><?php echo $apoteker_row["nama_depan"] . ' ' . $apoteker_row["nama_belakang"]; ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <!-- Card Content -->
            <div class="card">
                <div class="card-body">
                    <!-- Datatable -->
                    <div class="col-md-12 mb-3">
                        <h4 class="font-weight-bold text-center">Daftar Resep</h4>
                    </div>

                    <div class="data-tables">
                        <table id="datatable" class="table table-responsive-lg nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pasien</th>
                                    <th>Nomor Pasien</th>
                                    <th>Status Pembuatan</th>
                                    <th>Aksi</th>
                                    <th>Status Ambil Obat</th> <!-- Tambahkan kolom baru -->
                                </tr>
                            </thead>
                            <tbody id="responsecontainer">
                                <?php
                                $no = 1;
                                foreach ($resepData as $row) {
                                    $status_pembuatan = $row['status_pembuatan'] == 'Sudah Selesai' ? 'Sudah Selesai' : 'Belum Selesai';
                                    $status_class = $row['status_pembuatan'] == 'Sudah Selesai' ? 'status-selesai' : 'status-belum';
                                    $status_ambil = $row['status_ambil_obat'] == 1 ? 'Sudah Diambil' : 'Belum Diambil';

                                    echo "<tr data-id='{$row['id_riwayat_medis']}'>";
                                    echo "<td>{$no}</td>";
                                    echo "<td>{$row['patient_name']}</td>";
                                    echo "<td>{$row['nomor_pasien']}</td>";
                                    echo "<td class='status-cell'><span class='status-pembuatan status-box {$status_class}'>{$status_pembuatan}</span></td>";
                                    echo "<td><button class='btn btn-primary aksi-button' data-toggle='modal' data-target='#statusModal' data-id='{$row['id_riwayat_medis']}'>Aksi</button></td>";
                                    echo "<td>";
                                    echo "<select class='form-control status-ambil-obat' data-id='{$row['id_riwayat_medis']}'>";
                                    echo "<option value='0'" . ($row['status_ambil_obat'] == 0 ? " selected" : "") . ">Belum Diambil</option>";
                                    echo "<option value='1'" . ($row['status_ambil_obat'] == 1 ? " selected" : "") . ">Sudah Diambil</option>";
                                    echo "</select>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $no++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- End Datatable -->
                </div>
            </div>
            <!-- End Card Content -->
        </div>

    </div>

    <!-- Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Status Pembuatan Obat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Table inside modal -->
                    <div class="data-tables mt-3">
                        <table id="modalTable" class="table table-responsive-lg nowrap">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Obat</th>
                                    <th>Dosis</th>
                                    <th>Status Pembuatan</th>
                                </tr>
                            </thead>
                            <tbody id="modalResponseContainer">
                                <!-- Data akan diisi melalui AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table inside modal -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="saveChanges()">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <?php include JS_PATH; ?>

    <script>
      function saveChanges() {
    const idRiwayatMedis = $('#statusModal').data('idRiwayatMedis');
    const statusData = [];

    $('#modalResponseContainer input[name="status"]').each(function() {
        const idObatPasien = $(this).val();
        const statusPembuatan = $(this).is(':checked') ? 1 : 0;
        statusData.push({ id_obat_pasien: idObatPasien, status_pembuatan: statusPembuatan });
    });

    console.log(statusData);  // Untuk memastikan data yang dikirim benar

    // Kirim permintaan AJAX untuk memperbarui status_pembuatan di database
    $.ajax({
        url: 'update_status_periksa.php',
        method: 'POST',
        data: {
            id_riwayat_medis: idRiwayatMedis,
            status_data: statusData
        },
        success: function(response) {
            if (response === 'success') {
                $('#statusModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Status pembuatan obat berhasil diperbarui',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload(); // Refresh halaman untuk memperbarui status pembuatan di tabel utama
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal memperbarui status',
                });
            }
        },
        error: function(error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Terjadi kesalahan dalam memperbarui status',
            });
        }
    });
}

        $('.status-ambil-obat').change(function() {
            const idRiwayatMedis = $(this).data('id');
            const statusAmbilObat = $(this).val();

            $.ajax({
                url: 'update_status_ambil_obat.php',
                method: 'POST',
                data: {
                    id_riwayat_medis: idRiwayatMedis,
                    status_ambil: statusAmbilObat
                },
                success: function(response) {
                    if (response === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status ambil obat berhasil diperbarui',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal memperbarui status ambil obat',
                        });
                    }
                },
                error: function(error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Terjadi kesalahan dalam memperbarui status ambil obat',
                    });
                }
            });
        });

        $('#statusModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const idRiwayatMedis = button.data('id');
            const modal = $(this);
            modal.data('idRiwayatMedis', idRiwayatMedis);

            // Fetch data terkait untuk ditampilkan dalam modal
            $.ajax({
                url: 'fetch_obat_data.php', // Ganti dengan URL yang sesuai untuk fetch data
                method: 'POST',
                data: { id_riwayat_medis: idRiwayatMedis },
                success: function(response) {
                    // Isi data pada modal
                    $('#modalResponseContainer').html(response);
                }
            });
        });

        $('#statusModal').on('hide.bs.modal', function() {
            // Simpan status checkbox ke dalam atribut data
            const statusCheckboxes = $('#modalResponseContainer input[name="status"]');
            const statusChecked = [];
            statusCheckboxes.each(function() {
                statusChecked.push($(this).is(':checked'));
            });
            $(this).data('statusChecked', statusChecked);
        });

        $('#statusModal').on('shown.bs.modal', function() {
            // Muat status checkbox dari atribut data
            const statusChecked = $(this).data('statusChecked') || [];
            $('#modalResponseContainer input[name="status"]').each(function(index) {
                $(this).prop('checked', statusChecked[index]);
            });
        });
    </script>
</body>

</html>
