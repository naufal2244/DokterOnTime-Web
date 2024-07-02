<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $encrypted_id = $_GET["id"];
   

    $app_id = decrypt_url($encrypted_id);

    if ($app_id) {
        $app_id = $conn->real_escape_string($app_id); // Menghindari SQL Injection

        $result = $conn->query("SELECT * FROM appointment LEFT JOIN patients ON appointment.patient_id = patients.patient_id WHERE appointment.app_id = '$app_id'");
        
        if ($result) {
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
               
                
                $patient_id = $row["patient_id"];

                $patient_age = date('Y') - date('Y', strtotime($row['patient_dob']));
                
                // Proses selanjutnya
            } else {
                die("Error: Appointment not found.");
            }
        } else {
            die("Error: " . $conn->error);
        }
    } else {
        die("Error: Decryption failed.");
    }
} else {
    die("Error: Invalid ID");
}

$patient_id = $row["patient_id"];
$patient_age = date('Y') - date('Y', strtotime($row['patient_dob']));

$medresult = $conn->query(
    "SELECT * FROM medical_record M 
    INNER JOIN clinics C ON M.clinic_id = C.clinic_id
    INNER JOIN patients P ON M.patient_id = P.patient_id
    WHERE M.patient_id = $patient_id ORDER BY M.med_id DESC"
);
$medrow = $medresult->fetch_assoc();

$errors = array();
$success = false; // Definisikan variabel $success di awal

if (isset($_POST['prescriptionbtn'])) {
    $sympton = escape_input($_POST['sympton']);
    $diagnosis = escape_input($_POST['diagnosis']);
    $advice = escape_input($_POST['advice']);

    if (empty($sympton)) {
        array_push($errors, "Symptons is required");
    }

    if (empty($diagnosis)) {
        array_push($errors, "Diagnosis is required");
    }

    if (empty($advice)) {
        array_push($errors, "Advice is required");
    }

    if (count($errors) == 0) {
        $stmt = $conn->prepare("INSERT INTO medical_record (med_sympton, med_diagnosis, med_advice, med_date, patient_id, clinic_id, doctor_id) VALUE (?,?,?,?,?,?,?) ");
        $stmt->bind_param("sssssss", $sympton, $diagnosis, $advice, $date_created, $patient_id, $doctor_row['clinic_id'], $doctor_row['doctor_id']);
        $stmt->execute();
        $stmt->close();
        header('Location: '.$_SERVER['REQUEST_URI']);
    }
}

$apperrors = array();

if (isset($_POST['appointmentbtn'])) {
    $date = escape_input($_POST['inputAppointmentDate']);
    $time = escape_input($_POST['inputAppointmentTime']);
    $treatment = $conn->real_escape_string($_POST['inputTreatment']);

    if (empty($date)) {
        array_push($apperrors, "Dates is required");
    }

    if (empty($time)) {
        array_push($apperrors, "Time is required");
    }

    if (empty($treatment)) {
        array_push($apperrors, "Treatment is required");
    }

    if (count($apperrors) == 0) {
        $appstmt = $conn->prepare("INSERT INTO appointment (app_date, app_time, treatment_type, patient_id, clinic_id, doctor_id) VALUE (?,?,?,?,?,?) ");
        $appstmt->bind_param("ssssss", $date, $time, $treatment, $patient_id, $doctor_row['clinic_id'], $doctor_row['doctor_id']);
        $appstmt->execute();
        $appstmt->close();
        header('Location: '.$_SERVER['REQUEST_URI']);
    }
}

if (isset($_POST['teruskan_resep'])) {
    $nama_obat = escape_input($_POST['nama_obat']);
    $inputDosage = escape_input($_POST['inputDosage']);
    $statusPembuatan = 0; // Default value is false (not ready yet)

    if (empty($nama_obat)) {
        array_push($errors, "Nama obat tidak boleh kosong");
    }

    if (empty($inputDosage)) {
        array_push($errors, "Dosis tidak boleh kosong");
    }

    if (count($errors) == 0) {
       // Ambil ID nama_obat dari tabel obat berdasarkan nama obat
       $stmt = $conn->prepare("SELECT id_obat FROM obat WHERE nama_obat = ?");
       $stmt->bind_param("s", $nama_obat);
       $stmt->execute();
       $result = $stmt->get_result();
       if ($result->num_rows > 0) {
        $obat = $result->fetch_assoc();
        $nama_obat_id = $obat['id_obat'];


         // Masukkan data ke tabel resep
         $stmt = $conn->prepare("INSERT INTO resep (status_pembuatan, dosis, nama_obat, patient_id) VALUES (?, ?, ?, ?)");
         $stmt->bind_param("issi", $statusPembuatan, $inputDosage, $nama_obat_id, $patient_id); // Tambahkan $patient_id di sini
         if ($stmt->execute()) {
            $success = true;
        }
        

       
    } else {
        array_push($errors, "Nama obat tidak ditemukan.");
    }
    $stmt->close();
    }
}

$apperrors = array();


 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include CSS_PATH; ?>
    <!-- Memuat jQuery dan jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Memuat jQuery dan jQuery UI JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
<?php
if (isset($_POST["completebtn"])) {
    $id = escape_input($_POST['inputID']);
    $comstmt = $conn->prepare("UPDATE appointment SET consult_status = 1 WHERE app_id = ? ");
    $comstmt->bind_param("s", $id);

    if ($comstmt->execute()) {
        echo '<script>
            Swal.fire({ title: "Great!", text: "Successfully!", type: "success" }).then((result) => {
                if (result.value) { window.location.href = "appointment.php"; }
            });
            </script>';
    }
    $comstmt->close();
}
?>
    <?php include NAVIGATION; ?>
    <!-- Page content holder -->
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <!-- Page content -->
        <div class="row">
            <div class="col-12">
                <div class="modal fade" id="followup" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Add <strong><?= $row["patient_firstname"] . ' ' . $row["patient_lastname"] ?></strong> Follow Up Visit</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="POST">
                                <?php
                                    if (count($apperrors) > 0) {
                                        echo '<div class="alert alert-warning" role="alert">';
                                        foreach ($apperrors as $err) {
                                            echo $err . '<br>';
                                        }
                                        echo '</div>';
                                    }
                                ?>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Treatment Type</label>
                                        <select name="inputTreatment" id="inputTreatment" class="form-control">
                                            <?php
                                                $treatresult = mysqli_query($conn, "SELECT * FROM treatment_type WHERE doctor_id = '" . $doctor_row['doctor_id'] . "'");
                                                while($treatrow = mysqli_fetch_assoc($treatresult)) {
                                                    echo '<option value='.$treatrow['treatment_name'].'>'.$treatrow['treatment_name'].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="hidden" class="form-control form-control-sm" name="inputAppointmentDate" id="inputAppointmentDate">
                                        <input type="hidden" class="form-control form-control-sm" name="inputAppointmentTime" id="inputAppointmentTime">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Select Date</label>
                                            <div id="datepicker" onclick="getDate()"></div>
                                        </div>
                                        <div class="form-group">
                                            <label>Select Time : <small id="labelAppointmentTime"></small></label>
                                            <div id="responsecontainer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="appointmentbtn" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="resep_teruskan" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Teruskan Resep ke Apoteker</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="POST">
                                <?= display_error();?>
                                <div class="modal-body">
                               
                                <div class="form-group ">
                                    <label>Nama Obat</label>
                                    <input type="text" id="nama_obat" name="nama_obat" class="form-control" placeholder="Type here..." aria-haspopup="true" aria-expanded="false" onkeyup="javascript:load_data(this.value)" onfocus="javascript:load_search_history()">
                                    <span id="nama_obat_result"></span>              
                                </div>
                                    <div class="form-group ">
                                        <label>Dosis</label>
                                        <select name="inputDosage" id="inputDosage" class="form-control">
                                            <option value="Sampe mati overdosis">Sampe mati overdosis</option>
                                            <option value="gila">Sampe gila</option>
                                            <option value="Sampe Gila">Sampe Gila</option>
                                            <option value="kanker">Sampe kanker</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="status_pembuatan" value="0">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="teruskan_resep" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="prescription" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Add New Prescription</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" method="POST">
                                <?= display_error();?>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Symptons</label>
                                        <textarea name="sympton" class="form-control" id="sympton" cols="30" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Diagnosis</label>
                                        <input type="text" name="diagnosis" class="form-control" id="diagnosis">
                                    </div>
                                    <div class="form-group">
                                        <label>Advice</label>
                                        <textarea name="advice" class="form-control" id="advice" cols="30" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="prescriptionbtn" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="complete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="border:none;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="inputID" value="<?= $app_id ?>">
                                Case Complete for <b><?= $row["patient_lastname"].' '.$row["patient_firstname"] ?></b>
                            </div>
                            <div class="modal-footer" style="border:none;">
                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" name="completebtn" class="btn btn-sm btn-success px-3">Yes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <!-- Card Content -->
                <div class="card patient-status-bar">
                    <div class="card-body">
                        <div class="d-flex bd-highlight">
                            <div class="flex-fill bd-highlight">
                                <p class="text-muted">Patient Info</p>
                                <h5 class="font-weight-bold"><?php echo $row["patient_lastname"] . ' ' . $row["patient_firstname"] ?></h5>
                                <p><?= $patient_age ?>,&nbsp; <?= strtoupper($row["patient_gender"]) ?> </p>
                            </div>
                            <div class="flex-fill bd-highlight">
                                <p class="text-muted">Last Visit</p>
                                <h5 class="font-weight-bold">
                                    <?php if ($medresult->num_rows == 0) {
                                        echo 'New Patient';
                                    } else {
                                        echo date_format(new DateTime($medrow['med_date']), 'Y-m-d');
                                    }
                                    ?>
                                </h5>
                            </div>
                            <div class="flex-fill bd-highlight">
                                <p class="text-muted">Diagnosis</p>
                                <h5 class="font-weight-bold">
                                    <?php if ($medresult->num_rows == 0) {
                                        echo 'New Patient';
                                    } else {
                                        echo $medrow['med_diagnosis'];
                                    }
                                    ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <nav class="navbar px-0 mb-3">
                    <div class="nav nav-pills mr-auto">
                        <a class="nav-item text-sm-center nav-link active" data-toggle="pill" href="#tab1">Prescription Info</a>
                        <a class="nav-item text-sm-center nav-link" data-toggle="pill" href="#tab3">Appointment Record</a>
                        <div class="dropdown mr-1">
                            <button type="button" class="btn btn-secondary dropdown-toggle" id="dropdownMenuOffset" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
                            Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
                            <a class="dropdown-item" data-toggle="modal"  href="#resep_teruskan">Teruskan Resep</a>
                            
                            <a class="dropdown-item" href="#">Another action</a>
                            
                            </div>
                        </div>
                    </div>
                    <div class=" nav nav-pills ml-auto">
                        <a class="nav-item btn btn-sm btn-link" data-toggle="modal" href="#prescription">Add Prescription</a>
                        <a class="nav-item btn btn-sm btn-link" data-toggle="modal" href="#followup">Add Appointment</a>
                        
                        <button class="nav-item btn btn-sm btn-success" data-toggle="modal" href="#complete">Case Complete</button>
                    </div>
                </nav>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1">
                        <div class="card">
                            <div class="card-body">
                                <table class="table nowrap">
                                    <thead>
                                        <th>Symptons</th>
                                        <th>Diagnosis</th>
                                        <th>Date Recorded</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $tresult = $conn->query("SELECT * FROM medical_record WHERE patient_id = $patient_id");
                                        if ($tresult->num_rows == 0) {
                                            echo '<td colspan="4">No Record Found</td>';
                                        } else {
                                            while ($trow = $tresult->fetch_assoc()) {
                                                ?>
                                                <tr>
                                                    <td><?= $trow['med_sympton'] ?></td>
                                                    <td><?= $trow['med_diagnosis'] ?></td>
                                                    <td><?= $trow['med_date'] ?></td>
                                                    <td><button data-toggle="modal" data-target="#viewdiagnosis<?= $trow['med_id']?>" class="btn btn-sm btn-primary px-3">View</button></td>
                                                </tr>

                                                <div class="modal fade" id="viewdiagnosis<?= $trow['med_id']?>" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h6 class="modal-title">View Details</h6>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <p class="col-sm-3 text-right"><b>Symptons</b></p>
                                                                    <div class="col-sm-6">
                                                                    <p><?= $trow['med_sympton'] ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <p class="col-sm-3 text-right"><b>Diagnosis</b></p>
                                                                    <div class="col-sm-6">
                                                                    <p><?= $trow['med_diagnosis'] ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <p class="col-sm-3 text-right"><b>Advice</b></p>
                                                                    <div class="col-sm-6">
                                                                    <p><?= $trow['med_advice'] ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3">
                        <div class="card">
                            <div class="card-body">
                                <table class="table nowrap">
                                    <thead>
                                        <th>Date</th>
                                        <th>Treatment</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $tresult = $conn->query("SELECT * FROM appointment WHERE patient_id = $patient_id ORDER BY app_date DESC");
                                        if ($tresult->num_rows == 0) {
                                            echo '<td colspan="2">No Record Found</td>';
                                        } else {
                                            while ($trow = $tresult->fetch_assoc()) {
                                                ?>
                                                <tr>
                                                    <td><?= $trow['app_date'] ?></td>
                                                    <td><?= $trow['treatment_type'] ?></td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <!-- End Page Content -->
    </div>

   

    <script>
    function load_search_history() {
        var search_query = document.getElementById('nama_obat').value;

        if (search_query == '') {
            fetch("process_data.php", {
                method: "POST",
                body: JSON.stringify({
                    action: 'fetch'
                }),
                headers: {
                    'Content-type': 'application/json; charset=UTF-8'
                }
            }).then(function(response) {
                return response.json();
            }).then(function(responseData) {
                if (responseData.length > 0) {
                    var html = '<ul class="list-group">';
                    html += '<li class="list-group-item d-flex justify-content-between align-items-center"><b class="text-primary"><i>Your Recent Searches</i></b></li>';
                    for (var count = 0; count < responseData.length; count++) {
                        html += '<li class="list-group-item text-muted" style="cursor:pointer"><i class="fas fa-history mr-3"></i><span onclick="get_text(this)">' + responseData[count].search_query + '</span> <i class="far fa-trash-alt float-right mt-1" onclick="delete_search_history(' + responseData[count].id + ')"></i></li>';
                    }
                    html += '</ul>';
                    var resultElement = document.getElementById('nama_obat_result');
                    if (resultElement) {
                        resultElement.innerHTML = html;
                    }
                }
            });
        }
    }

    function get_text(event) {
        var string = event.textContent;

        fetch("process_data.php", {
            method: "POST",
            body: JSON.stringify({
                search_query: string
            }),
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        }).then(function(response) {
            return response.json();
        }).then(function(responseData) {
            document.getElementById('nama_obat').value = string;
            var resultElement = document.getElementById('nama_obat_result');
            if (resultElement) {
                resultElement.innerHTML = '';
            }
        });
    }

    function load_data(query) {
    if (query.length > 0) { // Mengubah dari 2 menjadi 0 agar mulai dari 1 karakter
        var form_data = new FormData();
        form_data.append('query', query);

        var ajax_request = new XMLHttpRequest();
        ajax_request.open('POST', 'process_data.php');
        ajax_request.send(form_data);

        ajax_request.onreadystatechange = function() {
            if (ajax_request.readyState == 4 && ajax_request.status == 200) {
                try {
                    var response = JSON.parse(ajax_request.responseText);
                    var html = '<div class="list-group">';
                    if (response.length > 0) {
                        for (var count = 0; count < response.length; count++) {
                            html += '<a href="#" class="list-group-item list-group-item-action" onclick="get_text(this)">' + response[count].nama_obat + '</a>';
                        }
                    } else {
                        html += '<a href="#" class="list-group-item list-group-item-action disabled">No Data Found</a>';
                    }
                    html += '</div>';
                    var resultElement = document.getElementById('nama_obat_result');
                    if (resultElement) {
                        resultElement.innerHTML = html;
                    }
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                }
            }
        }
    } else {
        var resultElement = document.getElementById('nama_obat_result');
        if (resultElement) {
            resultElement.innerHTML = '';
        }
    }
}

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi DateTimePicker jika diperlukan, pastikan elemen input ada
        if (document.getElementById('dateTimePickerInput')) {
            $('#dateTimePickerInput').datetimepicker();
        }
    });
</script>

    <?php include JS_PATH; ?>
    <?php if ($success): ?>
        <script>
        // Tangkap encrypted ID dari PHP
        var encryptedId = "<?php echo htmlspecialchars($encrypted_id); ?>";

        Swal.fire({
            title: "Berhasil!",
            text: "Data resep berhasil disimpan.",
            icon: "success",
            confirmButtonText: "OK"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "appointment-view.php?id=" + encryptedId; // Tambahkan encrypted ID ke URL
            }
        });
    </script>
    <?php endif; ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script type="text/javascript" src="autocomplete.js"></script>
    
    
</body>
</html>