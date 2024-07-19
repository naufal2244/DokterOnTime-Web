<?php
require_once('../config/autoload.php');
require_once('./includes/path.inc.php');
include(EMAIL_HELPER);

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$email = escape_input($_POST["inputEmailAddress"]);

	$forgotstmt = $conn->prepare("SELECT * FROM clinic_manager WHERE clinicadmin_email = ?");
	$forgotstmt->bind_param("s", $email);
	$forgotstmt->execute();
	$result = $forgotstmt->get_result();
	$r = $result->fetch_assoc();

	$clinicadmin_id = $r['clinicadmin_id'];

	if (empty($email)) {
		array_push($errors, "Alamat Email diperlukan");
	} else if ($result->num_rows != 1) {
		array_push($errors, "Alamat Email tidak terdaftar");
	} else {
		email_validation($email);
	}
}
?>
<!DOCTYPE html>
<html>

<head>
	<?php include CSS_PATH; ?>
	<link rel="stylesheet" href="../assets/css/clinic/login.css">
</head>

<body>
	<div class="container">
		<div class="login-wrap mx-auto">
			<div class="login-head">
				<h4><?php echo $BRAND_NAME; ?></h4>
				<p>Lupa Kata Sandi</p>
			</div>
			<div class="login-body">
				<form name="forgot_form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
					<?= display_error(); ?>
					<div class="form-group">
						<label for="inputEmailAddress">Alamat Email</label>
						<input type="email" name="inputEmailAddress" class="form-control" id="inputEmailAddress" aria-describedby="emailHelp" placeholder="contoh@email.com">
						<small id="emailHelp" class="form-text text-muted">Berikan kami alamat email/nomor ponsel akun Anda<br> Kami akan mengirimkan email instruksi untuk mereset kata sandi Anda.</small>
					</div>
					<button type="submit" name="forgotbtn" class="btn btn-primary btn-block button">Kirimkan</button>
				</form>
			</div>
			<div class="login-footer">
				<p class="text-muted"><a href="login.php"><i class="fa fa-long-arrow-alt-left"></i> Kembali</a></p>
			</div>
		</div>
	</div>
	<?php include JS_PATH; ?>
</body>

</html>
<?php
if (isset($_POST["forgotbtn"])) {
	if (count($errors) == 0) {
		$selector = bin2hex(random_bytes(8));
		$validator = random_bytes(32);
		$link = $_SERVER["SERVER_NAME"] . "/doclab/clinic/reset.php?selector=" . $selector . "&validator=" . bin2hex($validator);
		$expires = date("U") + 1800;

		$userEmail = $_POST["inputEmailAddress"];

		// Hapus token yang sudah ada untuk email ini
		$stmt = $conn->prepare("DELETE FROM clinic_reset WHERE reset_email = ?");
		$stmt->bind_param("s", $userEmail);
		$stmt->execute();

		$hashedToken = password_hash($validator, PASSWORD_DEFAULT);

		// Masukkan token baru
		$stmt = $conn->prepare("INSERT INTO clinic_reset (reset_email, reset_selector, reset_token, reset_expires) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ssss", $userEmail, $selector, $hashedToken, $expires);
		$stmt->execute();

		$stmt->close();

		if (sendmail($userEmail, $mail['fg_subject'], $mail['fg_title'], $mail['fg_content'], $mail['fg_button'], $link, "")) {
			echo "<script>Swal.fire('Great !','Kata Sandi Anda Telah Dikirim ke Email Anda','success')</script>";
		} else {
			echo "<script>Swal.fire('Oops...','Gagal Mengembalikan Kata Sandi Anda! Coba Lagi!','error')</script>";
		}
	}
	$forgotstmt->close();
}
?>