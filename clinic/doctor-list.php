<?php
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include CSS_PATH; ?>
	<style>
		.card-body {
			display: flex;
			flex-direction: column;
			justify-content: center;
			height: 100%;
		}

		.fa-user {
			font-size: 100px; /* Perbesar ukuran ikon */
			color: #333;
			display: block;
			margin: auto;
		}

		.col-md-4 {
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 5px; /* Kurangi padding untuk mengurangi white space */
		}

		.col-md-8 {
			display: flex;
			flex-direction: column;
			justify-content: center;
		}
	</style>
</head>

<body>
	<?php include NAVIGATION; ?>
	<div class="page-content" id="content">
		<?php include HEADER; ?>
		<!-- Page content -->
		<div class="row">
			<?php
			$tlist = $conn->prepare("SELECT * FROM doctors WHERE clinic_id = ?");
			$tlist->bind_param("i", $clinic_row['clinic_id']);
			$tlist->execute();
			$tresult = $tlist->get_result();
			if ($tresult->num_rows === 0) {
				echo '<div>Tidak ada rekaman dokter</div>';
			} else {
				while ($trow = $tresult->fetch_assoc()) { ?>
					<div class="col-sm-6">
						<div class="card card-hover mb-3" style="height:200px;overflow:hidden;">
							<div class="row no-gutters">
								<div class="col-md-4">
									<i class="fas fa-user"></i>
								</div>
								<div class="col-md-8">
									<div class="card-body">
										<h6 class="card-title font-weight-bold"><?= $trow["doctor_firstname"] . ' ' . $trow["doctor_lastname"]; ?></h6>
										<p class="card-text"><b>
										<?php
											$table_result = mysqli_query($conn, "SELECT * FROM speciality WHERE speciality_id =  '".$trow["doctor_speciality"]."' ");
											while ($table_row = mysqli_fetch_assoc($table_result)) {
												echo $table_row['speciality_name'];
											}
											?>	
										</b></p>
										<p class="card-text"><?= $trow["doctor_email"]; ?></p>
										<p class="card-text"><?= $trow["doctor_contact"]; ?></p>
										<div class="mt-3">
											<a href="doctor-view.php?did=<?= encrypt_url($trow["doctor_id"]) ?>" class="btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i> Lihat</a>
											<a href= "#deleteid<?= $trow['doctor_id'] ?>" data-toggle="modal" class="btn btn-sm btn-danger" id="delete_product" data-id="<?php echo $trow["doctor_id"]; ?>"><i class="fa fa-trash mr-1"></i> Hapus</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- End Modal -->
					<div class="modal fade" id="deleteid<?= $trow['doctor_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header" style="border:none;">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
									<div class="modal-body">
										<input type="hidden" name="doctor_id" value="<?= $trow['doctor_id'] ?>">
										Apakah Anda yakin ingin menghapus <strong><?= $trow['doctor_firstname'].' '.$trow['doctor_lastname'] ?></strong> ?
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
		</div>
		<!-- End Page Content -->
	</div>
	<?php include JS_PATH; ?>
</body>

</html>
<?php
if (isset($_POST["deletebtn"])) {

	$id = $_POST["doctor_id"];
	if (mysqli_query($conn, "DELETE FROM doctors WHERE doctor_id = $id")) {
		echo '<script>
		Swal.fire({ title: "Bagus!", text: "Doctor berhasil dihapus!", type: "success" }).then((result) => {
			if (result.value) { window.location.href = "doctor-list.php"; }
		})
		</script>';
	} else {
		echo "Error menghapus data: " . mysqli_error($conn);
	}
	mysqli_close($conn);
}
?>
