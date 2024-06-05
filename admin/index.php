<?php
// SEMUA HANYA KONFIGURASI
require_once('../config/autoload.php');
include('includes/path.inc.php');
include('includes/session.inc.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- untuk memuat yang dibutuhkan -->
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="../assets/css/clinic/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
</head>

<body>
    <!-- MASUKIN kode navigate.php -->
    <?php include NAVIGATION; ?>
    
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <?php include WIDGET; ?>
        <!-- Page content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6>
                            <i class="far fa-clock"></i> <?php echo date('Y-m-d'); ?> <span id="timer"></span>
                            <script>
                                setInterval(function() {
                                    var currentTime = new Date();
                                    var currentHours = currentTime.getHours();
                                    var currentMinutes = currentTime.getMinutes();
                                    var currentSeconds = currentTime.getSeconds();
                                    currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
                                    currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
                                    var timeOfDay = (currentHours < 12) ? "AM" : "PM";
                                    currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
                                    currentHours = (currentHours == 0) ? 12 : currentHours;
                                    var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
                                    document.getElementById("timer").innerHTML = currentTimeString;
                                }, 1000);
                            </script>
                        </h6>
                    </div>
                </div>

                

            </div>
        </div>
        <!-- End Page Content -->
    </div>
    <?php include JS_PATH; ?>
</body>

</html>