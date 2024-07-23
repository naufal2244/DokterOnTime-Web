<?php
require_once('../config/autoload.php');
include('includes/path.inc.php');
include('includes/session.inc.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="../assets/css/clinic/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
    
</head>

<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <!-- Page content -->
        <?php include WIDGET; ?>
        <div class="row">
        <div class="col-md-6 ">
    <div class="card">
        <div class="card-body">
            <canvas id="myChart"></canvas>
            <script>
                Chart.platform.disableCSSInjection = true;
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"],
                        datasets: [{
                            label: '# Banyak Janji Temu  ditangani',
                            data: [
                                <?php
                                $month_array = array("jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec");
                                foreach ($month_array as $key => $month_value) {
                                    $result = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM janji_temu WHERE MONTH(tanggal_janji) = '" . ++$key . "' AND doctor_id = '" . $doctor_row['doctor_id'] . "' AND status_periksa = 2"));
                                    echo "$result,";
                                }
                                ?>
                            ],
                            backgroundColor: [
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: "Rekap Janji Temu"
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    scaleIntegersOnly: true,
                                    stepSize: 1,
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
            </script>
        </div>
    </div>
</div>

<div class="col-md-6 ">
    <div class="card">
        <div style="height: 250px;" class="card-body">
           
        </div>
    </div>
</div>

          

          
        <!-- End Page Content -->
    </div>
    <?php include JS_PATH; ?>
</body>

</html>