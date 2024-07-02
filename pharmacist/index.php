<?php
require_once ('../config/autoload.php');
include ('includes/path.inc.php');
include ('includes/session.inc.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$id_resep = $_POST['id_resep'];
	$status_pembuatan = $_POST['status_pembuatan'];

	$sql = "UPDATE resep SET status_pembuatan = ? WHERE id_resep = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param('ii', $status_pembuatan, $id_resep);

	if ($stmt->execute()) {
		echo 'success';
	} else {
		echo 'error';
	}

	$stmt->close();
	$conn->close();
	exit;
}

function fetchResepData($conn)
{
	$sql = "SELECT resep.id_resep, resep.status_pembuatan, resep.dosis, obat.nama_obat AS nama_obat,
                   CONCAT(patients.patient_firstname, ' ', patients.patient_lastname) AS patient_name
            FROM resep
            JOIN patients ON resep.patient_id = patients.patient_id
            JOIN obat ON resep.nama_obat = obat.id_obat";
	$result = $conn->query($sql);

	if ($result === false) {
		die("Error: " . $conn->error);
	}

	$data = array();
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
	}
	return $data;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include CSS_PATH; ?>
	<link rel="stylesheet" href="../assets/css/clinic/index.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>

<body>
	<?php include NAVIGATION; ?>
	<div class="page-content" id="content">
		<?php include HEADER; ?>
		<!-- Page content -->

		<div class="col-md-12">
			<!-- Card Content -->
			<div class="card patient-status-bar">
				<div class="card-body">
					<div class="d-flex bd-highlight">
						<div class="flex-fill bd-highlight">
							<p class="text-muted text-center">Pharmacist Info</p>
							<h5 class="font-weight-bold text-center"> Agung</h5>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="col-md-12">
			<!-- Card Content -->
			<div class="card">
				<div class="card-body">
					<!-- Datatable -->
					<?php
					function headerTable()
					{
						$header = array("ID", "Status Pembuatan", "Dosis", "Nama Obat", "Nama Patient", "Action");
						foreach ($header as $head) {
							echo "<th>" . $head . "</th>" . PHP_EOL;
						}
					}

					$resepData = fetchResepData($conn);
					?>

					<div class="col-md-12 mb-3">
						<h4 class="font-weight-bold text-center">Daftar Resep</h4>
					</div>


					<div class="data-tables">
						<table id="datatable" class="table table-responsive-lg nowrap">
							<thead>
								<tr>
									<?php headerTable(); ?>
								</tr>
							</thead>
							<tbody id="responsecontainer">
								<?php
								foreach ($resepData as $row) {
									$status_pembuatan = $row['status_pembuatan'] == 1 ? 'sudah selesai' : 'belum selesai';
									$status_color = $row['status_pembuatan'] == 1 ? 'text-success' : 'text-danger'; // Define color based on status
								
									echo "<tr>";
									echo "<td>{$row['id_resep']}</td>";
									echo "<td class='{$status_color}'>{$status_pembuatan}</td>"; // Apply color class
									echo "<td>{$row['dosis']}</td>";
									echo "<td>{$row['nama_obat']}</td>";
									echo "<td>{$row['patient_name']}</td>";
									echo "<td><button class='btn btn-primary' onclick='openStatusModal({$row['id_resep']}, {$row['status_pembuatan']})'>Action</button></td>";
									echo "</tr>";
								}

								?>
							</tbody>
						</table>
					</div>
					<!-- End Datatable -->
				</div>
			</div>
			<!-- End Card Content -->
		</div>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
		aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="statusModalLabel">Change Status</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="statusForm">
						<input type="hidden" id="resepId" name="id_resep">
						<div class="form-group">
							<label for="status">Status</label>
							<select class="form-control" id="status" name="status_pembuatan">
								<option value="0">Belum Selesai</option>
								<option value="1">Sudah Selesai</option>
							</select>
						</div>
						<button type="submit" class="btn btn-primary">Save changes</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<?php include JS_PATH; ?>

	<script>
		function openStatusModal(id_resep, status_pembuatan) {
			document.getElementById('resepId').value = id_resep;
			document.getElementById('status').value = status_pembuatan;
			$('#statusModal').modal('show');
		}

		document.getElementById('statusForm').addEventListener('submit', function (event) {
			event.preventDefault();

			const formData = new FormData(this);
			const id_resep = formData.get('id_resep');
			const status_pembuatan = formData.get('status_pembuatan');

			fetch('', {
				method: 'POST',
				body: formData
			})
				.then(response => response.text())
				.then(data => {
					if (data == 'success') {
						$('#statusModal').modal('hide');
						location.reload(); // Reload the page to see changes
					} else {
						alert('Failed to update status');
					}
				})
				.catch(error => {
					console.error('Error:', error);
				});
		});
	</script>
</body>

</html>