<?php
require_once('../config/autoload.php');
require_once('./includes/path.inc.php');
require_once('./includes/session.inc.php');
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
            <div class="col-12 mt-3">
                <button type="button" class="btn btn-primary btn-sm pull-right px-5" data-toggle="modal" data-target="#modalPassword">Change Password</button>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="modalPasswordTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="modalPasswordTitle">Reset Password</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <form name="resetform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" autocomplete="off">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="inputOldPassword">Old Password</label>
                                    <input type="text" name="inputOldPassword" class="form-control" id="inputOldPassword" placeholder="Enter Old Password">
                                </div>
                                <div class="form-group">
                                    <label for="inputNewPassword">New Password</label>
                                    <input type="text" name="inputNewPassword" class="form-control" id="inputNewPassword" placeholder="Enter New Password">
                                    <small class="form-text text-muted" id="passwordHelp">Use 8 or more characters with a mix of letters, numbers & symbols</small>
                                </div>
                                <div class="form-group">
                                    <label for="inputConfirmPassword">Confirm New Password</label>
                                    <input type="text" name="inputConfirmPassword" class="form-control" id="inputConfirmPassword" placeholder="Enter Confirm New Password" >
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" name="resetbtn">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form name="regform" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" autocomplete="off">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputAdminID">Admin ID #</label>
                                    <input type="text" name="inputAdminID" class="form-control" id="inputAdminID" value="<?php echo $admin_row["clinicadmin_id"];?>" disabled>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputFirstName">Name</label>
                                    <input type="text" name="inputName" class="form-control" id="inputName" placeholder="Enter Name" value="<?php echo $admin_row["clinicadmin_name"];?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputContactNumber">Contact Number</label>
                                    <input type="text" name="inputContactNumber" class="form-control" id="inputContactNumber" placeholder="Enter Phone Number" value="<?php echo $admin_row["clinicadmin_contact"];?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmailAddress">Email Address</label>
                                    <input type="text" name="inputEmailAddress" class="form-control" id="inputEmailAddress" placeholder="Enter Email Address" value="<?php echo $admin_row["clinicadmin_email"];?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <button type="submit" class="btn btn-primary btn-block" name="savebtn">Save</button>
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
    $id         = $admin_row["clinicadmin_id"];
    $name       = $conn->real_escape_string($_POST['inputName']);
    $email      = $conn->real_escape_string($_POST['inputEmailAddress']);
    $contact    = $conn->real_escape_string($_POST['inputContactNumber']);

    $query = "UPDATE clinic_manager SET clinicadmin_name = '".$name."', clinicadmin_email = '".$email."', clinicadmin_contact = '".$contact."' WHERE clinicadmin_id = '".$id."' ";
    if (mysqli_query($conn,$query)) {
        $_SESSION['sess_email'] = $email;
        echo '<script>
            Swal.fire({ "Great!", "Update Successfully!", "success" }).then((result) => {
                if (result.value) { window.location.href = "manage-admin.php"; }
            })
            </script>';
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
    mysqli_close($conn);
}

// Reset Password
if (isset($_POST["resetbtn"])) {
    $id      = $admin_row["clinicadmin_id"];
    $oldpass = $conn->real_escape_string(md5($_POST['inputOldPassword']));
    $newpass = $conn->real_escape_string(md5($_POST['inputNewPassword']));
    $conpass = $conn->real_escape_string(md5($_POST['inputConfirmPassword']));

    $sql = "SELECT clinicadmin_password FROM clinic_manager WHERE clinicadmin_id = '".$id."' ";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $oldpassword = $row["clinicadmin_password"];

    if (empty($oldpass) && empty($newpass)) {
        echo "<script>Swal.fire('Oops...', 'Password & New Password Cannot Be Empty!', 'error');</script>";
        exit();
    }

    if ($oldpass == $oldpassword) {
        if ($newpass == $conpass) {
            $query = "UPDATE clinic_manager SET clinicadmin_password = '".$newpass."' WHERE clinicadmin_id = '".$id."' ";
            if (mysqli_query($conn,$query)) {
                echo '<script>
                    Swal.fire({ "Great!", "Password Reset Successfully!", "success" }).then((result) => {
                        if (result.value) { window.location.href = "manage-admin.php"; }
                    })
                    </script>';
            } else {
                echo "Error: " . $query . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "<script>Swal.fire('Oops...', 'New Password Does&apos;t Match!', 'error');</script>";
        }
    } else {
        echo "<script>Swal.fire('Oops...', 'Old Password Does&apos;t Match!', 'error');</script>";
    }

    mysqli_close($conn);
}
?>