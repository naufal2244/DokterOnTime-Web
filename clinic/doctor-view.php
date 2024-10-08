<?php
require_once('../config/autoload.php');
require_once('./includes/path.inc.php');
require_once('./includes/session.inc.php');

$doctor_id = decrypt_url($_GET['did']);
$result = mysqli_query($conn,"SELECT * FROM doctors WHERE doctor_id = '".$doctor_id."' ");
$doctor_row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
    <style>
        .text-left {
            text-align: left !important;
        }
    </style>
</head>

<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <!-- Page content -->
       
            <div class="col-md-10 text-left">
                <div class="card "> <!-- Menambahkan class text-left -->
                    <div class="card-body">
                        <h5 class="font-weight-bold mb-2">Dr. <?php echo $doctor_row["doctor_firstname"] . ' ' . $doctor_row["doctor_lastname"]; ?></h5>
                        <h6>
                            <?php
                            $table_result = mysqli_query($conn, "SELECT * FROM speciality WHERE speciality_id =  '".$doctor_row["doctor_speciality"]."' ");
                            while ($table_row = mysqli_fetch_assoc($table_result)) {
                                echo $table_row['speciality_name'];
                            }
                            ?>
                        </h6>
                    </div>
                </div>
                <div class="mt-3">
                    <h5>Tentang</h5>
                    <div class="card text-left"> <!-- Menambahkan class text-left -->
                        <div class="card-body">
                            <p><i class="fas fa-vote-yea fa-fw mr-3"></i><?= $doctor_row["doctor_experience"]; ?> Thn Pengalaman</p>
                            <p><i class="fas fa-phone-alt fa-fw mr-3"></i><?= $doctor_row["doctor_contact"]; ?></p>
                            <p><i class="far fa-envelope fa-fw mr-3"></i><?= $doctor_row["doctor_email"]; ?></p>
                            <p><i class="far fa-calendar fa-fw mr-3"></i><?= $doctor_row["doctor_dob"]; ?></p>
                            <p><i class="fas fa-venus-mars fa-fw mr-3"></i><?= $doctor_row["doctor_gender"]; ?></p>
                            <p><i class="fas fa-language fa-fw mr-3"></i><?= $doctor_row["doctor_spoke"]; ?></p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="card text-left"> <!-- Menambahkan class text-left -->
                        <div class="card-body">
                            <p><?= $doctor_row["doctor_desc"]; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <!-- End Page Content -->
    </div>
    <?php include JS_PATH; ?>
</body>

</html>
