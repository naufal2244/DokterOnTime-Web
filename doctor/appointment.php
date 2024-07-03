<?php
require_once('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include CSS_PATH; ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<style>
		.status-label {
			padding: 5px 10px;
			border-radius: 5px;
			display: inline-block;
			margin: 2px 0;
		}

		.status-belum-periksa {
			background-color: #d3d3d3;
			color: black;
		}

		.status-sedang-periksa {
			background-color: #ffd700;
			color: black;
		}

		.status-sudah-periksa {
			background-color: #32cd32;
			color: black;
		}

		.status-tidak-hadir {
			background-color: #FF4500;
			color: black;
		}

		.btn-diagnosa {
			display: inline-block;
			background-color: #007bff;
			color: white;
			padding: 6px 12px;
			text-decoration: none;
			border-radius: 5px;
			border: 2px solid;
		}

		.btn-diagnosa i {
			margin-right: 5px;
		}
	</style>
</head>

<body>
	<?php include NAVIGATION; ?>
	<div class="page-content" id="content">
		<?php include HEADER; ?>
		<!-- Page content -->
		<div class="row">
			<div class="col-md-4">
				<div class="card">
					<div class="card-body">
						<div class="form-group">
							<div id="datepicker"></div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-8">
				<!-- Card Content -->
				<div class="card">
					<div class="card-body">
						<!-- Dropdown Pilih Sesi -->
						<div class="form-group row">
							<label for="session" class="col-form-label" style="margin-left: 20px;">Pilih Sesi :</label>
							<div>
								<select id="session" class="form-control form-control-sm ml-2">
									<option value="1">Sesi 1 (08.00-09.00)</option>
									<option value="2">Sesi 2 (09.00-10.00)</option>
									<option value="3">Sesi 3 (10.00-11.00)</option>
								</select>
							</div>
						</div>

						<!-- Datatable -->
						<?php
						function headerTable()
						{
							$header = array("Nama Pasien", "No Antri", "Waktu Periksa", "Status Periksa", "Aksi");
							for ($i = 0; $i < count($header); $i++) {
								echo "<th>" . $header[$i] . "</th>" . PHP_EOL;
							}
						}

						// Contoh data pasien untuk diisi ke dalam tabel
						$patients = [
							["nama" => "John Doe", "no_antri" => "001", "waktu_periksa" => "09:00", "status_periksa" => "Belum Periksa"],
							["nama" => "Jane Smith", "no_antri" => "002", "waktu_periksa" => "09:30", "status_periksa" => "Sedang Periksa"],
							["nama" => "Bob Johnson", "no_antri" => "003", "waktu_periksa" => "10:00", "status_periksa" => "Sudah Periksa"],
							["nama" => "Alice Williams", "no_antri" => "004", "waktu_periksa" => "10:30", "status_periksa" => "Tidak Hadir"],
						];

						function statusClass($status)
						{
							switch ($status) {
								case "Belum Periksa":
									return "status-belum-periksa";
								case "Sedang Periksa":
									return "status-sedang-periksa";
								case "Sudah Periksa":
									return "status-sudah-periksa";
								case "Tidak Hadir":
									return "status-tidak-hadir";
								default:
									return "";
							}
						}
						?>
						<div class="data-tables">
							<table id="datatable" class="table table-responsive-lg nowrap">
								<thead>
									<tr>
										<?php headerTable(); ?>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($patients as $patient) : ?>
										<tr>
											<td><?= htmlspecialchars($patient['nama']) ?></td>
											<td><?= htmlspecialchars($patient['no_antri']) ?></td>
											<td><?= htmlspecialchars($patient['waktu_periksa']) ?></td>
											<td><span class="status-label <?= statusClass($patient['status_periksa']) ?>"><?= htmlspecialchars($patient['status_periksa']) ?></span></td>
											<td>
												<a href="#" class="btn btn-diagnosa">
													<i class="fas fa-stethoscope" style="margin-right: 5px;"></i> Mulai Diagnosa
												</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						<!-- End Datatable -->
					</div>
				</div>
				<!-- End Card Content -->
			</div>

			<?php
			/*
<div class="col-md-8">
    <!-- Card Content -->
    <div class="card">
        <div class="card-body">
            <!-- Datatable -->
            <?php
            function headerTable()
            {
                $header = array("Name", "Time",  "Treatment", "Case","Arrive", "Status", "Action");
                for ($i = 0; $i < count($header); $i++) {
                    echo "<th>" . $header[$i] . "</th>" . PHP_EOL;
                }
            }
            ?>
            <div class="data-tables">
                <table id="datatable" class="table table-responsive-lg nowrap">
                    <thead>
                        <tr>
                            <?php headerTable(); ?>
                        </tr>
                    </thead>
                    <tbody id="responsecontainer"></tbody>
                </table>
            </div>
            <!-- End Datatable -->
        </div>
    </div>
    <!-- End Card Content -->
</div>
*/
			?>
		</div>
		<!-- End Page Content -->
	</div>
	<?php include JS_PATH; ?>
	<script type="text/javascript">
		$(function() {
			$('#datepicker').datetimepicker({
				inline: true,
				minDate: '<?= $current_date ?>',
				format: 'YYYY-MM-DD',
			}).on('dp.change', function(event) {
				var formatted = event.date.format('YYYY-MM-DD');
				loadData(formatted, $('#session').val(), <?= $doctor_row['doctor_id'] ?>);
			});

			$('#session').change(function() {
				var formatted = $('#datepicker').data('DateTimePicker').date().format('YYYY-MM-DD');
				loadData(formatted, $(this).val(), <?= $doctor_row['doctor_id'] ?>);
			});

			function loadData(formatted, session, doctorId) {
				$.ajax({
					type: "POST",
					data: {
						date: formatted,
						session: session,
						doctorId: doctorId
					},
					url: 'loadAppointment.php',
					dataType: "html",
					success: function(response) {
						$("#responsecontainer").html(response);
					}
				});
			}
		});
	</script>
</body>

</html>