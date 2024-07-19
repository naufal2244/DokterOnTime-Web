<?php
require_once('../config/autoload.php');
require_once('./includes/path.inc.php');
ob_start(); // Mulai output buffering

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = escape_input($_POST['inputClinicName']);
    $manager  = escape_input($_POST['inputManagerName']);
    $email    = escape_input($_POST['inputEmail']);
    $contact  = escape_input($_POST['inputContact']);
    $password = $conn->real_escape_string($_POST['inputPassword']);
    $con_pass = $conn->real_escape_string($_POST['inputConfirmPassword']);

    if (empty($name)) {
        array_push($errors, "Nama Klinik diperlukan");
    }
    if (empty($manager)) {
        array_push($errors, "Nama Manajer Klinik diperlukan");
    }
    if (empty($email)) {
        array_push($errors, "Email diperlukan");
    } else {
        email_validation($email);
    }
    if (empty($contact)) {
        array_push($errors, "Kontak diperlukan");
    }
    if (empty($password)) {
        array_push($errors, "Password diperlukan");
    } elseif ($password != $con_pass) {
        array_push($errors, "Password tidak sama");
    } else {
        password_validation($password);
    }

    if (empty($con_pass)) {
        array_push($errors, "Konfirmasi Password diperlukan");
    }

    if (count($errors) == 0) {
        $date_created = date('Y-m-d H:i:s'); // Assuming date_created should be the current date-time

        $stmt = $conn->prepare("INSERT INTO clinics (clinic_name, date_created) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $date_created);
        if ($stmt->execute()) {
            $last_id = $stmt->insert_id;
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();

        $token = generateCode(22);
        $en_pass = encrypt(md5($password), $token);

        $stmt = $conn->prepare("INSERT INTO clinic_manager (clinicadmin_name, clinicadmin_email, clinicadmin_password, clinicadmin_token, clinicadmin_contact, date_created, clinic_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $manager, $email, $en_pass, $token, $contact, $date_created, $last_id);

        if ($stmt->execute()) {
            $_SESSION['sess_clinicadminemail'] = $email;
            $_SESSION['loggedin'] = 1;
            header("Location: clinic-register.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="login-wrap mx-auto">
            <div class="login-head">
                <h4><?php echo $BRAND_NAME; ?></h4>
                <p>Buat Akun! Kelola Klinik Anda</p>
            </div>
            <div class="login-body">
                <form name="login_form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <?php echo display_error(); ?>
                    <div class="form-group">
                        <label for="inputClinicName">Nama Klinik</label>
                        <input type="text" name="inputClinicName" class="form-control" id="inputClinicName" placeholder="Nama Klinik">
                    </div>
                    <div class="form-group">
                        <label for="inputManagerName">Nama Manajer Klinik</label>
                        <input type="text" name="inputManagerName" class="form-control" id="inputManagerName" placeholder="John Doe">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail">Alamat Email</label>
                            <input type="text" name="inputEmail" class="form-control" id="inputEmail" placeholder="example@address.com">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputContact">Nomor Kontak</label>
                            <input type="text" name="inputContact" class="form-control" id="inputContact" placeholder="01012345678">
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="form-group col-md-6">
                            <label for="inputPassword">Password</label>
                            <div class="input-group">
                                <input type="password" name="inputPassword" class="form-control" id="inputPassword" placeholder="Masukkan Password" data-toggle="popover" data-placement="left" data-content="Password harus berisi minimal 8 karakter, termasuk huruf besar, huruf kecil, dan angka">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('inputPassword');">
                                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputConfirmPassword">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="inputConfirmPassword" class="form-control" id="inputConfirmPassword" placeholder="Masukkan Ulang Password">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('inputConfirmPassword');">
                                        <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="registerbtn" class="btn btn-primary btn-block button">Buat Akun</button>
                </form>
            </div>
            <div class="login-footer">
                <p class="text-muted">Sudah punya akun? <a href="login.php">Masuk</a></p>
            </div>
        </div>
    </div>
    <?php include JS_PATH; ?>
    <script>
        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();
        });

        function togglePassword(fieldId) {
            var field = document.getElementById(fieldId);
            var icon = document.querySelector(`#${fieldId} + .input-group-append .fas`);
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>