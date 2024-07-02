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

$errEmail = $errPassword = $errConfirmPassword = $errFName = $errLName = "";
$classEmail = $classPassword = $classConfirmPassword = $classFName = $classLName = "";

$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = escape_input($_POST['inputEmailAddress']);
    $password = escape_input($_POST['inputPassword']);
    $confirm_password = escape_input($_POST['inputConfirmPassword']);
    $fname = escape_input($_POST['inputFirstName']);
    $lname = escape_input($_POST['inputLastName']);
    $clinic_id = $_SESSION['clinic_id'] ?? null; // Mengambil clinic_id dari sesi

    if (is_null($clinic_id)) {
        echo '<script>alert("Clinic ID is not set in session. Please login again.");</script>';
        exit();
    }

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

    if (empty($email)) {
        $errEmail = $error_html['errEmail'];
        $classEmail = $error_html['errClass'];
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errEmail = $error_html['invalidEmail'];
            $classEmail = $error_html['errClass'];
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

    if (empty($errEmail) && empty($errPassword) && empty($errConfirmPassword) && empty($errFName) && empty($errLName)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO apoteker (apoteker_email, apoteker_password, nama_depan, nama_belakang, clinic_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $email, $hashedPassword, $fname, $lname, $clinic_id);
        if ($stmt->execute()) {
            $success = true; // Set success flag to true
        } else {
            echo 'Something Wrong';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
                                <!-- Add Apoteker -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputFirstName">First Name</label>
                                        <input type="text" name="inputFirstName" class="form-control <?php echo $classFName ?>" id="inputFirstName" placeholder="Enter First Name">
                                        <?php echo $errFName; ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputLastName">Last Name/Surname</label>
                                        <input type="text" name="inputLastName" class="form-control <?php echo $classLName ?>" id="inputLastName" placeholder="Enter Last Name">
                                        <?php echo $errLName; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmailAddress">Email Address</label>
                                        <input type="text" name="inputEmailAddress" class="form-control <?php echo $classEmail ?>" id="inputEmailAddress" placeholder="Enter Email Address">
                                        <?php echo $errEmail; ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword">Password</label>
                                        <input type="password" name="inputPassword" class="form-control <?php echo $classPassword ?>" id="inputPassword" placeholder="Enter Password">
                                        <?php echo $errPassword ?>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputConfirmPassword">Confirm Password</label>
                                        <input type="password" name="inputConfirmPassword" class="form-control <?php echo $classConfirmPassword ?>" id="inputConfirmPassword" placeholder="Confirm Password">
                                        <?php echo $errConfirmPassword ?>
                                    </div>
                                </div>
                                <!-- End Add Apoteker -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="reset" class="btn btn-outline-secondary btn-block">Clear</button>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block" name="savebtn">Add Apoteker</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
    <?php include JS_PATH; ?>

    <?php if ($success): ?>
    <script>
        Swal.fire({
            title: "Great!",
            text: "New Apoteker Added!",
            icon: "success"
        }).then((result) => {
            if (result.value) {
                window.location.href = "apoteker-list.php";
            }
        });
    </script>
    <?php endif; ?>
</body>

</html>
