<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
?>
<!DOCTYPE html>
<html>

<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="../assets/css/clinic/login_copy.css">
</head>

<body>
    <div class="container">
        <div class="login-wrap mx-auto">
            <div class="login-head">
                <h4><?php echo $BRAND_NAME; ?></h4>
                <p>Halo, Masuk ke Akun Anda!</p>
                <p>Perawat</p>
            </div>
            <div class="login-body">
                <form name="login_form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="inputEmail">Alamat Email</label>
                        <input type="email" name="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" placeholder="Masukkan email">
                        <small id="emailHelp" class="form-text text-muted">Kami tidak akan membagikan email Anda kepada siapapun.</small>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword">Kata Sandi</label>
                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Kata Sandi">
                    </div>
                    <button type="submit" name="loginbtn" class="btn btn-primary btn-block button">Masuk</button>
                </form>
            </div>
            <!-- <div class="login-footer">
                <a class="text-muted">Tidak punya akun? <a href="register.php">Daftar</a></p>
            </div> -->
        </div>
    </div>
    <?php include JS_PATH; ?>
</body>

</html>
<?php
if (isset($_POST['loginbtn'])) {
    $inputEmail = $conn->real_escape_string($_POST['email']);
    $inputPassword = $_POST['password'];

    $check = $conn->prepare("SELECT * FROM perawat WHERE alamat_email = ?");
    $check->bind_param("s", $inputEmail);
    $check->execute();
    $q = $check->get_result();
    $r = $q->fetch_assoc();

    if ($q->num_rows != 1) {
        echo "<script>Swal.fire({title: 'Error!', text: 'Email & Kata Sandi Tidak Ada', icon: 'error', confirmButtonText: 'Coba Lagi'})</script>";
        exit();
    }

    $hashedPassword = $r['password'];

    // Debug output
    echo "Input Password: $inputPassword <br>";
    echo "Stored Hashed Password: $hashedPassword <br>";

    try {
        if (empty($inputEmail)) {
            echo "<script>Swal.fire({title: 'Error!', text: 'Harap Masukkan Email', icon: 'error'}).then(function() { $('#inputEmail').focus(); });</script>";
            exit();
        }

        if (empty($inputPassword)) {
            echo "<script>Swal.fire({title: 'Error!', text: 'Harap Masukkan Kata Sandi', icon: 'error'}).then(function() { $('#inputPassword').focus(); });</script>";
            exit();
        }

        if (!password_verify($inputPassword, $hashedPassword)) {
            echo "<script>Swal.fire({title: 'Error!', text: 'Email & Kata Sandi Tidak Ada', icon: 'error', confirmButtonText: 'Coba Lagi'})</script>";
            exit();
        } else {
            $_SESSION['PerawatRoleID'] = $r['perawat_id'];
            $_SESSION['PerawatRoleEmail'] = $r['alamat_email'];
            $_SESSION['PerawatRoleLoggedIn'] = 1;
            header("Location: index.php");
        }
    } catch (Exception $error) {
        die('Ada kesalahan saat menjalankan query [' . $conn->error . ']');
    }

    $check->close();
    $conn->close();
}
?>