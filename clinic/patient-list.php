<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style>
        .status-label {
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            margin: 2px 0;
            width: 120px;
            text-align: center;
        }

        .status-belum-periksa {
            background-color: #d3d3d3;
            color: black;
        }

        .status-sedang-periksa {
            background-color: #ffd700;
            color: black;
        }

        .status-sudah-periksa {
            background-color: #87e7ae;
            color: white;
        }

        .status-tidak-hadir {
            background-color: #FF4500;
            color: white;
        }

        .col-form-label {
            margin-left: 15px;
        }
    </style>
</head>

<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <!-- Page content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Filter Tanggal -->
                        <div class="form-group row">
                            <label for="filterDate"  class="col-form-label">Filter Tanggal:</label>
                            <div class="col-sm-3">
                                <input type="text" id="filterDate" class="form-control" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                        <!-- Datatable -->
                        <?php
                        function headerTable()
                        {
                            $header = array("#Id Janji", "Tanggal Janji Temu", "Dokter Penanggung Jawab", "Nama Pasien", "No HP Pasien", "Status Periksa");
                            $arrlen = count($header);
                            for ($i = 0; $i < $arrlen; $i++) {
                                echo "<th>" . $header[$i] . "</th>" . PHP_EOL;
                            }
                        }

                        function statusClass($status)
                        {
                            switch ($status) {
                                case "0":
                                    return "status-belum-periksa";
                                case "1":
                                    return "status-sedang-periksa";
                                case "2":
                                    return "status-sudah-periksa";
                                case "3":
                                    return "status-tidak-hadir";
                                default:
                                    return "";
                            }
                        }

                        function statusText($status)
                        {
                            switch ($status) {
                                case "0":
                                    return "Belum Periksa";
                                case "1":
                                    return "Sedang Periksa";
                                case "2":
                                    return "Sudah Periksa";
                                case "3":
                                    return "Tidak Hadir";
                                default:
                                    return "";
                            }
                        }
                        ?>
                        <div class="data-tables">
                            <table id="datatable" class="table" style="width:100%">
                                <thead>
                                    <tr>
                                        <?php headerTable(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $appointment_counter = 1; // Initialize appointment counter
                                    $table_result = mysqli_query($conn, "
                                        SELECT 
                                            janji_temu.id_janji_temu, 
                                            janji_temu.tanggal_janji, 
                                            doctors.doctor_id,  /* Pastikan mengambil doctor_id */
                                            doctors.doctor_firstname, 
                                            doctors.doctor_lastname, 
                                            janji_temu.nama_lengkap, 
                                            janji_temu.no_hp, 
                                            janji_temu.status_periksa
                                        FROM 
                                            janji_temu
                                        INNER JOIN 
                                            doctors ON janji_temu.doctor_id = doctors.doctor_id 
                                        WHERE 
                                            doctors.clinic_id = '" . $clinic_row['clinic_id'] . "'
                                        ORDER BY 
                                            janji_temu.tanggal_janji ASC
                                    ");
                                    while ($table_row = mysqli_fetch_assoc($table_result)) {
                                        ?><tr>
                                            <td><?= $appointment_counter++; ?></td>
                                            <td><?= $table_row["tanggal_janji"]; ?></td>
                                            <td>
                                                <a href="doctor-view.php?did=<?= urlencode(base64_encode($table_row['doctor_id'])); ?>">
                                                    <?= $table_row["doctor_firstname"] . ' ' . $table_row["doctor_lastname"]; ?>
                                                </a>
                                            </td>
                                            <td><?= $table_row["nama_lengkap"]; ?></td>
                                            <td><?= $table_row["no_hp"]; ?></td>
                                            <td>
                                                <span class="status-label <?= statusClass($table_row["status_periksa"]); ?>">
                                                    <?= statusText($table_row["status_periksa"]); ?>
                                                </span>
                                            </td>
                                        </tr><?php
                                    }
                                    ?>
                                </tbody>
                                <!-- <tfoot>
                                    <tr>
                                        <?php headerTable(); ?>
                                    </tr>
                                </tfoot> -->
                            </table>
                        </div>
                        <!-- End Datatable -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Content -->
    </div>

    <?php include JS_PATH; ?>
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                searching: true, // Disable the default search
                destroy: true, // Allow reinitialization of the DataTable
                language: {
            lengthMenu: "Tampilkan _MENU_ entri",
            search: "Cari:",
            paginate: {
                previous: "Sebelumnya",
                next: "Berikutnya"
            },
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri"
        }
                
            });

            // Date picker initialization
            $('#filterDate').datetimepicker({
                format: 'YYYY-MM-DD'
            });

            // Custom filtering function which will search data in column four between two values
            $('#filterDate').on('dp.change', function() {
                var selectedDate = $(this).val();
                table.columns(1).search(selectedDate).draw();
            });
        });
    </script>
</body>

</html>
