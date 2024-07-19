<?php
require_once('../config/autoload.php');
require_once('./includes/path.inc.php');
require_once('./includes/session.inc.php');

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$tanggal_dari = $conn->real_escape_string($_POST['inputTanggalDari']);
	$tanggal_sampai = $conn->real_escape_string($_POST['inputTanggalSampai']);

	if (empty($tanggal_dari)) {
		array_push($errors, "Tanggal Dari harus diisi");
	}
	if (empty($tanggal_sampai)) {
		array_push($errors, "Tanggal Sampai harus diisi");
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<?php include CSS_PATH; ?>
</head>

<body>
	<?php include NAVIGATION; ?>
	<div class="page-content" id="content">
		<?php include HEADER; ?>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<form name="report_frm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
							<?php echo display_error(); ?>
							<div class="form-group row">
								<label for="inputTanggalDari" class="col-sm-3 col-form-label text-right">Dari Tanggal</label>
								<div class="col-sm-6">
									<input type="text" name="inputTanggalDari" class="form-control form-control-sm" id="tanggal_dari">
								</div>
							</div>
							<div class="form-group row">
								<label for="inputTanggalSampai" class="col-sm-3 col-form-label text-right">Sampai Tanggal</label>
								<div class="col-sm-6">
									<input type="text" name="inputTanggalSampai" class="form-control form-control-sm" id="tanggal_sampai">
								</div>
							</div>
							<div class="d-flex justify-content-md-center pt-3">
								<button type="clear" class="btn btn-light btn-sm px-5 mr-2" name="clearbtn">Bersihkan</button>
								<button type="submit" class="btn btn-primary btn-sm px-5" name="generatebtn">Buat Laporan</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div id="responsecontainer"></div>

	</div>
	<?php include JS_PATH; ?>
	<script>
		function print() {
			var w = window.open('', '', 'left=0,top=0,width=800,height=600,toolbar=0,scrollbars=0,status=0');
			var html = $("#printContent").html();

			$(w.document.body).html(html);
			w.focus();
			w.print();
			w.close();
		}
	</script>
	<script type="text/javascript">
		$(function() {
			$('#tanggal_dari').datetimepicker({
				format: 'YYYY-MM-DD',
			});
			$('#tanggal_sampai').datetimepicker({
				format: 'YYYY-MM-DD',
				useCurrent: false,
			});

			$('#tanggal_dari').on('dp.change', function(e) {
				$('#tanggal_sampai').data('DateTimePicker').minDate(e.date);
			});
			$('#tanggal_sampai').on('dp.change', function(e) {
				$('#tanggal_dari').data('DateTimePicker').maxDate(e.date);
			});

		});
	</script>
	<script>
		function loadData(from, to) {
			$.ajax({
				type: "POST",
				data: {
					tanggal_dari: from,
					tanggal_sampai: to,
				},
				url: 'loadReport.php',
				dateType: "html",
				success: function(response) {
					$("#responsecontainer").html(response);
				}
			});
		}
	</script>
	<?php
	 if (isset($_POST['generatebtn'])) {
		if (count($errors) == 0) {
			?><script>
				loadData('<?=$tanggal_dari?>', '<?= $tanggal_sampai ?>')
			</script>
			<?php
		}
	 }
	?>
</body>

</html>
