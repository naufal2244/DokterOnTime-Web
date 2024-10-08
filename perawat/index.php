<?php
session_start();
require_once('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

// Periksa apakah perawat sudah login
if (!isset($_SESSION['PerawatRoleLoggedIn']) || $_SESSION['PerawatRoleLoggedIn'] != 1) {
    header("Location: login_perawat.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        .status-label {
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            margin: 2px 0;
            width: 150px;
            text-align: center;
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

        #datepicker {
            width: 100%;
        }

        .bootstrap-datetimepicker-widget table td,
        .bootstrap-datetimepicker-widget table th {
            text-align: center;
        }

        .no-appointments {
            text-align: center;
            font-size: 1.2em;
            color: #777;
        }
    </style>
</head>

<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <div id="datepicker"></div>
                            <input type="hidden" name="date" id="selectedDate">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="session" class="col-form-label" style="margin-left: 20px;">Pilih Sesi :</label>
                            <div>
                                <select id="session" class="form-control form-control-sm ml-2">
                                    <?php for ($i = 1; $i <= 15; $i++) : ?>
                                        <option value="<?= $i ?>">Sesi <?= $i ?> (<?= sprintf('%02d:00 - %02d:00', 8 + $i - 1, 9 + $i - 1) ?>)</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="data-tables">
                            <table class="table table-responsive-lg nowrap">
                                <thead>
                                    <tr>
                                        <th>Nama Pasien</th>
                                        <th>No Antri</th>
                                        <th>Waktu Periksa</th>
                                        <th>Status Hadir</th>
                                    </tr>
                                </thead>
                                <tbody id="responsecontainer">
                                    <!-- Data akan diisi oleh AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include JS_PATH; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('#datepicker').datetimepicker({
                inline: true,
                format: 'YYYY-MM-DD',
                defaultDate: moment()
            }).on('dp.change', function(event) {
                var formatted = event.date.format('YYYY-MM-DD');
                $("#selectedDate").val(formatted);
                loadData(formatted, $('#session').val());
            });

            $('#session').change(function() {
                var selectedDate = $('#selectedDate').val();
                if (selectedDate) {
                    loadData(selectedDate, $(this).val());
                }
            });

            var today = moment().format('YYYY-MM-DD');
            $("#selectedDate").val(today);
            loadData(today, $('#session').val());

            function loadData(date, session) {
                $.ajax({
                    type: "POST",
                    url: 'loadPerawat.php',
                    data: {
                        date: date,
                        session: session
                    },
                    dataType: "html",
                    success: function(response) {
                        $("#responsecontainer").html(response);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });
    </script>
</body>

</html>