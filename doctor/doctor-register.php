<?php
require_once('../config/autoload.php');
require_once('./includes/path.inc.php');
include('../helper/select_helper.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = escape_input($_POST["inputEmail"]);
    $password = escape_input($_POST["inputPassword"]);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $clinic_id = escape_input($_POST["clinic_id"]);  // Pastikan ini sesuai dengan nilai valid di tabel clinics

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM doctors WHERE doctor_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Email already exists";
    } else {
        // Insert new doctor
        $stmt = $conn->prepare("INSERT INTO doctors (doctor_email, doctor_password, clinic_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashed_password, $clinic_id);

        if ($stmt->execute()) {
            $success = "Registration successful";
        } else {
            $error = "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="../assets/css/clinic/style.css">
</head>

<body>
    <div class="container">
        <div class="title text-center mt-5">
            <h3><a href="login.php"><?php echo $BRAND_NAME; ?></a></h3>
        </div>
        <form name="registerForm" id="registerForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="register-wrap">
                <div class="form-group">
                    <label for="inputEmail">Email Address*</label>
                    <input type="email" name="inputEmail" class="form-control" id="inputEmail" required>
                </div>
                <div class="form-group">
                    <label for="inputPassword">Password*</label>
                    <input type="password" name="inputPassword" class="form-control" id="inputPassword" required>
                </div>
                <div class="form-group">
                    <label for="clinic_id">Clinic ID*</label>
                    <input type="text" name="clinic_id" class="form-control" id="clinic_id" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
        <?php
        if (isset($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        if (isset($success)) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        ?>
    </div>
    <?php include JS_PATH; ?>
</body>

</html>
