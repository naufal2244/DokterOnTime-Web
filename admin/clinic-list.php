<?php
require_once('../config/autoload.php');
include('includes/path.inc.php');
include('includes/session.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include CSS_PATH;?>
    <style>
        .sukses {
            background-color: #87e7ae;
            color: white;

        }

        .badge {
            padding: 10px;
            width: 100px; /* Sesuaikan nilai ini sesuai kebutuhan */
            
            
        }
    </style>
</head>

<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER;?>
        <!-- Page content -->
        <div class="row">
            <div class="col-12">
                <!-- Card Content -->
                <div class="card">
                    <div class="card-body">
                        <!-- <div class="d-flex mb-3">
                            <h5 class="card-title mr-auto">Clinics List</h5>
                        </div> -->
                        <div class="card-inner">
                            <!-- Datatable -->
                            <div class="data-tables">
                                <table id="datatable" class="table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID Rumah Sakit</th>
                                            <th>Nama RS</th>
                                            <th>Nomor RS</th>
                                            <th>Tanggal Registrasi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $table_result = mysqli_query($conn, "SELECT * FROM clinics");
                                        while ($table_row = mysqli_fetch_assoc($table_result)) {
                                            $id = $table_row["clinic_id"];
                                            $encrypt_id = urlencode(base64_encode($id));
                                        ?>
                                        <tr>
                                            <td><?php echo '#' . '' . $table_row["clinic_id"]; ?></td>

                                            <td><?php echo $table_row["clinic_name"];?></td>
                                            <td><?php echo $table_row["clinic_contact"];?></td>
                                            <td><?php echo $table_row["date_created"];?></td>
                                            <td>
                                                <?php if ($table_row["clinic_status"] == "1") {
                                                    echo '<span class="badge sukses">Dikonfirmasi</span>';
                                                } else {
                                                    echo '<span class="badge badge-danger">Belum dikonfirmasi</span>';
                                                }?>
                                            </td>
                                            <td>
                                                <a href="clinic-view.php?cid=<?php echo $encrypt_id;?>" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i>Konfirmasi</a>
                                             
                                                <!-- <a href="clinic-view.php" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a> -->
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                        <th>ID Rumah Sakit</th>
                                            <th>Nama RS</th>
                                            <th>Nomor RS</th>
                                            <th>Tanggal Registrasi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- End Datatable -->
                        </div>
                    </div>
                </div>
                <!-- End Card Content -->
            </div>
        </div>
        <!-- End Page Content -->
    </div>

    <?php include JS_PATH;?>
    
</body>
</html>