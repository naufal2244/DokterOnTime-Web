<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

include(SELECT_HELPER);
include(EMAIL_HELPER);

$errClinic = $errFName = $errLName = $errSpec = $errYears = $errFee = $errSpoke = $errGender = $errEmail = $errContact = $errImage = $errPassword = $errConfirmPassword = "";
$classClinic = $classFName = $classLName = $classSpec = $classYears = $classFee = $classSpoke = $classGender = $classEmail = $classContact = $classPassword = $classConfirmPassword = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['inputClinic'])) {
        $clinic_id = escape_input($_POST['inputClinic']);
    }
    $fname       = escape_input($_POST['inputFirstName']);
    $lname       = escape_input($_POST['inputLastName']);
    if (isset($_POST['inputSpeciality'])) {
        $speciality = escape_input($_POST['inputSpeciality']);
    }
    $years      = escape_input($_POST['inputYrsExp']);
    $fees      = escape_input($_POST['inputFee']);
    $desc       = escape_input($_POST['inputDesc']);
    if (isset($_POST['inputLanguages'])) {
        $lang = $_POST['inputLanguages'];
        $spoke = implode(",", $lang);
    }
    $dob        = escape_input($_POST['inputDOB']);
    if (isset($_POST['inputGender'])) {
        $gender     = escape_input($_POST['inputGender']);
    }
    $email      = escape_input($_POST['inputEmailAddress']);
    $contact    = escape_input($_POST['inputContactNumber']);
    $password   = escape_input($_POST['inputPassword']);
    $confirm_password = escape_input($_POST['inputConfirmPassword']);

    if (empty($fname)) {
        $errFName = $error_html['errFirstName'];
        $classFName = $error_html['errClass'];
    } else {
        if (!preg_match($regrex['text'], $fname)) {
            $errFName = $error_html['invalidText'];
            $classFName = $error_html['errClass'];
        }
    }

    if (empty($lname)) {
        $errLName = $error_html['errLastName'];
        $classLName = $error_html['errClass'];
    } else {
        if (!preg_match($regrex['text'], $lname)) {
            $errFName = $error_html['invalidText'];
            $classFName = $error_html['errClass'];
        }
    }

    if (empty($speciality)) {
        $errSpec = $error_html['errSpec'];
        $classSpec = $error_html['errClass'];
    }
    
    if (empty($clinic_id)) {
        $errClinic = "Clinic is required";
        $classClinic = $error_html['errClass'];
    }

    if (empty($years)) {
        $errYears = $error_html['errYears'];
        $classYears = $error_html['errClass'];
    } else {
        if (!filter_var($years, FILTER_VALIDATE_INT)) {
            $errYears = $error_html['invalidInt'];
            $classYears = $error_html['errClass'];
        }
    }
    
    if (empty($fees)) {
        $errFee = $error_html['errFee'];
        $classFee = $error_html['errClass'];
    } else {
        if (!filter_var($fees, FILTER_VALIDATE_INT)) {
            $errFee = $error_html['invalidInt'];
            $classFee = $error_html['errClass'];
        }
    }

    if (empty($lang)) {
        $errSpoke = $error_html['errSpoke'];
        $classSpoke = $error_html['errClass'];
    }
    if (empty($gender)) {
        $errGender = $error_html['errGender'];
        $classGender = $error_html['errClass'];
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

  
    
    if (empty($password)) {
        $errPassword = "Password harus diisi";
        $classPassword = "invalid";
    } else if (strlen($password) < 6) {
        $errPassword = "Password harus memiliki panjang minimal 6 karakter";
        $classPassword = "invalid";
    }

    if (empty($confirm_password)) {
        $errConfirmPassword = "Mohon konfirmasi password";
        $classConfirmPassword = "invalid";
    } else if ($password !== $confirm_password) {
        $errConfirmPassword = "Password tidak cocok";
        $classConfirmPassword = "invalid";
    }

    if (empty($_FILES['inputAvatar']['name'])) {
        $errImage = "Image harus diisi";
        $classImage = "invalid";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
    <style>
        .imageupload .btn-file {
            overflow: hidden;
            position: relative;
        }

        .imageupload .btn-file input[type="file"] {
            cursor: inherit;
            display: block;
            font-size: 100px;
            min-height: 100%;
            min-width: 100%;
            opacity: 0;
            position: absolute;
            right: 0;
            text-align: right;
            top: 0;
        }

        .imageupload .thumbnail {
            margin-bottom: 10px;
        }

        .imageupload .invalid {
            border: 1px solid red;
        }
    </style>
</head>

    <body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <!-- Page content -->
        <div class="row">
            <div class="col-12">
                <form name="regform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="d-flex">
                        <div class="card col-md-9">
                            <div class="card-body">
                                <!-- Tambah Dokter -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputClinic">Klinik</label>
                                        <select name="inputClinic" id="inputClinic" class="form-control selectpicker <?= $classClinic ?>" data-live-search="true">
                                            <option value="" selected disabled>Pilih</option>
                                            <?php
                                            $table_result = mysqli_query($conn, "SELECT * FROM clinics");
                                            while ($table_row = mysqli_fetch_assoc($table_result)) {
                                                echo '<option value="' . $table_row["clinic_id"] . '">'. $table_row["clinic_id"] .' '. $table_row["clinic_name"] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <?= $errClinic ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputFirstName">Nama Depan</label>
                                        <input type="text" name="inputFirstName" class="form-control <?php echo $classFName ?>" id="inputFirstName" placeholder="Masukkan Nama Depan">
                                        <?php echo $errFName; ?>
                                    </div>
                                    <div class="form-group col-md6">
                                        <label for="inputLastName">Nama Belakang</label>
                                        <input type="text" name="inputLastName" class="form-control <?php echo $classLName ?>" id="inputLastName" placeholder="Masukkan Nama Belakang">
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
                                                echo '<option value="' . $table_row["speciality_id"] . '">' . $table_row["speciality_name"] . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <?= $errSpec ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputYrsExp">Tahun Pengalaman</label>
                                        <input type="text" name="inputYrsExp" class="form-control <?= $classYears ?>" id="inputYrsExp" placeholder="Masukkan Tahun Pengalaman">
                                        <?= $errYears ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputFee">Biaya Konsultasi</label>
                                        <input type="text" name="inputFee" class="form-control <?= $classFee ?>" id="inputFee" placeholder="Masukkan Biaya Konsultasi">
                                        <?= $errFee ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword">Kata Sandi</label>
                                        <input type="password" name="inputPassword" class="form-control <?= $classPassword ?>" id="inputPassword" placeholder="Masukkan Kata Sandi">
                                        <?= $errPassword ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputConfirmPassword">Konfirmasi Kata Sandi</label>
                                        <input type="password" name="inputConfirmPassword" class="form-control <?= $classConfirmPassword ?>" id="inputConfirmPassword" placeholder="Konfirmasi Kata Sandi">
                                        <?= $errConfirmPassword ?>
                                    </div>
                                </div>
                                <!-- Akhir Tambah Dokter -->
                            </div>
                        </div>

<div class="card col-md-3">
                            <div class="card-body">
                                <div class="imageupload">
                                    <small class="text-danger"><?= $errImage ?></small>
                                    <img src="../assets/img/empty/empty-avatar.jpg" id="output" class="img-fluid thumbnail <?= $classImage ?>" alt="Dokter-Avatar" title="Dokter-Avatar">
                                    <div class="file-tab">
                                        <label class="btn btn-sm btn-primary btn-block btn-file">
                                            <span>Cari</span>
                                            <input type="file" name="inputAvatar" id="inputAvatar" accept="image/*" onchange="openFile(event)">
                                        </label>
                                    </div>
                                </div>
                                <script>
                                    var openFile = function(file) {
                                        var input = file.target;

                                        var reader = new FileReader();
                                        reader.onload = function() {
                                            var dataURL = reader.result;
                                            var output = document.getElementById('output');
                                            output.src = dataURL;
                                        };
                                        reader.readAsDataURL(input.files[0]);
                                    };
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputLanguages">Bahasa yang Dibicarakan</label><small class="text-muted m-2">Pilih Bahasa yang Anda Bicarakan.</small>
                                <div class="row">
                                    <?php $i = 1;
                                    foreach ($select_lang as $lang_value) {
                                        echo
                                            '<div class="col">
                                            <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="inputLanguages[]" id="customCheck' . $i . '" class="custom-control-input ' . $classSpoke . '" value="' . $lang_value . '">
                                            <label class="custom-control-label" for="customCheck' . $i . '">' . $lang_value . '</label>
                                        </div></div>';
                                        $i++;
                                    } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputDesc">Deskripsi</label>
                                <textarea class="form-control" id="inputDesc" name="inputDesc" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputDOB">Tanggal Lahir</label>
                                    <input type="text" name="inputDOB" class="form-control" id="datepicker" placeholder="Masukkan Tanggal Lahir">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputGender">Jenis Kelamin</label>
                                    <div class="row">
                                        <div class="col">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="inputGenderMale" name="inputGender" class="custom-control-input <?= $classGender ?>" value="male">
                                                <label class="custom-control-label" for="inputGenderMale">Laki-laki</label>
                                                <?= $errGender ?>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="inputGenderFemale" name="inputGender" class="custom-control-input <?= $classGender ?>" value="female">
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
                                <input type="tel" name="inputContactNumber" class="form-control <?= $classContact ?>" id="inputContactNumber" placeholder="Masukkan Nomor Telepon" pattern="[0-9]*" inputmode="numeric">
                                <?= $errContact ?>
                            </div>

                                <div class="form-group col-md-6">
                                    <label for="inputEmailAddress">Alamat Email</label>
                                    <input type="text" name="inputEmailAddress" class="form-control <?= $classEmail ?>" id="inputEmailAddress" placeholder="Masukkan Alamat Email">
                                    <?= $errEmail ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="reset" class="btn btn-outline-secondary btn-block">Bersihkan</button>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block" name="savebtn">Tambah Dokter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
    <?php include JS_PATH; ?>
    <script>
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
if (isset($_POST["savebtn"])) {
    if (multi_empty($errFName, $errLName, $errSpec, $errYears, $errFee, $errSpoke, $errGender, $errEmail, $errContact, $errImage, $errPassword, $errConfirmPassword)) {

        if (isset($_FILES["inputAvatar"]["name"])) {
            $allowed =  array('gif', 'png', 'jpg');
            $filename = $_FILES['inputAvatar']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array($ext, $allowed)) {
                echo "<script>Swal.fire('Upss...','Hanya dapat format gambar!','error')</script>";
                exit();
            } else {
                if (!empty($_FILES['inputAvatar']['name'])) {
                    $folderpath = "../uploads/" . $clinic_id . "/doctor" . "/";
                    $path = "../uploads/" . $clinic_id . "/doctor" . "/" . $_FILES['inputAvatar']['name'];
                    $image = $_FILES['inputAvatar']['name'];

                    if (!file_exists($folderpath)) {
                        mkdir($folderpath, 0777, true);
                        move_uploaded_file($_FILES['inputAvatar']['tmp_name'], $path);
                    } else {
                        move_uploaded_file($_FILES['inputAvatar']['tmp_name'], $path);
                    }
                } else {
                    echo "<script>Swal.fire('Upss...','Anda harus memilih file untuk diunggah!','error')</script>";
                    exit();
                }
            }
        }

        $token = generateCode(6);
        $en_token = md5($token);

        // Hash the password before saving it to the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO doctors (doctor_avatar, doctor_firstname, doctor_lastname, doctor_speciality, doctor_experience, doctor_desc, doctor_spoke, doctor_gender, doctor_dob, doctor_email, doctor_contact, consult_fee, date_created, clinic_id, doctor_password) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssisssssssiss", $image, $fname, $lname, $speciality, $years, $desc, $spoke, $gender, $dob, $email, $contact, $fees, $date_created, $clinic_id, $hashedPassword);
        if ($stmt->execute()) {

            $last_id = $conn->insert_id;
            mysqli_query($conn,"INSERT INTO treatment_type (treatment_name, doctor_id) VALUES ('Pasien Baru', $last_id) ");

            $selector = bin2hex(random_bytes(8));
            $validator = random_bytes(32);
            $link = $_SERVER["SERVER_NAME"] . "/doclab/doctor/activate.php?selector=".$selector."&validator=". bin2hex($validator);
            $expries = date("U") + 86400; // one day

            $delstmt = $conn->prepare("DELETE FROM doctor_reset WHERE reset_email = ?");
            $delstmt->bind_param("s", $email);
            $delstmt->execute();

            $hashedToken = password_hash($validator, PASSWORD_DEFAULT);

            $resetstmt = $conn->prepare("INSERT INTO doctor_reset (reset_email, reset_selector, reset_token, reset_expires, activate_token) VALUE (?,?,?,?,?)");
            $resetstmt->bind_param("sssss", $email, $selector, $hashedToken, $expries, $en_token);
            $resetstmt->execute();

            if (sendmail($email, $mail['acc_subject'], $mail['acc_title'], $mail['acc_content'], $mail['acc_button'], $link, $token)) {
                echo '<script>
                Swal.fire({ title: "Hore!", text: "Dokter Baru Ditambahkan!", type: "success" }).then((result) => {
                    if (result.value) { window.location.href = "doctor-list.php"; }
                });
                </script>';
            } else {
                echo 'Ada yang salah';
            }
            // Tambahkan entri untuk tabel doctor_availabilities
            for ($day_id = 1; $day_id <= 7; $day_id++) {
                for ($session_id = 1; $session_id <= 65; $session_id++) {
                    $stmt_availabilities = $conn->prepare("INSERT INTO doctor_availabilities (doctor_id, day_id, session_id, available) VALUES (?, ?, ?, ?)");
                    $default_available = 0; // FALSE
                    $stmt_availabilities->bind_param("iiii", $last_id, $day_id, $session_id, $default_available);
                    $stmt_availabilities->execute();
                }
            }
        } else {
            echo 'Ada yang salah';
        }
        $stmt->close();
    }
}
?>
