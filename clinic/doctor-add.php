<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

include(SELECT_HELPER);
include(EMAIL_HELPER);

$clinic_id = $_SESSION["clinic_id"];
error_log("Debug: clinic_id from session: " . $clinic_id);

$stmt = $conn->prepare("SELECT clinic_name, clinic_status FROM clinics WHERE clinic_id = ?");
$stmt->bind_param("i", $clinic_id);
$stmt->execute();
$result = $stmt->get_result();
$clinic_row = $result->fetch_assoc();
$clinic_name = $clinic_row['clinic_name'];
$clinic_status = $clinic_row['clinic_status'];

error_log("Debug: clinic_name: " . $clinic_name . ", clinic_status: " . $clinic_status);

$errClinic = $errFName = $errLName = $errSpec = $errYears = $errSpoke = $errGender = $errEmail = $errContact = $errPassword = $errConfirmPassword = "";
$classClinic = $classFName = $classLName = $classSpec = $classYears = $classSpoke = $classGender = $classEmail = $classContact = $classPassword = $classConfirmPassword = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    error_log("Debug: Form is submitted using POST method");

    $fname = escape_input($_POST['inputFirstName']);
    $lname = escape_input($_POST['inputLastName']);
    $speciality = isset($_POST['inputSpeciality']) ? escape_input($_POST['inputSpeciality']) : '';
    $years = escape_input($_POST['inputYrsExp']);
    $desc = escape_input($_POST['inputDesc']);
    $lang = isset($_POST['inputLanguages']) ? $_POST['inputLanguages'] : [];
    $spoke = implode(",", $lang);
    $dob = escape_input($_POST['inputDOB']);
    $gender = isset($_POST['inputGender']) ? escape_input($_POST['inputGender']) : '';
    $email = escape_input($_POST['inputEmailAddress']);
    $contact = escape_input($_POST['inputContactNumber']);
    $password = escape_input($_POST['inputPassword']);
    $confirm_password = escape_input($_POST['inputConfirmPassword']);

    error_log("Debug: Received data - fname: $fname, lname: $lname, speciality: $speciality, years: $years, desc: $desc, spoke: $spoke, dob: $dob, gender: $gender, email: $email, contact: $contact, password: [HIDDEN], confirm_password: [HIDDEN]");

    if (multi_empty($errFName, $errLName, $errSpec, $errYears, $errSpoke, $errGender, $errEmail, $errContact, $errPassword, $errConfirmPassword)) {
        error_log("Debug: All validations passed");

        $kode_dokter = generateKodeDokter($conn, $clinic_id);
        error_log("Debug: Generated kode_dokter: " . $kode_dokter);

        $token = generateCode(6);
        $en_token = md5($token);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO doctors (doctor_firstname, doctor_lastname, doctor_speciality, doctor_experience, doctor_desc, doctor_spoke, doctor_gender, doctor_dob, doctor_email, doctor_contact, date_created, clinic_id, doctor_password, kode_dokter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssissssssiss", $fname, $lname, $speciality, $years, $desc, $spoke, $gender, $dob, $email, $contact, $date_created, $clinic_id, $hashedPassword, $kode_dokter);
        
        if ($stmt->execute()) {
            error_log("Debug: Doctor data inserted successfully. Last inserted ID: " . $conn->insert_id);

            $last_id = $conn->insert_id;
            mysqli_query($conn, "INSERT INTO treatment_type (treatment_name, doctor_id) VALUES ('Pasien Baru', $last_id)");

            for ($day_id = 1; $day_id <= 7; $day_id++) {
                for ($session_id = 1; $session_id <= 65; $session_id++) {
                    $stmt_availabilities = $conn->prepare("INSERT INTO doctor_availabilities (doctor_id, day_id, session_id, available) VALUES (?, ?, ?, ?)");
                    $default_available = 0; // FALSE
                    $stmt_availabilities->bind_param("iiii", $last_id, $day_id, $session_id, $default_available);
                    $stmt_availabilities->execute();
                }
            }

            // Redirect after successful insert to prevent double submission
            header("Location: doctor-list.php?success=1");
            exit();
        } else {
            error_log("Error: Failed to insert doctor data. Error: " . $stmt->error);
            echo 'Ada yang salah';
        }
        $stmt->close();
    } else {
        error_log("Debug: Validation failed - errors exist");
    }
}
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
                <form name="regform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="d-flex">
                        <div class="card col-md-12">
                            <div class="card-body">
                                <!-- Tambah Dokter -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputClinic">Klinik</label>
                                        <select name="inputClinic" id="inputClinic" class="form-control selectpicker <?= $classClinic ?>" data-live-search="true" disabled>
                                            <option value="<?= $clinic_id ?>" selected><?= $clinic_id . ' ' . $clinic_name ?></option>
                                        </select>
                                        <?= $errClinic ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputFirstName">Nama Depan</label>
                                        <input type="text" name="inputFirstName" class="form-control <?php echo $classFName ?>" id="inputFirstName" placeholder="Masukkan Nama Depan" value="<?= isset($fname) ? $fname : '' ?>">
                                        <?php echo $errFName; ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputLastName">Nama Belakang</label>
                                        <input type="text" name="inputLastName" class="form-control <?php echo $classLName ?>" id="inputLastName" placeholder="Masukkan Nama Belakang" value="<?= isset($lname) ? $lname : '' ?>">
                                        <?php echo $errLName; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputSpeciality">Spesialisasi</label>
                                        <select name="inputSpeciality" id="inputSpeciality" class="form-control selectpicker <?= $classSpec ?>" data-live-search="true">
                                            <option value="" selected disabled>Pilih</option>
                                            <?php
                                            $table_result = mysqli_query($conn, "SELECT * FROM speciality");
                                            while ($table_row = mysqli_fetch_assoc($table_result)) {
                                                echo '<option value="' . $table_row["speciality_id"] . '" ' . (isset($speciality) && $speciality == $table_row["speciality_id"] ? 'selected' : '') . '>' . $table_row["speciality_name"] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <?= $errSpec ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputYrsExp">Tahun Pengalaman</label>
                                        <input type="text" name="inputYrsExp" class="form-control <?= $classYears ?>" id="inputYrsExp" placeholder="Masukkan Tahun Pengalaman" value="<?= isset($years) ? $years : '' ?>">
                                        <?= $errYears ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword">Kata Sandi</label>
                                        <input type="password" name="inputPassword" class="form-control <?= $classPassword ?>" id="inputPassword" placeholder="Masukkan Kata Sandi" value="<?= isset($password) ? $password : '' ?>">
                                        <?= $errPassword ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputConfirmPassword">Konfirmasi Kata Sandi</label>
                                        <input type="password" name="inputConfirmPassword" class="form-control <?= $classConfirmPassword ?>" id="inputConfirmPassword" placeholder="Konfirmasi Kata Sandi" value="<?= isset($confirm_password) ? $confirm_password : '' ?>">
                                        <?= $errConfirmPassword ?>
                                    </div>
                                </div>
                                <!-- Akhir Tambah Dokter -->
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputLanguages">Bahasa </label>
                                <div class="row">
                                    <?php $i = 1;
                                    foreach ($select_lang as $lang_value) {
                                        echo
                                            '<div class="col">
                                            <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="inputLanguages[]" id="customCheck' . $i . '" class="custom-control-input ' . $classSpoke . '" value="' . $lang_value . '" ' . (isset($lang) && in_array($lang_value, $lang) ? 'checked' : '') . '>
                                            <label class="custom-control-label" for="customCheck' . $i . '">' . $lang_value . '</label>
                                        </div></div>';
                                        $i++;
                                    } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputDesc">Deskripsi</label>
                                <textarea class="form-control" id="inputDesc" name="inputDesc" rows="3"><?= isset($desc) ? $desc : '' ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputDOB">Tanggal Lahir</label>
                                    <input type="text" name="inputDOB" class="form-control" id="datepicker" placeholder="Masukkan Tanggal Lahir" value="<?= isset($dob) ? $dob : '' ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputGender">Jenis Kelamin</label>
                                    <div class="row">
                                        <div class="col">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="inputGenderMale" name="inputGender" class="custom-control-input <?= $classGender ?>" value="Laki-laki" <?= isset($gender) && $gender == 'Laki-laki' ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="inputGenderMale">Laki-laki</label>
                                                <?= $errGender ?>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="inputGenderFemale" name="inputGender" class="custom-control-input <?= $classGender ?>" value="Perempuan" <?= isset($gender) && $gender == 'Perempuan' ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="inputGenderFemale">Perempuan</label>
                                                <?= $errGender ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputContactNumber">Nomor Kontak</label>
                                    <input type="tel" name="inputContactNumber" class="form-control <?= $classContact ?>" id="inputContactNumber" placeholder="Masukkan Nomor Telepon" pattern="[0-9]*" inputmode="numeric" value="<?= isset($contact) ? $contact : '' ?>">
                                    <?= $errContact ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmailAddress">Alamat Email</label>
                                    <input type="text" name="inputEmailAddress" class="form-control <?= $classEmail ?>" id="inputEmailAddress" placeholder="Masukkan Alamat Email" value="<?= isset($email) ? $email : '' ?>">
                                    <?= $errEmail ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="reset" class="btn btn-outline-secondary btn-block">Bersihkan Input</button>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block" name="savebtn">Tambah Dokter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include JS_PATH; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Dokter Baru Ditambahkan!',
                icon: 'success'
            });
        }
    });

    $('#datepicker').on('changeDate', function() {
        var date = $(this).datepicker('getDate'),
            year = date.getFullYear(),
            current_year = new Date().getFullYear(),
            totalyear = current_year - year;
        $('#inputAge').val(totalyear);
    });

    $('#inputIC').on('keyup', function() {
        var input = $(this).val(),
            lastnum = input % 10;
        if (lastnum % 2 === 0) {
            $("#inputGenderFemale").prop("checked", true);
        } else {
            $("#inputGenderMale").prop("checked", true);
        }
    });
    </script>
</body>
</html>

<?php
function generateKodeDokter($conn, $clinic_id) {
    $query = "SELECT kode_dokter FROM doctors WHERE clinic_id = ? ORDER BY kode_dokter DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $clinic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_kode = $result->fetch_assoc()['kode_dokter'];

    if ($last_kode) {
        $num = (int) substr($last_kode, 1) + 1;
        $new_kode = 'D' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $new_kode = 'D001';
    }

    return $new_kode;
}

if (isset($_POST["savebtn"])) {
    if (multi_empty($errFName, $errLName, $errSpec, $errYears, $errSpoke, $errGender, $errEmail, $errContact, $errPassword, $errConfirmPassword)) {

        $kode_dokter = generateKodeDokter($conn, $clinic_id);

        $token = generateCode(6);
        $en_token = md5($token);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO doctors (doctor_firstname, doctor_lastname, doctor_speciality, doctor_experience, doctor_desc, doctor_spoke, doctor_gender, doctor_dob, doctor_email, doctor_contact, date_created, clinic_id, doctor_password, kode_dokter) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssissssssiss", $fname, $lname, $speciality, $years, $desc, $spoke, $gender, $dob, $email, $contact, $date_created, $clinic_id, $hashedPassword, $kode_dokter);
        
        if ($stmt->execute()) {

            $last_id = $conn->insert_id;
            mysqli_query($conn,"INSERT INTO treatment_type (treatment_name, doctor_id) VALUES ('Pasien Baru', $last_id) ");

            for ($day_id = 1; $day_id <= 7; $day_id++) {
                for ($session_id = 1; $session_id <= 65; $session_id++) {
                    $stmt_availabilities = $conn->prepare("INSERT INTO doctor_availabilities (doctor_id, day_id, session_id, available) VALUES (?, ?, ?, ?)");
                    $default_available = 0; // FALSE
                    $stmt_availabilities->bind_param("iiii", $last_id, $day_id, $session_id, $default_available);
                    $stmt_availabilities->execute();
                }
            }

            // Redirect after successful insert to prevent double submission
            header("Location: doctor-list.php?success=1");
            exit();
        } else {
            error_log("Error: Failed to insert doctor data. Error: " . $stmt->error);
            echo 'Ada yang salah';
        }
        $stmt->close();
    }
}
?>
