    // Inside fetchAppointment.php
    <?php

    include('../config/autoload.php');

    // Koneksi ke database
    $host = 'localhost';
    $db = 'clinic_appointment';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        echo "Koneksi database gagal: " . $e->getMessage();
        exit;
    }

    // Fungsi untuk mendapatkan kelas status
    function statusClass($status)
    {
        switch ($status) {
            case "0":
                return "status-belum-periksa";
            case "1":
                return "status-sedang-periksa";
            case "2":
                return "status-sudah-periksa";
            case "3":
                return "status-tidak-hadir";
            default:
                return "";
        }
    }

    // Fungsi untuk mendapatkan teks status
    function statusText($status)
    {
        switch ($status) {
            case "0":
                return "Belum Periksa";
            case "1":
                return "Sedang Periksa";
            case "2":
                return "Sudah Periksa";
            case "3":
                return "Tidak Hadir";
            default:
                return "";
        }
    }

    if (isset($_POST['date']) && isset($_POST['session']) && isset($_POST['doctorId'])) {
        $date = $_POST['date'];
        $session = $_POST['session'];
        $doctor_id = $_POST['doctorId'];

        // Hitung sub-sesi berdasarkan sesi utama
        $subSessions = [];
        for ($i = 1; $i <= 3; $i++) {
            $subSessions[] = ($session - 1) * 3 + $i;
        }

        // Query untuk mengambil data janji_temu berdasarkan sesi dan doctor_id
        $query = "SELECT id_janji_temu, nama_lengkap, nomor_antrian, session_id, status_periksa 
                FROM janji_temu 
                WHERE doctor_id = :doctor_id AND tanggal_janji = :date AND session_id IN (" . implode(',', $subSessions) . ")
                ORDER BY nomor_antrian ASC";

        $stmt = $pdo->prepare($query);
        $stmt->execute(['doctor_id' => $doctor_id, 'date' => $date]);
        $appointments = $stmt->fetchAll();

        if (empty($appointments)) {
            echo '<td>Tidak Ada Janji Temu</td>';
        } else {
            foreach ($appointments as $appointment) {
                $startTime = 8 + floor(($appointment['session_id'] - 1) / 3);
                $minuteOffset = (($appointment['session_id'] - 1) % 3) * 20;
                $endTimeHour = $startTime;
                $endTimeMinute = $minuteOffset + 20;
                if ($endTimeMinute >= 60) {
                    $endTimeHour += 1;
                    $endTimeMinute -= 60;
                }
                $formattedAntrian = substr($appointment['nomor_antrian'], -3);

                echo '<tr>';
                echo '<td>' . htmlspecialchars($appointment['nama_lengkap']) . '</td>';
                echo '<td>' . $formattedAntrian . '</td>';
                echo '<td>' . sprintf('%02d:%02d - %02d:%02d', $startTime, $minuteOffset, $endTimeHour, $endTimeMinute) . '</td>';
                echo '<td><span class="status-label ' . statusClass($appointment['status_periksa']) . '">' . statusText($appointment['status_periksa']) . '</span></td>';

            echo '<td>
                    <form action="diagnosa.php" method="POST" style="display: inline;">
                        <input type="hidden" name="id_janji_temu" value="' . $appointment['id_janji_temu'] . '">
                        <input type="hidden" name="action" value="start">
                        <button type="submit" class="btn btn-diagnosa"><i class="fas fa-stethoscope" style="margin-right: 5px;"></i> Mulai Diagnosa</button>
                    </form>
                  </td>';
            echo '</tr>';
        }
    }
}
?>

