<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

// Pastikan session sudah dimulai
// session_start();

// Ambil clinic_id dari session
$clinic_id = $_SESSION['clinic_id'];
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
                            $header = array("Klinik", "Nama Apoteker", "Email Apoteker");
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
                                    $query = "SELECT 
                                                c.clinic_name, 
                                                a.apoteker_email, 
                                                CONCAT(a.nama_depan, ' ', a.nama_belakang) AS apoteker_name
                                              FROM apoteker a
                                              JOIN clinics c ON a.clinic_id = c.clinic_id
                                              WHERE a.clinic_id = ?";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("i", $clinic_id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    while ($table_row = $result->fetch_assoc()) {
                                        ?><tr>
                                            <td><?= htmlspecialchars($table_row["clinic_name"]); ?></td>
                                            <td><?= htmlspecialchars($table_row["apoteker_name"]); ?></td>
                                            <td><?= htmlspecialchars($table_row["apoteker_email"]); ?></td>
                                        </tr>
                                    <?php
                                    }

                                    $stmt->close();
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
</body>

</html>
