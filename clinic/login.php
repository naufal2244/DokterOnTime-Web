<?php
include('../config/autoload.php');
include('./includes/path.inc.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
</head>

<body>
    <div class="container">
        <div class="login-wrap mx-auto">
            <div class="login-head">
                <h4><?php echo $BRAND_NAME; ?></h4>
                <p>Halo, Masuk ke Akun Anda!</p>
                <p>Klinik</p>
            </div>
            <div class="login-body">
                <form name="login_form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="inputEmail">Alamat Email</label>
                        <input type="text" name="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" placeholder="Masukkan email">
                        <small id="emailHelp" class="form-text text-muted">Kami tidak akan membagikan email Anda kepada siapapun.</small>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword">Kata Sandi</label>
                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Kata Sandi">
                    </div>
                    <div class="mb-3">
                        <a href="forgot.php">Lupa Kata Sandi?</a>
                    </div>
                    <button type="submit" name="login_btn" class="btn btn-primary btn-block button">Masuk</button>
                </form>
            </div>
            <div class="login-footer">
                <p class="text-muted">Belum punya akun? <a href="register.php">Daftar</a></p>
            </div>
        </div>
    </div>
</body>
<?php include JS_PATH; ?>
</html>
<?php
if (isset($_POST['login_btn']))
{
    $inputEmail = $conn->real_escape_string($_POST['email']);

    $check = $conn->prepare("SELECT * FROM clinic_manager WHERE clinicadmin_email = ? ");
    $check->bind_param("s", $inputEmail);
    $check->execute();
    $q = $check->get_result();
    $r = $q->fetch_assoc();
    if (mysqli_num_rows($q) != 1) {
        echo "<script>Swal.fire({title: 'Error!', text: 'Email & Password Tidak Ditemukan', type: 'error', confirmButtonText: 'Coba Lagi'})</script>";
        exit();
    } else {
        $token = $r["clinicadmin_token"];
    }
    
    $inputPassword = $conn->real_escape_string(encrypt(md5($_POST['password']), $token));

    $stmt = $conn->prepare("SELECT * FROM clinic_manager WHERE clinicadmin_email = ? AND clinicadmin_password = ? ");
    $stmt->bind_param("ss", $inputEmail, $inputPassword);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($inputEmail == "" && empty($inputEmail)) {
        echo "<script>Swal.fire({title: 'Error!', text: 'Silakan Masukkan Email', type: 'error'}).then(function() { $('#inputEmail').focus(); });</script>";
        exit();
    }

    if ($inputPassword == "" && empty($inputPassword)) {
        echo "<script>Swal.fire({title: 'Error!', text: 'Silakan Masukkan Kata Sandi', type: 'error'}).then(function() { $('#inputPassword').focus(); });</script>";
        exit();
    }

    if ($result->num_rows != 1)
    {
        echo "<script>Swal.fire({title: 'Error!', text: 'Email & Password Tidak Ditemukan', type: 'error', confirmButtonText: 'Coba Lagi'})</script>";
        exit();
    }
    else {
        $_SESSION['sess_clinicadminid'] = $row['clinicadmin_id'];
        $_SESSION['sess_clinicadminemail'] = $row['clinicadmin_email'];
        $_SESSION['loggedin'] = 1;
        header("Location: index.php");
    }
    $stmt->close();
}
?>
