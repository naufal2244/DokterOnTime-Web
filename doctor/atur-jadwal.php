<?php
require_once('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

$doctor_id = $_SESSION['doctor_id'] ?? 0; // Default value jika $_SESSION['doctor_id'] tidak terdefinisi
$selected_day = $_GET['day'] ?? 1; // Default value jika $_GET['day'] tidak terdefinisi atau kosong

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_day = $_POST['selected_day'] ?? 1;
    $selected_availabilities = $_POST['availabilities'] ?? [];

    // Reset all availabilities to FALSE for the current doctor on the selected day
    $stmt_reset = $conn->prepare("UPDATE doctor_availabilities SET available = 0 WHERE doctor_id = ? AND day_id = ?");
    $stmt_reset->bind_param("ii", $doctor_id, $selected_day);
    $stmt_reset->execute();

    // Update selected availabilities to TRUE
    if (!empty($selected_availabilities)) {
        $stmt_update = $conn->prepare("UPDATE doctor_availabilities SET available = 1 WHERE doctor_id = ? AND day_id = ? AND session_id = ?");
        foreach ($selected_availabilities as $session_id) {
            $stmt_update->bind_param("iii", $doctor_id, $selected_day, $session_id);
            $stmt_update->execute();
        }
        $_SESSION['update_success'] = true;
    }

    // Redirect setelah proses POST selesai untuk menghindari pengiriman ulang form
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Fetch current availabilities for the doctor on the selected day
$availabilities = [];
$stmt_select_avail = $conn->prepare("SELECT session_id FROM doctor_availabilities WHERE doctor_id = ? AND day_id = ? AND available = 1");
$stmt_select_avail->bind_param("ii", $doctor_id, $selected_day);
$stmt_select_avail->execute();
$result = $stmt_select_avail->get_result();
while ($row = $result->fetch_assoc()) {
    $availabilities[] = $row['session_id'];
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Jumlah sesi per halaman
$offset = ($page - 1) * $limit;

// Query untuk mengambil data time blocks
$time_blocks = [];
$time_result = $conn->query("SELECT * FROM time_blocks LIMIT $limit OFFSET $offset");
while ($row = $time_result->fetch_assoc()) {
    $time_blocks[$row['time_block_id']] = $row;
}

// Query untuk menghitung total items
$total_result = $conn->query("SELECT COUNT(*) as total FROM time_blocks");
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $limit);

// Array of days
$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include CSS_PATH; ?>
    <style>
        .session-container {
            margin-left: 50px; /* Mengatur jarak dari kiri */
        }
        .update-btn-container {
            text-align: right; /* Mengatur posisi tombol di kanan bawah */
            margin-top: 20px; /* Tambahkan jarak ke atas */
        }
        .session-section {
            margin-bottom: 20px;
            margin-left: 40px; /* Mengatur margin kiri untuk sesi */
        }
        .session-day {
            font-size: 1em;
            font-weight: bold;
            padding: 10px;
            display: inline-block;
        }
        .form-check-inline {
            display: inline-block;
            margin-right: 10px;
        }
        .subsesi {
            margin-left: 20px; /* Menggeser subsesi ke kanan */
            margin-top: 10px; /* Menambahkan margin atas untuk memisahkan dengan sesi */
        }
        .session-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>
        <!-- Page content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- Dropdown for Days -->
                        <form method="POST" action="?day=<?= $selected_day ?>&page=<?= $page ?>">
    <div class="form-group">
        <label for="selectDay">Pilih Hari:</label>
        <select name="selected_day" class="form-control form-control-sm" id="selectDay" style="width: auto; display: inline-block;">
            <?php foreach ($days as $index => $day): ?>
                <option value="<?= $index + 1 ?>" <?= $selected_day == $index + 1 ? 'selected' : '' ?>><?= $day ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="session-day" id="selectedDay"><?= $days[$selected_day - 1] ?></div>

    <div class="session-container">
        <div class="session-table" id="sessionTable">
            <div class="form-check mt-1">
                <input type="checkbox" class="form-check-input" id="checkAll">
                <label class="form-check-label" for="checkAll">Check All</label>
            </div>
            <?php foreach ($time_blocks as $time_block): ?>
                <div class="session-section mt-3 ms-4">
                    <div class="session-header">
                        <input type="checkbox" class="form-check-input sesi-check" id="sesi<?= $time_block['time_block_id'] ?>-check" data-session="<?= $time_block['time_block_id'] ?>">
                        <strong class="ms-2">Sesi <?= $time_block['time_block_id'] ?> (<?= $time_block['start_time'] ?> - <?= $time_block['end_time'] ?>)</strong>
                    </div>
                    <div class="subsesi ms-4 mt-2">
                        <?php for ($sub_session = 1; $sub_session <= 3; $sub_session++):
                            $session_id = (($time_block['time_block_id'] - 1) * 3) + $sub_session;
                            $checked = in_array($session_id, $availabilities) ? 'checked' : '';
                            $start_time = date("H:i", strtotime($time_block['start_time']) + ($sub_session - 1) * 20 * 60);
                            $end_time = date("H:i", strtotime($time_block['start_time']) + $sub_session * 20 * 60);
                        ?>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input sesi sub-sesi-<?= $time_block['time_block_id'] ?>" name="availabilities[]" value="<?= $session_id ?>" id="sesi<?= $session_id ?>" <?= $checked ?> data-session="<?= $time_block['time_block_id'] ?>">
                                <label class="form-check-label" for="sesi<?= $session_id ?>"><?= $sub_session ?> (<?= $start_time ?> - <?= $end_time ?>)</label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Pagination -->
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?day=<?= $selected_day ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?day=<?= $selected_day ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?day=<?= $selected_day ?>&page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <div class="update-btn-container mt-3">
        <button type="submit" class="btn btn-primary">Update Availability</button>
    </div>
</form>


                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Content -->

    </div>

    <?php include JS_PATH; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <script>
    $(document).ready(function() {
        $('#selectDay').on('change', function() {
            var selectedDay = $(this).val();
            window.location.href = window.location.pathname + "?day=" + selectedDay + "&page=<?= $page ?>";
        });

        $('#checkAll').on('click', function() {
            var isChecked = $(this).is(':checked');
            $('.sesi').prop('checked', isChecked);
            $('.sesi-check').prop('checked', isChecked);
        });

        $('.sesi-check').on('click', function() {
            var isChecked = $(this).is(':checked');
            $(this).closest('.session-section').find('.sesi').prop('checked', isChecked);
        });

        $('.sesi').on('change', function() {
            var session = $(this).data('session');
            var allChecked = $('.sub-sesi-' + session).length === $('.sub-sesi-' + session + ':checked').length;
            $('#sesi' + session + '-check').prop('checked', allChecked);
        });

        $('.sesi').trigger('change'); // Trigger change event to initialize checkboxes

        // SweetAlert script for success message
        <?php if (isset($_SESSION['update_success']) && $_SESSION['update_success']): ?>
            var currentDay = <?= $selected_day ?>;
            var currentPage = <?= $page ?>;
            Swal.fire({
                title: "Great!",
                text: "Update Availability Successful!",
                icon: "success"
            }).then((result) => {
                if (result.isConfirmed || result.isDismissed) {
                    <?php unset($_SESSION['update_success']); ?>
                    window.location.href = "atur-jadwal.php?day=" + currentDay + "&page=" + currentPage; // Redirect to another page after closing the popup
                }
            });
        <?php endif; ?>
    });
</script>



</body>
</html>
