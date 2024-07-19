<?php
require_once('../config/autoload.php');
require_once('./includes/path.inc.php');
require_once('./includes/session.inc.php');

$errors = array();

if (isset($_POST["savebtn"])) {
    $id         = $admin_row["clinicadmin_id"];
    $name       = escape_input($_POST['inputName']);
    $email      = escape_input($_POST['inputEmailAddress']);
    $contact    = escape_input($_POST['inputContactNumber']);


    if (empty($name)) {
        array_push($errors, "Nama Harus Diisi");
    }

    if (empty($email)) {
        array_push($errors, "Alamat Email Harus Diisi");
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Format Email Tidak Valid");
        }
    }

    if (empty($contact)) {
        array_push($errors, "Nomor Telepon harus Diisi");
    }
}

// Reset Password
if (isset($_POST["resetbtn"])) {
    $id      = $admin_row["clinicadmin_id"];
    $oldpass = $conn->real_escape_string($_POST['inputOldPassword']);
    $newpass = $conn->real_escape_string($_POST['inputNewPassword']);
    $conpass = $conn->real_escape_string($_POST['inputConfirmPassword']);

    $passstmt = $conn->prepare("SELECT * FROM clinic_manager WHERE clinicadmin_id =?");
    $passstmt->bind_param("i", $id);
    $passstmt->execute();
    $result = $passstmt->get_result();
    $row = $result->fetch_assoc();
    $token = $row["clinicadmin_token"];
    $password = decrypt($row["clinicadmin_password"], $token);

    if (empty($oldpass)) {
		array_push($errors, "Password Lama Harus Diisi");
	} elseif (empty($newpass)) {
		array_push($errors, "Password Baru Harus Diisi");
	} elseif (empty($conpass)) {
		array_push($errors, "Konfirmasi Password Harus Diisi");
	} elseif (md5($oldpass) != $password) {
		array_push($errors, "Password Lama Salah");
	} elseif (!empty($newpass)) {
        password_validation($newpass);
    } elseif ($newpass != $conpass) {
        array_push($errors, "Password Baru Tidak Sama");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
</head>

<body>
    <?php
        if (isset($_POST["resetbtn"])) {
            if (count($errors) == 0) {
                $newtoken = generateCode(22);
                $en_pass = encrypt(md5($newpass), $newtoken);
                $stmt2 = $conn->prepare("UPDATE clinic_manager SET clinicadmin_password = ?, clinicadmin_token = ? WHERE clinicadmin_id = ?");
                $stmt2->bind_param("ssi", $en_pass, $newtoken, $id);
                if ($stmt2->execute()) {
                    echo '<script>
                        Swal.fire({ title: "Great!", text: "Reset Password Berhasil!", type: "success" }).then((result) => {
                            if (result.value) { window.location.href = "admin.php"; }
                        })
                        </script>';
                } else {
                    echo "Error: " . $query . "<br>" . mysqli_error($conn);
                }
            }
        }
    ?>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <!-- Page content -->
        <div class="row">
            <div class="col-12">
                <form name="regform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" autocomplete="off">
                    <?php echo display_error(); ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputAdminID">ID Admin #</label>
                                    <input type="text" name="inputAdminID" class="form-control" id="inputAdminID" value="<?php echo $admin_row["clinicadmin_id"]; ?>" disabled>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputFirstName">Nama</label>
                                    <input type="text" name="inputName" class="form-control" id="inputName" placeholder="Masukkan Nama" value="<?php echo $admin_row["clinicadmin_name"]; ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputContactNumber">Nomor Kontak</label>
                                    <input type="text" name="inputContactNumber" class="form-control" id="inputContactNumber" placeholder="Masukkan Nomor Telepon" value="<?php echo $admin_row["clinicadmin_contact"]; ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmailAddress">Alamat Email</label>
                                    <input type="text" name="inputEmailAddress" class="form-control" id="inputEmailAddress" placeholder="Masukkan Alamat Email" value="<?php echo $admin_row["clinicadmin_email"]; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <button type="submit" class="btn btn-primary btn-block" name="savebtn">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <form name="resetform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" autocomplete="off">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputOldPassword">Password Lama</label>
                                <input type="password" name="inputOldPassword" class="form-control" id="inputOldPassword" placeholder="Masukkan Password Lama">
                            </div>
                            <div class="form-group">
                                <label for="inputNewPassword">Password Baru</label>
                                <input type="password" name="inputNewPassword" class="form-control" id="inputNewPassword" placeholder="Masukkan Password Baru">
                                <small class="form-text text-muted" id="passwordHelp">Gunakan 8 karakter atau lebih dengan kombinasi antara huruf, angka, dan simbol</small>
                            </div>
                            <div class="form-group">
                                <label for="inputConfirmPassword">Konfirmasi Password</label>
                                <input type="password" name="inputConfirmPassword" class="form-control" id="inputConfirmPassword" placeholder="Konfirmasi Password Baru">
                            </div>
                        </div>
                    </div>
                    <div class="md-3 mt-3">
                        <button type="submit" class="btn btn-primary btn-block" name="resetbtn">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Page Content -->
    </div>
    <?php include JS_PATH; ?>
</body>

</html>
<?php
// Edit Profile
if (isset($_POST["savebtn"])) {
    if (count($errors) == 0) {
        $stmt = $conn->prepare("UPDATE clinic_manager SET clinicadmin_name = ?, clinicadmin_email = ?, clinicadmin_contact = ? WHERE clinicadmin_id = ? ");
        $stmt->bind_param("sssi", $name, $email, $contact, $id);

        if ($stmt->execute()) {
            $_SESSION['sess_clinicadminemail'] = $email;
            echo '<script>
            Swal.fire({ title: "Sempurna!", text: "Berhasil mengupdate profil!", type: "success" }).then((result) => {
                if (result.value) { window.location.href = "admin.php"; }
            });
            </script>';
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
        $stmt->close();
    } else {
        echo '<script>
        Swal.fire({
            title: "Perhatian!",
            text: "Gagal mengupdate profil. Silakan coba lagi.",
            type: "error"
        });
        </script>';
    }
}
?>