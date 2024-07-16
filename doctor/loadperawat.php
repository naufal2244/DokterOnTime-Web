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

if (isset($_POST['date']) && isset($_POST['session'])) {
    $date = $_POST['date'];
    $session = $_POST['session'];

    // Hitung sub-sesi berdasarkan sesi utama
    $subSessions = [];
    for ($i = 1; $i <= 3; $i++) {
        $subSessions[] = ($session - 1) * 3 + $i;
    }

    // Query untuk mengambil data janji_temu berdasarkan sesi
    $query = "SELECT nama_lengkap, nomor_antrian, session_id 
              FROM janji_temu 
              WHERE tanggal_janji = :date AND session_id IN (" . implode(',', $subSessions) . ")
              ORDER BY session_id ASC";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['date' => $date]);
    $appointments = $stmt->fetchAll();

    if (empty($appointments)) {
        echo '<tr><td colspan="4" class="text-center">Tidak Ada Janji Temu</td></tr>';
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

            // Format nomor antrian dengan 4 angka dari depan dan 3 angka dari belakang
            $formattedAntrian = substr($appointment['nomor_antrian'], 0, 4) . '-' . substr($appointment['nomor_antrian'], -3);

            echo '<tr>';
            echo '<td>' . htmlspecialchars($appointment['nama_lengkap']) . '</td>';
            echo '<td>' . $formattedAntrian . '</td>';
            echo '<td>' . sprintf('%02d:%02d - %02d:%02d', $startTime, $minuteOffset, $endTimeHour, $endTimeMinute) . '</td>';
            echo '<td class="text-center"><input type="checkbox" class="status-checkbox"></td>';
            echo '</tr>';
        }
    }
}
