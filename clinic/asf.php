<?php
require_once('../config/autoload.php');
include('includes/path.inc.php');
include('includes/session.inc.php');
include(SELECT_HELPER);

// Kode untuk mengambil data business_hour
$clinic_id = $clinic_row['clinic_id'];
$business_hours = mysqli_query($conn, "SELECT * FROM business_hour WHERE clinic_id = $clinic_id ORDER BY days_id");

$days = [
    1 => 'Monday - Friday',
    6 => 'Saturday',
    7 => 'Sunday'
];

// Inisialisasi variabel untuk jam operasional
$open_times = [];
$close_times = [];

// Mengelompokkan hari kerja (Senin hingga Jumat) dalam satu entri
$weekdays = [
    'open_time' => '',
    'close_time' => ''
];

while ($hour_row = mysqli_fetch_assoc($business_hours)) {
    $days_id = $hour_row['days_id'];
    if ($days_id >= 1 && $days_id <= 5) {
        // Jika belum diatur, atur nilainya
        if (empty($weekdays['open_time']) && empty($weekdays['close_time'])) {
            $weekdays['open_time'] = $hour_row["open_time"];
            $weekdays['close_time'] = $hour_row["close_time"];
        }
    } else {
        $open_times[$days_id] = $hour_row["open_time"];
        $close_times[$days_id] = $hour_row["close_time"];
    }
}

// Default value jika tidak ada data untuk hari tersebut
for ($i = 6; $i <= 7; $i++) {
    if (!isset($open_times[$i])) {
        $open_times[$i] = '';
        $close_times[$i] = '';
    }
}

// Inisialisasi nilai weekdays di luar loop
$open_times[1] = $weekdays['open_time'];
$close_times[1] = $weekdays['close_time'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include CSS_PATH; ?>

    <!-- CSS untuk menonaktifkan pilihan menit -->
    <style>
    /* Nonaktifkan pilihan menit */
    input[type="time"]::-webkit-datetime-edit-ampm-field,
    input[type="time"]::-webkit-datetime-edit-minute-field {
        display: none;
    }
    input[type="time"]::-webkit-inner-spin-button {
        display: none;
    }
    input[type="time"]::-webkit-clear-button {
        display: none;
    }
</style>


</head>

<body>
	<?php include NAVIGATION; ?>
	<div class="page-content" id="content">
		<?php include HEADER; ?>
		<?php
			$errName = $errContact = $errEmail = $errURL  = $errAddress = $errCity = $errState = $errZipcode = "";
			$className = $classContact = $classEmail = $classURL = $classAddress = $classCity = $classState = $classZipcode = "";
			
			if (isset($_POST["savebtn"])) {
				$clinic_name = escape_input($_POST["inputClinicName"]);
				$contact = escape_input($_POST["inputContact"]);
				$email = escape_input($_POST["inputEmailAddress"]);
				$url = escape_input($_POST["inputURL"]);
			
				$opensweek = escape_input($_POST["inputOpensHourWeek"]);
				$closeweek = escape_input($_POST["inputCloseHourWeek"]);
			
				$openssat = escape_input($_POST["inputOpensHourSat"]);
				$closesat = escape_input($_POST["inputCloseHourSat"]);
			
				$openssun = escape_input($_POST["inputOpensHourSun"]);
				$closesun = escape_input($_POST["inputCloseHourSun"]);
			
				$address = escape_input($_POST["inputAddress"]);
				$city = escape_input($_POST["inputCity"]);
				if (isset($_POST['inputState'])) {
					$state = escape_input($_POST['inputState']);
				}
				$zipcode = escape_input($_POST["inputZipCode"]);

                 
                
			
				// Validate
				if (empty($clinic_name)) {
					$errName = $error_html['errFirstName'];
					$className = $error_html['errClass'];
				} else {
					if (!preg_match($regrex['text'], $clinic_name)) {
						$errName = $error_html['invalidText'];
						$className = $error_html['errClass'];
					}
				}
			
				if (empty($url)) {
					$errURL = $error_html['errURL'];
					$classURL = $error_html['errClass'];
				} else {
					if (!filter_var($url, FILTER_VALIDATE_URL)) {
						$errURL =  $error_html['invalidURL'];
						$classURL = $error_html['errClass'];
					}
				}
			
				if (empty($email)) {
					$errEmail = $error_html['errEmail'];
					$classEmail = $error_html['errClass'];
				} else {
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$errEmail =  $error_html['invalidEmail'];
						$classEmail = $error_html['errClass'];
					}
				}
			
				if (!empty($contact)) {
                    if (!preg_match($regrex['contact'], $contact)) {
                        $errContact = $error_html['invalidContact'];
                        $classContact = $error_html['errClass'];
                    }
                }
                
				if (empty($address)) {
					$errAddress = $error_html['errAddress'];
					$classAddress = $error_html['errClass'];
				} 
				// else {
				// 	if (!preg_match($regrex['text'], $address)) {
				// 		$errAddress = $error_html['invalidText'];
				// 		$classAddress = $error_html['errClass'];
				// 	}
				// }
			
				if (empty($city)) {
					$errCity = $error_html['errCity'];
					$classCity = $error_html['errClass'];
				} else {
					if (!preg_match($regrex['text'], $city)) {
						$errCity = $error_html['invalidText'];
						$classCity = $error_html['errClass'];
					}
				}
			
				if (empty($zipcode)) {
					$errZipcode = $error_html['errZipcode'];
					$classZipcode = $error_html['errClass'];
				} else {
					if (!filter_var($zipcode, FILTER_VALIDATE_INT)) {
						$errZipcode = $error_html['invalidInt'];
						$errZipcode = $error_html['errClass'];
					}
				}
			
				if (empty($state)) {
					$errState = $error_html['errState'];
					$classState = $error_html['errClass'];
				}
			
				if (multi_empty($errName,  $errURL, $errEmail, $errAddress, $errCity, $errState, $errZipcode)) {
					$clinicstmt = $conn->prepare("UPDATE clinics SET clinic_name = ?, clinic_email = ?, clinic_url = ?,  clinic_address = ?, clinic_city = ?, clinic_state = ?, clinic_zipcode = ? WHERE clinic_id = ?");
					$clinicstmt->bind_param("sssssssi", $clinic_name, $email, $url,  $address, $city, $state, $zipcode, $clinic_row['clinic_id']);
			
					// Inisialisasi status eksekusi
    $update_success = true;

    // Mengupdate days_id 1 hingga 5
    $clinic_id = $clinic_row['clinic_id'];
    $opensweek = escape_input($_POST["inputOpensHourWeek"]);
    $closeweek = escape_input($_POST["inputCloseHourWeek"]);
    for ($days_id = 1; $days_id <= 5; $days_id++) {
        $hourstmt = $conn->prepare("UPDATE business_hour SET open_time = ?, close_time = ? WHERE clinic_id = ? AND days_id = ?");
        $hourstmt->bind_param("ssii", $opensweek, $closeweek, $clinic_id, $days_id);
        if (!$hourstmt->execute()) {
            $update_success = false;
            break;
        }
    }

    // Mengupdate hari Sabtu (days_id 6)
    if ($update_success) {
        $openssat = escape_input($_POST["inputOpensHourSat"]);
        $closesat = escape_input($_POST["inputCloseHourSat"]);
        $hourstmt = $conn->prepare("UPDATE business_hour SET open_time = ?, close_time = ? WHERE clinic_id = ? AND days_id = 6");
        $hourstmt->bind_param("ssi", $openssat, $closesat, $clinic_id);
        if (!$hourstmt->execute()) {
            $update_success = false;
        }
    }

    // Mengupdate hari Minggu (days_id 7)
    if ($update_success) {
        $openssun = escape_input($_POST["inputOpensHourSun"]);
        $closesun = escape_input($_POST["inputCloseHourSun"]);
        $hourstmt = $conn->prepare("UPDATE business_hour SET open_time = ?, close_time = ? WHERE clinic_id = ? AND days_id = 7");
        $hourstmt->bind_param("ssi", $openssun, $closesun, $clinic_id);
        if (!$hourstmt->execute()) {
            $update_success = false;
        }
    }

    if ($clinicstmt->execute() && $update_success) {
        echo '<script>
            Swal.fire({ title: "Great!", text: "Record Updated!", type: "success" }).then((result) => {
                if (result.value) { window.location.href = "profile-edit.php"; }
            });
        </script>';
    } else {
        echo '<script>Swal.fire({ title: "Oops...!", text: "Something Happen!", type: "error" });</script>';
    }
}
			}
		?>
		<!-- Page content -->
		<div class="row">
			<div class="col-12">
				<form name="regform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
					<h5 class="card-title mt-3">
						Clinic Profile Info
					</h5>
					<div class="card">
						<div class="card-body">
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="inputDoctorID">Clinic ID #</label>
									<input type="text" name="inputClinicID" class="form-control" id="inputClinicID" readonly value="<?php echo $clinic_row["clinic_id"]; ?>">
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="inputClinicName">Clinic Name</label>
									<input type="text" name="inputClinicName" class="form-control <?= $className ?>" id="inputClinicName" placeholder="" value="<?php echo $clinic_row["clinic_name"]; ?>">
									<?= $errName; ?>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="inputContact">Contact Number</label>
									<input type="text" name="inputContact" class="form-control <?= $classContact ?>" id="inputContact" placeholder="" value="<?php echo $clinic_row["clinic_contact"]; ?>">
									<?= $errContact; ?>
								</div>
								<div class="form-group col-md-6">
									<label for="inputEmailAddress">Email Address</label>
									<input type="text" name="inputEmailAddress" class="form-control <?= $classEmail ?>" id="inputEmailAddress" placeholder="example@address.com" value="<?php echo $clinic_row["clinic_email"]; ?>">
									<?= $errEmail; ?>
								</div>
								<div class="form-group col-md-6">
									<label for="inputURL">URL</label>
									<input type="text" name="inputURL" class="form-control <?= $classURL ?>" id="inputURL" placeholder="www.example.com" value="<?php echo $clinic_row["clinic_url"]; ?>">
									<?= $errURL; ?>
								</div>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-body">
							<span class="card-title">Business Hour</span>
							<div class="mb-2">
								<small class="text-muted">When you're closed on a certain day, just leave the hours blank.</small>
								<small class="text-muted">Remember: 12PM is midday, 12AM is midnight</small>
							</div>
							
							<div class="form-group row">
    <label for="inputBusinessHourWeek" class="col-sm-2 col-form-label"><?= $days[1]; ?></label>
    <div class="col-sm-3">
        <input type="time" class="form-control timepicker" name="inputOpensHourWeek" value="<?= $open_times[1]; ?>" min="08:00" max="23:00">
    </div><span>--</span>
    <div class="col-sm-3">
        <input type="time" class="form-control timepicker" name="inputCloseHourWeek" value="<?= $close_times[1]; ?>" min="08:00" max="23:00">
    </div>
</div>

<div class="form-group row">
    <label for="inputBusinessHourSat" class="col-sm-2 col-form-label"><?= $days[6]; ?></label>
    <div class="col-sm-3">
        <input type="time" class="form-control timepicker" name="inputOpensHourSat" value="<?= $open_times[6]; ?>" min="08:00" max="23:00">
    </div><span>--</span>
    <div class="col-sm-3">
        <input type="time" class="form-control timepicker" name="inputCloseHourSat" value="<?= $close_times[6]; ?>" min="08:00" max="23:00">
    </div>
</div>

<div class="form-group row">
    <label for="inputBusinessHourSun" class="col-sm-2 col-form-label"><?= $days[7]; ?></label>
    <div class="col-sm-3">
        <input type="time" class="form-control timepicker" name="inputOpensHourSun" value="<?= $open_times[7]; ?>" min="08:00" max="23:00">
    </div><span>--</span>
    <div class="col-sm-3">
        <input type="time" class="form-control timepicker" name="inputCloseHourSun" value="<?= $close_times[7]; ?>" min="08:00" max="23:00">
    </div>
</div>



    </div>
</div>

					<div class="card">
						<div class="card-body">
							<div class="form-group">
								<label for="inputAddress">Address</label>
								<input type="text" name="inputAddress" class="form-control <?= $classAddress ?>" id="inputAddress" placeholder="1234 Main St" value="<?php echo $clinic_row["clinic_address"]; ?>">
								<?= $errAddress; ?>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="inputCity">City</label>
									<input type="text" name="inputCity" class="form-control <?= $classCity ?>" id="inputCity" value="<?php echo $clinic_row["clinic_city"]; ?>">
									<?= $errCity; ?>
								</div>
								<div class="form-group col-md-4">
									<label for="inputState">State</label>
									<select name="inputState" id="inputState" class="form-control selectpicker <?= $classState ?>" data-live-search="true">
										<option value="" selected disabled>Choose</option>
										<?php foreach ($select_state as $state_value) {
											if ($clinic_row["clinic_state"] == "$state_value") {
												$selected = "selected";
											} else {
												$selected = "";
											}
											echo '<option value="' . $state_value . '"' . $selected . '>' . $state_value . '</option>';
										} ?>
									</select>
									<?= $errState; ?>
								</div>
								<div class="form-group col-md-2">
									<label for="inputZipCode">Zip Code</label>
									<input type="text" name="inputZipCode" class="form-control <?= $classZipcode ?>" id="inputZipCode" value="<?php echo $clinic_row["clinic_zipcode"]; ?>">
									<?= $errZipcode; ?>
								</div>
							</div>
						</div>
					</div>

					<div class="mb-3 mt-3">
						<button type="submit" class="btn btn-primary btn-block" name="savebtn">Save</button>
					</div>
				</form>
			</div>
			
			<div class="col-12">
				<hr>
				<h5 class="card-title mt-3">
					Clinic Cover Image
				</h5>
				<form name="imgform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
					<div class="card">
						<div class="card-body">
							<div class="input-group mb-3">
								<div class="custom-file">
									<input type="file" name="inputImageUpload[]" class="custom-file-input" id="inputImageUpload" multiple>
									<label class="custom-file-label" for="inputImageUpload">Choose file</label>
								</div>
								<div class="input-group-prepend">
									<button type="submit" name="uploadbtn" class="btn btn-primary btn-sm px-4" id="inputGroupFileImage">Upload</button>
								</div>
							</div>

							<div class="row">
								<?php
								$table_result = mysqli_query($conn, "SELECT * FROM clinic_images WHERE clinic_id = " . $clinic_row['clinic_id'] . "");
								$count = mysqli_num_rows($table_result);
								if ($count == 0) {
									echo '<div class="col mt-2">
								<div class="text-center">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-octagon"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
								<h6 class="mt-2">No Image Available</h6>
								</div></div>';
								} else {
									while ($table_row = mysqli_fetch_assoc($table_result)) {
										if (!empty($table_row["clinicimg_filename"])) {
											echo '<div class="col-sm-3">
										<img src="../uploads/' . $clinic_row["clinic_id"] . '/clinic/' . $table_row["clinicimg_filename"] . '" class="img-thumbnail" width="300px" alt="">
										</div>';
										} else {
											echo '<div class="col-sm-3">
										<img src="../assets/img/empty/empty-image.png" class="img-thumbnail" width="300px" alt="">
										</div>';
										}
									}
								}
								?>
							</div>

						</div>
					</div>
				</form>
			</div>

		</div>
		<!-- End Page Content -->
	</div>
	<?php include JS_PATH; ?>
	<script>
		$(function() {
			$('.timepicker').datetimepicker({
				format: 'HH:mm'
			});
		});
	</script>
	<script>
		// $('#add').on('click', add);
		// $('#remove').on('click', remove);

		// function add() {
		//     var new_chq_no = parseInt($('#total_chq').val()) + 1;
		//     var new_input = '<div class="form-group row" id=new_"' + new_chq_no + '">\
		//                 <label for="inputBusinessHour" class="col-sm-2 col-form-label">Tuesday</label>\
		//                 <div class="col-sm-2">\
		//                     <input type="text" class="form-control" id="inputBusinessHour">\
		//                 </div>\
		//                 <div class="col-sm-2">\
		//                     <select name="" class="form-control" id="">\
		//                         <option value="am">AM</option>\
		//                         <option value="pm">PM</option>\
		//                     </select>\
		//                 </div>\
		//                 <div class="col-sm-2">\
		//                     <input type="text" class="form-control" id="inputBusinessHour">\
		//                 </div>\
		//                 <div class="col-sm-2">\
		//                     <select name="" class="form-control" id="">\
		//                         <option value="am">AM</option>\
		//                         <option value="pm">PM</option>\
		//                     </select>\
		//                 </div>\
		//             </div>';
		//     $('#new_chq').append(new_input);
		//     $('#total_chq').val(new_chq_no);
		// }

		// function remove() {
		//     var last_chq_no = $('#total_chq').val();
		//     if (last_chq_no > 1) {
		//         $('#new_' + last_chq_no).remove();
		//         $('#total_chq').val(last_chq_no - 1);
		//     }
		// }
	</script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeInputs = document.querySelectorAll('input[type="time"]');
        timeInputs.forEach(input => {
            input.addEventListener('input', function() {
                validateTimeInput(input);
            });

            input.addEventListener('blur', function() {
                validateTimeInput(input);
            });
        });
    });

    function validateTimeInput(input) {
        const value = input.value;
        const [hour, minute] = value.split(':').map(Number);

        if (minute !== 0) {
            input.value = `${String(hour).padStart(2, '0')}:00`;
            input.setCustomValidity('Menit tidak bisa dipilih, hanya jam.');
        } else if (hour < 8 || hour > 23) {
            input.setCustomValidity('Jam harus antara 08:00 dan 23:00.');
        } else {
            input.setCustomValidity('');
        }
        input.reportValidity();
    }
</script>




</body>

</html>
<?php
if (isset($_POST["uploadbtn"])) {
	$targetDir = "../uploads/" . $clinic_row['clinic_id'] . "/clinic" . "/";
	$allowTypes = array('jpg', 'png', 'jpeg');

	$statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = "";
	if (!empty(array_filter($_FILES['inputImageUpload']['name']))) {
		foreach ($_FILES['inputImageUpload']['name'] as $key => $value) {
			// File upload path
			$fileName = basename($_FILES['inputImageUpload']['name'][$key]);
			$targetFilePath = $targetDir . $fileName;

			$folderpath = "../uploads/" . $clinic_row['clinic_id'] . "/clinic" . "/";
			if (!file_exists($folderpath)) {
				mkdir($folderpath, 0777, true);
			}

			// Check whether file type is valid
			$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
			if (in_array($fileType, $allowTypes)) {
				// Upload file to server
				if (move_uploaded_file($_FILES["inputImageUpload"]["tmp_name"][$key], $targetFilePath)) {
					// Image db insert sql
					$insertValuesSQL .= "('" . $fileName . "', '".$clinic_row['clinic_id']."'),";
				} else {
					$errorUpload .= $_FILES['inputImageUpload']['name'][$key] . ', ';
				}
			}
		}

		if (!empty($insertValuesSQL)) {
			$insertValuesSQL = trim($insertValuesSQL, ',');
			$insert = $conn->query("INSERT INTO clinic_images (clinicimg_filename, clinic_id) VALUES $insertValuesSQL");
			if ($insert) {
				$errorUpload = !empty($errorUpload) ? 'Upload Error: ' . $errorUpload : '';
				$errorUploadType = !empty($errorUploadType) ? 'File Type Error: ' . $errorUploadType : '';
				$errorMsg = !empty($errorUpload) ? '<br/>' . $errorUpload . '<br/>' . $errorUploadType : '<br/>' . $errorUploadType;
				echo "<script>Swal.fire('Great!','Images are uploaded successfully!','success').then((result) => { if (result.value) { window.location.href = 'profile-edit.php'; } });</script>";
			} else {
				echo "<script>Swal.fire('Oops...','there was an error uploading your file.','error')</script>";
			}
		}
	} else {
		echo "<script>Swal.fire('Oops...','Please upload a file.','error').then((result) => { if (result.value) { window.location.href = 'profile-edit.php'; } });</script>";
	}
}
?>