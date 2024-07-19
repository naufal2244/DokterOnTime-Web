<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

$errName = "";
$className = "";

if (isset($_POST['submitbtn'])) {
	$name = escape_input($_POST['inputTreatment']);

	if (empty($name)) {
		$errName = '<div class="invalid-feedback">This Field is required</div>';
		$className = $error_html['errClass'];
	} else {
		if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
			$errName = $error_html['invalidText'];
			$className = $error_html['errClass'];
		}
	}

	if ($errName == "") {
		$treatstmt = $conn->prepare("INSERT INTO treatment_type (treatment_name, doctor_id) VALUES (?, ?)");
		$treatstmt->bind_param("si", $name, $doctor_row['doctor_id']);
		$treatstmt->execute();
		$treatstmt->close();
		header('Location: '.$_SERVER['PHP_SELF']);
	}
}

$modalerrName = "";
$modalclassName = "";

if (isset($_POST['editbtn'])) {
	$newName = escape_input($_POST['inputNewTreatment']);
	$tid = escape_input($_POST['treatmentID']);

	if (empty($newName)) {
		$modalerrName = '<div class="invalid-feedback">This Field is required</div>';
		$modalclassName = $error_html['errClass'];
	} else {
		if (!preg_match($regrex['text'], $newName)) {
			$modalerrName = $error_html['invalidText'];
			$modalclassName = $error_html['errClass'];
		}
	}

	if ($modalerrName == "") {
		$treatstmt = $conn->prepare("UPDATE treatment_type SET treatment_name= ? WHERE treatment_id = ?");
		$treatstmt->bind_param("si", $newName, $tid);
		$treatstmt->execute();
		$treatstmt->close();
		header('Location: '.$_SERVER['PHP_SELF']);
	}
}

if (isset($_POST['deletebtn'])) {
	$tid = escape_input($_POST['treatmentID']);
	$treatstmt = $conn->prepare("DELETE FROM treatment_type WHERE treatment_id = ?");
	$treatstmt->bind_param("i", $tid);
	$treatstmt->execute();
	$treatstmt->close();

	header('Location: '.$_SERVER['PHP_SELF']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include CSS_PATH; ?>
	<script>

	</script>
</head>

<body>
	<?php include NAVIGATION; ?>
	<div class="page-content" id="content">
		<?php include HEADER; ?>
		<!-- Page content -->
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<form class="inline-form" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
							<div class="form-group row">
								<label for="inputTreatment" class="col-sm-3 col-form-label text-right">Jenis Perawatan</label>
								<div class="col-sm-6">
									<input type="text" name="inputTreatment" id="inputTreatment" class="form-control form-control-sm <?= $className ?>">
									<?= $errName ?>
								</div>
							</div>
							<div class="d-flex justify-content-md-center pt-2">
								<button type="reset" class="btn btn-light btn-sm px-5 mr-2" name="clearbtn">Bersihkan</button>
								<button type="submit" class="btn btn-primary btn-sm px-5" name="submitbtn">Tambah</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<table class="table">
							<thead>
								<th>Jenis Perawatan</th>
								<th>Aksi</th>
							</thead>
							<tbody>
							<?php
							$tresult = $conn->query("SELECT * FROM treatment_type WHERE doctor_id = '" . $doctor_row['doctor_id'] . "' ");
							if ($tresult->num_rows == 0) {
								echo '<tr><td colspan="3">Tidak ada data</td></tr>';
							} else {
								while ($trow = $tresult->fetch_assoc()) {
									?>
									<tr>
										<td><?= $trow['treatment_name'] ?></td>
										<td>
											<a class="btn btn-sm btn-outline-success" data-toggle="modal" href="#edit<?= $trow['treatment_id'] ?>">Ubah</a>
											<a class="btn btn-sm btn-outline-danger" data-toggle="modal" href="#delete<?= $trow['treatment_id'] ?>">Hapus</a>
										</td>
									</tr>

									<div class="modal fade" id="edit<?= $trow['treatment_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header" style="border:none;">
													<h5 class="modal-title" id="deleteModalLabel">Ubah</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
													<div class="modal-body">
														<input type="hidden" name="treatmentID" value="<?= $trow['treatment_id'] ?>">
														<div class="form-group">
															<input type="text" name="inputNewTreatment" id="inputNewTreatment" class="form-control <?= $modalclassName ?>" value="<?= $trow['treatment_name'] ?>">
															<?= $modalerrName ?>
														</div>
													</div>
													<div class="modal-footer" style="border:none;">
														<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
														<button type="submit" name="editbtn" class="btn btn-sm btn-primary">Simpan</button>
													</div>
												</form>
											</div>
										</div>
									</div>

									<div class="modal fade" id="delete<?= $trow['treatment_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header" style="border:none;">
													<!-- <h5 class="modal-title" id="deleteModalLabel">Delete</h5> -->
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
													<div class="modal-body">
														<input type="hidden" name="treatmentID" value="<?= $trow['treatment_id'] ?>">
														Apakah Anda yakin ingin menghapus <strong><?= $trow['treatment_name'] ?></strong> ?
													</div>
													<div class="modal-footer" style="border:none;">
														<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Tutup</button>
														<button type="submit" name="deletebtn" class="btn btn-sm btn-danger">Hapus</button>
													</div>
												</form>
											</div>
										</div>
									</div>
							<?php
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- End Page Content -->
	</div>
	<?php include JS_PATH; ?>
</body>

</html>