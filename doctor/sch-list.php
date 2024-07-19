<?php
header('Content-Type: text/html; charset=UTF-8');
include('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

if (isset($_POST['submitbtn'])) {
	$from = $_POST['datefrom'];
	$to = $_POST['dateto'];
	$week = $_POST['inputDay'];
	$status = 1;

	$stmt = $conn->prepare("INSERT INTO schedule (date_from, date_to, schedule_week, status, doctor_id, clinic_id) VALUE (?,?,?,?,?,?)");
	$stmt->bind_param("ssssss", $from, $to, $week, $status, $doctor_row['doctor_id'], $doctor_row['clinic_id']);
	$stmt->execute();
	$stmt->close();
	$id = $conn->insert_id;

	header('Location: sch-edit.php?scheduleid=' . $id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include CSS_PATH; ?>
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>

<body>
	<?php include NAVIGATION; ?>
	<div class="page-content" id="content">
		<?php include HEADER; ?>
		<div class="row">
			<div class="modal fade" id="addschedule">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h6 class="modal-title">Buat Jadwal Baru</h6>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						</div>
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
							<div class="modal-body">
								<div class="form-group">
									<label for="inputDay">Hari</label>
									<select name="inputDay" id="inputDay" class="form-control">
										<?php $dayval = array("Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu");
										foreach ($dayval as $week) : ?>
											<option value="<?= $week ?>"><?= $week ?></option>
										<?php endforeach ?>
									</select>
								</div>
								<div class="form-group">
									<label for="datepickerfrom">Dari Tanggal</label>
									<input type="text" id="datepickerfrom" name="datefrom" class="form-control">
								</div>
								<div class="form-group">
									<label for="datepickerto">Sampai Tanggal</label>
									<input type="text" id="datepickerto" name="dateto" class="form-control">
								</div>
							</div>
							<div class="modal-footer">
								<button type="reset" class="btn btn-light" name="clearbtn">Bersihkan</button>
								<button type="submit" class="btn btn-primary" name="submitbtn">Tambah</button>
							</div>
						</form>

					</div>
				</div>
			</div>

			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<button class="btn btn-sm btn-primary px-5 mb-3" data-toggle="modal" data-target="#addschedule">Tambah Jadwal</button>
						<div class="data-tables">
							<table id="datatable2" class="table table-responsive-lg nowrap" style="width:100%">
								<thead>
									<tr>
										<th>Tanggal Dari -- Sampai</th>
										<th>Minggu</th>
										<th>Status</th>
										<th>Tindakan</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$tresult = mysqli_query($conn, "SELECT * FROM schedule WHERE doctor_id = '" . $doctor_row['doctor_id'] . "'");
									if ($tresult->num_rows == 0) {
										echo '<tr><td>Tidak Ada Data</td></tr>';
									} else {
										while ($trow = mysqli_fetch_assoc($tresult)) { ?>
											<tr>
												<td><?= $trow["date_from"] . ' -- ' . $trow["date_to"]; ?></td>
												<td><?= $trow["schedule_week"]; ?></td>
												<td><?= ($trow['status'] == 1) ? '<span class="badge badge-success px-3 py-1">Aktif</span></td>' : '<span class="badge badge-warning px-3 py-1">Tidak Aktif</span></td>'; ?>
												<td>
													<a href="sch-edit.php?scheduleid=<?= $trow["schedule_id"]; ?>" class="btn btn-sm btn-outline-secondary"><i class="fa fa-plus"></i> Slot Waktu</a>
													<a data-toggle="modal" href="#editscheduleid<?= $trow["schedule_id"]; ?>" class="btn btn-sm btn-outline-info"><i class="fa fa-pen"></i> Edit</a>
													<a data-toggle="modal" href="#deletescheduleid<?= $trow["schedule_id"]; ?>" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i> Hapus</a>
												</td>
											</tr>

											<div class="modal fade" tabindex="-1" role="dialog" id="editscheduleid<?= $trow["schedule_id"]; ?>">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<h5 class="modal-title">Edit</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														</div>
														<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
															<div class="modal-body">
																<input type="hidden" name="inputID" value="<?= $trow["schedule_id"]; ?>">
																<div class="form-group">
																	<label for="">Hari</label>
																	<select name="inputEditDay" id="inputDay" class="form-control">
																		<?php $dayval = array("Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu");
																		foreach ($dayval as $week) { ?>
																			<option value="<?= $week ?>" <?= ($week == $trow['schedule_week']) ? 'selected' : '' ?>><?= $week ?></option>
																		<?php } ?>
																	</select>
																</div>
																<div class="form-group">
																	<label for="datepickerfrom">Dari Tanggal</label>
																	<input type="text" id="editdatepickerfrom<?= $trow["schedule_id"]; ?>" name="editdatefrom" class="form-control" value="<?= $trow['date_from'] ?>">
																</div>
																<div class="form-group">
																	<label for="datepickerto">Sampai Tanggal</label>
																	<input type="text" id="editdatepickerto<?= $trow["schedule_id"]; ?>" name="editdateto" class="form-control" value="<?= $trow['date_to'] ?>">
																</div>
															</div>
															<div class="modal-footer">
																<button type="submit" name="editbtn" class="btn btn-primary">Simpan</button>
																<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
															</div>
														</form>
														<script>
															$(function() {
																$('#editdatepickerfrom<?= $trow["schedule_id"]; ?>').datetimepicker({
																	format: 'YYYY-MM-DD',
																});
																$('#editdatepickerto<?= $trow["schedule_id"]; ?>').datetimepicker({
																	useCurrent: false,
																	format: 'YYYY-MM-DD',
																});
																$("#editdatepickerfrom<?= $trow["schedule_id"]; ?>").on("dp.change", function(e<?= $trow["schedule_id"]; ?>) {
																	$('#editdatepickerto<?= $trow["schedule_id"]; ?>').data("DateTimePicker").minDate(e<?= $trow["schedule_id"]; ?>.date);
																});
																$("#editdatepickerto<?= $trow["schedule_id"]; ?>").on("dp.change", function(e<?= $trow["schedule_id"]; ?>) {
																	$('#editdatepickerfrom<?= $trow["schedule_id"]; ?>').data("DateTimePicker").maxDate(e<?= $trow["schedule_id"]; ?>.date);
																});
															});
														</script>
													</div>
												</div>
											</div>

											<div class="modal fade" tabindex="-1" role="dialog" id="deletescheduleid<?= $trow["schedule_id"]; ?>">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<h5 class="modal-title">Hapus</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
														</div>
														<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
															<div class="modal-body">
																<input type="hidden" name="inputDeleteID" value="<?= $trow["schedule_id"]; ?>">
																<p>Apakah Anda yakin ingin menghapus?</p>
															</div>
															<div class="modal-footer">
																<button type="submit" name="deletebtn" class="btn btn-danger">Hapus</button>
																<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
		</div>
	</div>

</html>
<?php include JS_PATH; ?>
<script type="text/javascript"></script>
	<script>
		$(function() {
			$("#datepickerfrom").datepicker({
				dateFormat: "yy-mm-dd",
				onSelect: function(selected) {
					$("#datepickerto").datepicker("option", "minDate", selected);
				}
			});
			$("#datepickerto").datepicker({
				dateFormat: "yy-mm-dd",
				onSelect: function(selected) {
					$("#datepickerfrom").datepicker("option", "maxDate", selected);
				}
			});
		});
	</script>


<?php
if (isset($_POST['editbtn'])) {
	$id = escape_input($_POST['inputID']);
	$from = $_POST['editdatefrom'];
	$to = $_POST['editdateto'];
	$week = escape_input($_POST['inputEditDay']);
	$status = 1;

	$stmt = $conn->prepare("UPDATE schedule SET date_from = ?, date_to = ?, schedule_week = ?, status = ? WHERE schedule_id = ?");
	$stmt->bind_param("sssss", $from, $to, $week, $status, $id);

	if ($stmt->execute()) {
		echo '<script>
			Swal.fire({ title: "Great!", text: "Successful Updated!", type: "success" }).then((result) => {
				if (result.value) { window.location.href = "sch-list.php"; }
			});
		</script>';
	} else {
		echo "Error: " . $query . "<br>" . mysqli_error($conn);
	}
	$stmt->close();
}

if (isset($_POST['deletebtn'])) {
	$delid = escape_input($_POST['inputDeleteID']);

	$delstmt = $conn->prepare("DELETE FROM schedule WHERE schedule_id = ?");
	$delstmt->bind_param("s", $delid);

	$detailstmt = $conn->prepare("DELETE FROM schedule_detail WHERE schedule_id = ?");
	$detailstmt->bind_param("s", $delid);

	if ($delstmt->execute() && $detailstmt->execute()) {
		echo '<script>
			Swal.fire({ title: "Great!", text: "Successful Deleted!", type: "success" }).then((result) => {
				if (result.value) { window.location.href = "sch-list.php"; }
			});
		</script>';
	} else {
		echo "Error: " . $query . "<br>" . mysqli_error($conn);
	}
	$delstmt->close();
}
