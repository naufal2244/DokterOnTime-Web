<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
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
                        <!-- Datatable -->
                        <?php
                        function headerTable()
                        {
                            $header = array("Nama Dokter", "Perawat Email", "Nama Perawat");
                            $arrlen = count($header);
                            for ($i = 0; $i < $arrlen; $i++) {
                                echo "<th>" . $header[$i] . "</th>" . PHP_EOL;
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
                                    $table_result = mysqli_query($conn, "SELECT 
                                        CONCAT(d.doctor_firstname, ' ', d.doctor_lastname) AS doctor_name, 
                                        p.alamat_email, 
                                        CONCAT(p.nama_depan, ' ', p.nama_belakang) AS nurse_name
                                    FROM perawat p
                                    JOIN doctors d ON p.doctor_id = d.doctor_id");

                                    while ($table_row = mysqli_fetch_assoc($table_result)) {
                                        ?><tr>
                                            <td><?= htmlspecialchars($table_row["doctor_name"]); ?></td>
                                            <td><?= htmlspecialchars($table_row["alamat_email"]); ?></td>
                                            <td><?= htmlspecialchars($table_row["nurse_name"]); ?></td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <?php headerTable(); ?>
                                    </tr>
                                </tfoot>
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
</body>

</html>
