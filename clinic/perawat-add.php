<?php
session_start();
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

// Misalkan ini adalah hasil query dari database setelah login berhasil
$sess_email = $_SESSION["sess_clinicadminemail"];

$stmt1 = $conn->prepare("SELECT * FROM clinic_manager WHERE clinicadmin_email = ?");
$stmt1->bind_param("s", $sess_email);
$stmt1->execute();
$admin_row = $stmt1->get_result()->fetch_assoc();
$clinic_id = $admin_row['clinic_id'];
$_SESSION['clinic_id'] = $clinic_id;

$stmt1->close();

include(SELECT_HELPER);
include(EMAIL_HELPER);

$errEmail = $errPassword = $errConfirmPassword = $errFName = $errLName = $errDoctor = "";
$classEmail = $classPassword = $classConfirmPassword = $classFName = $classLName = $classDoctor = "";

$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = escape_input($_POST['inputEmailAddress']);
    $password = escape_input($_POST['inputPassword']);
    $confirm_password = escape_input($_POST['inputConfirmPassword']);
    $fname = escape_input($_POST['inputFirstName']);
    $lname = escape_input($_POST['inputLastName']);
    $doctor_id = escape_input($_POST['doctorSelect']); // Mengambil doctor_id dari form

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
            $errLName = $error_html['invalidText'];
            $classLName = $error_html['errClass'];
        }
    }

    if (empty($email)) {
        $errEmail = $error_html['errEmail'];
        $classEmail = $error_html['errClass'];
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errEmail = $error_html['invalidEmail'];
            $classEmail = $error_html['errClass'];
        } else {
            // Check if email already exists
            $check_email = $conn->prepare("SELECT * FROM perawat WHERE alamat_email = ?");
            $check_email->bind_param("s", $email);
            $check_email->execute();
            $result = $check_email->get_result();
            if ($result->num_rows > 0) {
                $errEmail = "Email already exists";
                $classEmail = "invalid";
            }
            $check_email->close();
        }
    }

    if (empty($password)) {
        $errPassword = "Password is required";
        $classPassword = "invalid";
    } else if (strlen($password) < 6) {
        $errPassword = "Password must be at least 6 characters long";
        $classPassword = "invalid";
    }

    if (empty($confirm_password)) {
        $errConfirmPassword = "Please confirm the password";
        $classConfirmPassword = "invalid";
    } else if ($password !== $confirm_password) {
        $errConfirmPassword = "Passwords do not match";
        $classConfirmPassword = "invalid";
    }

    if (empty($doctor_id)) {
        $errDoctor = "Please select a doctor";
        $classDoctor = "invalid";
    }

    if (empty($errEmail) && empty($errPassword) && empty($errConfirmPassword) && empty($errFName) && empty($errLName) && empty($errDoctor)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO perawat (alamat_email, password, nama_depan, nama_belakang, doctor_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $email, $hashedPassword, $fname, $lname, $doctor_id);
        if ($stmt->execute()) {
            $success = true; // Set success flag to true
        } else {
            echo 'Something went wrong';
        }
        $stmt->close();
    }
}

// Fetch doctors' data to populate the dropdown
$stmt2 = $conn->prepare("SELECT doctor_id, doctor_firstname, doctor_lastname FROM doctors");
$stmt2->execute();
$doctors = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .invalid-feedback {
            color: red;
        }
    </style>
</head>

<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <h4>Tambah Perawat</h4>
        <!-- Page content -->
        <div class="row">
            <div class="col-12">
                <form name="regform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <div class="d-flex">
                        <div class="card col-md-12">
                            <div class="card-body">
                                <!-- Add Perawat -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="doctorSelect">Dokter yang Didampingi</label>
                                        <select name="doctorSelect" class="form-control <?php echo $classDoctor ?>" id="doctorSelect">
                                            <?php foreach ($doctors as $doctor) : ?>
                                                <option value="<?php echo $doctor['doctor_id']; ?>">
                                                    <?php echo $doctor['doctor_id'] . ' - ' . $doctor['doctor_firstname'] . ' ' . $doctor['doctor_lastname']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback"><?php echo $errDoctor; ?></div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputFirstName">Nama Depan</label>
                                        <input type="text" name="inputFirstName" class="form-control <?php echo $classFName ?>" id="inputFirstName" placeholder="Enter First Name">
                                        <div class="invalid-feedback"><?php echo $errFName; ?></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputLastName">Nama Belakang</label>
                                        <input type="text" name="inputLastName" class="form-control <?php echo $classLName ?>" id="inputLastName" placeholder="Enter Last Name">
                                        <div class="invalid-feedback"><?php echo $errLName; ?></div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmailAddress">Alamat Email</label>
                                        <input type="text" name="inputEmailAddress" class="form-control <?php echo $classEmail ?>" id="inputEmailAddress" placeholder="Enter Email Address">
                                        <div class="invalid-feedback"><?php echo $errEmail; ?></div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword">Password</label>
                                        <div class="input-group">
                                            <input type="password" name="inputPassword" class="form-control <?php echo $classPassword ?>" id="inputPassword" placeholder="Enter Password">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('inputPassword')">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback"><?php echo $errPassword; ?></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputConfirmPassword">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" name="inputConfirmPassword" class="form-control <?php echo $classConfirmPassword ?>" id="inputConfirmPassword" placeholder="Confirm Password">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('inputConfirmPassword')">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback"><?php echo $errConfirmPassword; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Add Perawat -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="reset" class="btn btn-outline-secondary btn-block">Bersihkan</button>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block" name="savebtn">Tambah Perawat</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
    <?php include JS_PATH; ?>

    <?php if ($success) : ?>
        <script>
            Swal.fire({
                title: "Berhasil!",
                text: "Perawat Baru Ditambahkan!",
                icon: "success"
            }).then((result) => {
                if (result.value) {
                    window.location.href = "perawat-list.php";
                }
            });
        </script>
    <?php endif; ?>

    <script>
        function togglePasswordVisibility(id) {
            var input = document.getElementById(id);
            if (input.type === "password") {
                input.type = "text";
            } else {
                input.type = "password";
            }
        }
    </script>
</body>

</html>