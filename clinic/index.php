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
        <?php include WIDGET; ?>
        <div class="row">
            <div class="col-12">
                <?php
                if ($clinic_row["clinic_status"] == 0) {
                    echo '<div class="alert alert-danger mt-3" role="alert">
                            Maaf, administrator sistem sedang memeriksa profil Anda. Harap tunggu hingga disetujui! Terima kasih telah menggunakan platform kami.
                        </div>';
                } else {
                    $doctor_result = mysqli_query($conn, "SELECT * FROM doctors WHERE clinic_id = " . $clinic_row['clinic_id'] . "");
                    $doctor_row = mysqli_fetch_assoc($doctor_result);
                    if (mysqli_num_rows($doctor_result) == 0) {
                        echo '<div class="alert alert-warning mt-3" role="alert">
                               Silakan Tambahkan Dokter. Tambah Dokter di  <a href="doctor-add.php" class="alert-link">Sini</a>
                            </div>';
                    }
                }
                ?>

                <?php
                $doctor_result = mysqli_query($conn, "SELECT * FROM clinic_images WHERE clinic_id = " . $clinic_row['clinic_id'] . "");
                $doctor_row = mysqli_fetch_assoc($doctor_result);
                if (mysqli_num_rows($doctor_result) == 0) {
                  
                }
                ?>
                
                <!-- <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Hi, <?php echo $clinic_row["clinic_name"]; ?></h5>
                    </div>
                </div> -->

            </div>

            <div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <canvas id="myChart"></canvas>
            <script>
                Chart.platform.disableCSSInjection = true;
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                        datasets: [{
                            label: '#Banyak Janji Temu',
                            data: [
                                <?php
                                $month_array = range(1, 12); // Array dari 1 sampai 12 untuk bulan
                                $data_points = [];
                                foreach ($month_array as $month) {
                                    $clinic_id = $clinic_row['clinic_id']; // Pastikan clinic_id diambil dengan benar
                                    $query = "SELECT COUNT(*) as count FROM janji_temu jt JOIN doctors d ON jt.doctor_id = d.doctor_id WHERE MONTH(jt.tanggal_janji) = '$month' AND d.clinic_id = '$clinic_id' AND jt.status_periksa = 2";
                                    $result = mysqli_query($conn, $query);
                                    if (!$result) {
                                        die("Query Error: " . mysqli_error($conn)); // Tampilkan pesan kesalahan jika query gagal
                                    }
                                    $row = mysqli_fetch_assoc($result);
                                    $count = $row['count'];
                                    echo "$count,";
                                }
                                ?>
                            ],
                            fill: false,
                            borderColor: '#2196f3',
                            backgroundColor: '#2196f3',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Kunjungan Janji Temu Bulanan',
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    scaleIntegersOnly: true,
                                    stepSize: 1,
                                    beginAtZero: true,
                                }
                            }]
                        }
                    }
                });
            </script>
        </div>
    </div>
</div>





<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <canvas id="HorizontalChart"></canvas>
            <script>
                Chart.platform.disableCSSInjection = true;
                var ctx = document.getElementById('HorizontalChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'horizontalBar',
                    data: {
                        labels: [
                            <?php
                            $idquery = array();
                            $result = mysqli_query($conn,"SELECT * FROM doctors WHERE clinic_id = ".$clinic_row['clinic_id']." ");
                            while($row = mysqli_fetch_assoc($result)) {
                                echo '"'.$row['doctor_firstname'].' '.$row['doctor_lastname'].'",';
                                $idquery[] = $row["doctor_id"];
                            }
                            ?>
                        ],
                        datasets: [{
                            label: '#Banyak Janji Temu',
                            data: [
                                <?php
                                foreach ($idquery as $arrvalue) {
                                    $newsql = "SELECT * FROM janji_temu WHERE doctor_id = $arrvalue AND status_periksa = 2"; // Disesuaikan untuk menggunakan tabel dan kolom yang benar
                                    $idnum = mysqli_num_rows(mysqli_query($conn,$newsql));
                                    echo $idnum.',';
                                }
                                ?>
                            ],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Kunjungan Janji Temu Berdasarkan Dokter',
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
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


            <!-- <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                    <canvas id="PieChart"></canvas>
                        <script>
                            Chart.platform.disableCSSInjection = true;
                            var ctx = document.getElementById('PieChart').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    // labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                                    labels: [
                                        <?php
                                        $idquery = array();
                                        $result = mysqli_query($conn,"SELECT DISTINCT patient_nationality FROM appointment INNER JOIN patients ON appointment.patient_id = patients.patient_id WHERE clinic_id = ".$clinic_row['clinic_id']." ");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '"'.ucwords($row['patient_nationality']).'",';
                                            $idquery[] = $row["patient_nationality"];
                                        }
                                        ?>
                                    ],
                                    datasets: [{
                                        label: '# of Appointment',
                                        data: [
                                            <?php
                                            foreach ($idquery as $arrvalue) {
                                                $newsql = "SELECT * FROM appointment INNER JOIN patients ON appointment.patient_id = patients.patient_id WHERE patients.patient_nationality = '$arrvalue' AND appointment.consult_status = 1 ";
                                                $idnum = mysqli_num_rows(mysqli_query($conn,$newsql));
                                                echo $idnum.',';
                                            }
                                            ?>
                                        ],
                                        backgroundColor: [
                                            'rgba(255, 99, 132, 0.2)',
                                            'rgba(54, 162, 235, 0.2)',
                                            'rgba(255, 206, 86, 0.2)',
                                            'rgba(75, 192, 192, 0.2)',
                                            'rgba(153, 102, 255, 0.2)',
                                            'rgba(255, 159, 64, 0.2)'
                                        ],
                                        borderColor: [
                                            'rgba(255, 99, 132, 1)',
                                            'rgba(54, 162, 235, 1)',
                                            'rgba(255, 206, 86, 1)',
                                            'rgba(75, 192, 192, 1)',
                                            'rgba(153, 102, 255, 1)',
                                            'rgba(255, 159, 64, 1)'
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    title: {
                                        display: true,
                                        text: 'Country Visited Appointment',
                                    },
                                }
                            });
                        </script>
                    </div>
                </div>
            </div> -->

        </div>
    </div>
    <?php include JS_PATH; ?>
</body>

</html>