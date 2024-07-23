<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');
include(SELECT_HELPER);

$errFName = $errLName = $errSpec = $errYears = $errSpoke = $errGender = $errEmail = $errContact =  "";
$classFName = $classLName = $classSpec = $classYears  = $classSpoke = $classGender = $classEmail = $errContact = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = escape_input($_POST['inputFirstName']);
    $lname = escape_input($_POST['inputLastName']);
    if (isset($_POST['inputSpeciality'])) {
        $speciality = escape_input($_POST['inputSpeciality']);
    }
    $years = escape_input($_POST['inputYrsExp']);
    $desc = escape_input($_POST['inputDesc']);
    if (isset($_POST['inputLanguages'])) {
        $lang = $_POST['inputLanguages'];
        $spoke = implode(",", $lang);
    }
    $dob = escape_input($_POST['inputDOB']);
    if (isset($_POST['inputGender'])) {
        $gender = escape_input($_POST['inputGender']);
    }
    $email = escape_input($_POST['inputEmailAddress']);
    $contact = escape_input($_POST['inputContactNumber']);

    // Validate
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

    if (empty($years)) {
        $errYears = $error_html['errYears'];
        $classYears = $error_html['errClass'];
    } else {
        if (!filter_var($years, FILTER_VALIDATE_INT)) {
            $errYears = $error_html['invalidInt'];
            $classYears = $error_html['errClass'];
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

    if (!empty($contact)) {
        if (!preg_match($regrex['contact'], $contact)) {
            $errContact = $error_html['invalidInt'];
            $classContact = $error_html['errClass'];
        }
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
                                <!-- Add Doctor -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPatientID">Id Dokter#</label>
                                        <input type="text" name="inputPatientID" class="form-control" id="inputPatientID" disabled value="<?php echo $doctor_row["doctor_id"]; ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputFirstName">Nama Depan</label>
                                        <input type="text" name="inputFirstName" class="form-control <?= $classFName ?>" id="inputFirstName" placeholder="Enter First Name" value="<?php echo $doctor_row["doctor_firstname"]; ?>">
                                        <?php echo $errFName; ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputLastName">Nama Belakang</label>
                                        <input type="text" name="inputLastName" class="form-control <?= $classLName ?>" id="inputLastName" placeholder="Enter Last Name" value="<?php echo $doctor_row["doctor_lastname"]; ?>">
                                        <?php echo $errLName; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputSpeciality">Spesialis</label>
                                        <select name="inputSpeciality" id="inputSpeciality" class="form-control selectpicker <?= $classSpec ?>" data-live-search="true">
                                            <option value="" selected disabled>Choose</option>
                                            <?php
                                            $table_result = mysqli_query($conn, "SELECT * FROM speciality");
                                            while ($table_row = mysqli_fetch_assoc($table_result)) {
                                                if ($doctor_row["doctor_speciality"] == $table_row['speciality_id']) {
                                                    $selected = "selected";
                                                } else {
                                                    $selected = "";
                                                }
                                                echo '<option value="' . $table_row["speciality_id"] . '" ' . $selected . '>' . $table_row["speciality_name"] . '</option>' . PHP_EOL;
                                            }
                                            ?>
                                        </select>
                                        <?= $errSpec ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputYrsExp">Tahun Pengalaman</label>
                                        <input type="number" name="inputYrsExp" class="form-control <?= $classYears ?>" id="inputYrsExp" placeholder="Enter Years Experience" value="<?php echo $doctor_row["doctor_experience"]; ?>">
                                        <?= $errYears ?>
                                    </div>

                                </div>
                                <!-- End Add Doctor -->
                            </div>
                        </div>

                        <div class="card col-md-3">
                            <div class="card-body">
                                <div class="imageupload">
                                    <?php
                                    if (!empty($doctor_row["doctor_avatar"])) {
                                        echo '<img src="../uploads/' . $doctor_row["clinic_id"] . '/doctor' . '/' . $doctor_row["doctor_avatar"] . '" id="output" class="img-fluid thumbnail" alt="Doctor-Avatar" title="Doctor-Avatar">';
                                    } else {
                                        echo '<img src="../assets/img/empty/empty-avatar.jpg" id="output" class="img-fluid thumbnail" alt="Doctor-Avatar" title="Doctor-Avatar">';
                                    }
                                    ?>
                                    <div class="file-tab">
                                        <label class="btn btn-sm btn-primary btn-block btn-file">
                                            <span>Cari</span>
                                            <input type="file" name="inputAvatar" id="inputAvatar" accept="image/*" onchange="openFile(event)" value="<?= $doctor_row["doctor_avatar"]; ?>">
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
                                <label for="inputLanguages">Bahasa</label>
                                <div class="row">
                                    <?php $i = 1;
                                    foreach ($select_lang as $lang_value) {
                                        $checked_arr = explode(",", $doctor_row["doctor_spoke"]);
                                        $checked = "";
                                        if (in_array($lang_value, $checked_arr)) $checked = "checked";
                                    ?>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="inputLanguages[]" id="customCheck<?= $i ?>" class="custom-control-input <?= $classSpoke ?>" value="<?= $lang_value ?>" <?= $checked ?>>
                                                <label class="custom-control-label" for="customCheck<?= $i ?>"><?= $lang_value ?></label>
                                            </div>
                                        </div>
                                    <?php
                                        $i++;
                                    } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputDesc">Deskripsi</label>
                                <textarea class="form-control" id="inputDesc" name="inputDesc" rows="3"><?php echo $doctor_row["doctor_desc"]; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputDOB">Tanggal Lahir</label>
                                    <input type="text" name="inputDOB" class="form-control" id="datepicker" placeholder="Enter DOB" value="<?php echo $doctor_row["doctor_dob"]; ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputGender">Jenis Kelamin</label>
                                    <div class="row">
                                        <div class="col">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="inputGenderMale" name="inputGender" class="custom-control-input" value="Laki-laki" <?= $doctor_row["doctor_gender"] == "male" ? "checked" : "" ?>>
                                                <label class="custom-control-label" for="inputGenderMale">Laki-laki</label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="inputGenderFemale" name="inputGender" class="custom-control-input" value="Perempuan" <?= $doctor_row["doctor_gender"] == "female" ? "checked" : "" ?>>
                                                <label class="custom-control-label" for="inputGenderFemale">Perempuan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputContactNumber">Nomor HP</label>
                                    <input type="text" name="inputContactNumber" class="form-control <?= $classContact ?>" id="inputContactNumber" placeholder="Enter Phone Number" value="<?php echo $doctor_row["doctor_contact"]; ?>">
                                    <?= $errContact ?>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmailAddress">Alamat Email</label>
                                    <input type="text" name="inputEmailAddress" class="form-control <?= $classEmail ?>" id="inputEmailAddress" placeholder="Enter Email Address" value="<?php echo $doctor_row["doctor_email"]; ?>">
                                    <?= $errEmail ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6"><button type="reset" class="btn btn-outline-secondary btn-block">Bersihkan Input</button></div>
                        <div class="col-6"><button type="submit" class="btn btn-primary btn-block" name="savebtn">Simpan</button></div>
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
    if (multi_empty($errFName, $errLName, $errSpec, $errYears, $errSpoke, $errGender, $errEmail)) {

        if ($_FILES["inputAvatar"]["name"] != "") {
            if (isset($_FILES["inputAvatar"]["name"])) {
                $allowed =  array('gif', 'png', 'jpg');
                $filename = $_FILES['inputAvatar']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if (!in_array($ext, $allowed)) {
                    echo "<script>Swal.fire('Oops...','Only can be image!','error')</script>";
                    exit();
                } else {
                    if (!empty($_FILES['inputAvatar']['name'])) {
                        $folderpath = "../uploads/" . $doctor_row['clinic_id'] . "/doctor" . "/";
                        $path = "../uploads/" . $doctor_row['clinic_id'] . "/doctor" . "/" . $_FILES['inputAvatar']['name'];
                        $image = $_FILES['inputAvatar']['name'];

                        if (!file_exists($folderpath)) {
                            mkdir($folderpath, 0777, true);
                        }
                        move_uploaded_file($_FILES['inputAvatar']['tmp_name'], $path);
                    } else {
                        echo "<script>Swal.fire('Oops...','You should select a file to upload!','error')</script>";
                        exit();
                    }
                }
            }
            $updatestmt = $conn->prepare("UPDATE doctors SET doctor_avatar = ?, doctor_firstname = ?, doctor_lastname = ?, doctor_speciality = ?, doctor_experience = ?, doctor_desc = ?, doctor_spoke = ?, doctor_gender = ?, doctor_dob = ?, doctor_email = ?, doctor_contact = ? WHERE doctor_id = ? ");
            $updatestmt->bind_param("sssssssssssi", $image, $fname, $lname, $speciality, $years, $desc, $spoke, $gender, $dob, $email, $contact, $doctor_row['doctor_id']);
        } else {
            $updatestmt = $conn->prepare("UPDATE doctors SET doctor_firstname = ?, doctor_lastname = ?, doctor_speciality = ?, doctor_experience = ?, doctor_desc = ?, doctor_spoke = ?, doctor_gender = ?, doctor_dob = ?, doctor_email = ?, doctor_contact = ? WHERE doctor_id = ? ");
            $updatestmt->bind_param("ssssssssssi", $fname, $lname, $speciality, $years, $desc, $spoke, $gender, $dob, $email, $contact, $doctor_row['doctor_id']);
        }

        if ($updatestmt->execute()) {
            echo '<script>
                Swal.fire({ title: "Berhasil!", text: "Berhasil Update Profil", type: "success" }).then((result) => {
                    if (result.value) { window.location.href = "doctor-edit.php"; }
                });
            </script>';
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
        $updatestmt->close();
    }
}
?>
