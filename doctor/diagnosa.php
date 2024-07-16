<?php
// Ensure this part is at the top of your script
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../config/autoload.php');
include('./includes/path.inc.php');
include('./includes/session.inc.php');

// Koneksi ke database
$host = 'localhost';
$db = 'clinic_appointment';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Koneksi database gagal: " . $e->getMessage();
    exit;
}

// Debug code to print GET parameters
echo '<pre>';
print_r($_GET);
echo '</pre>';

// Ambil id_janji_temu dari URL
$id_janji_temu = isset($_GET['id_janji_temu']) ? $_GET['id_janji_temu'] : null;

// Jika id_janji_temu tidak ada, redirect ke halaman lain atau tampilkan pesan error
if (!$id_janji_temu) {
    echo "ID janji temu tidak ditemukan.";
    exit;
}

// Ambil data janji temu dari database
$query = "SELECT * FROM janji_temu WHERE id_janji_temu = :id_janji_temu";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_janji_temu' => $id_janji_temu]);
$appointment = $stmt->fetch();

// Jika data janji temu tidak ditemukan, tampilkan pesan error
if (!$appointment) {
    echo "Data janji temu tidak ditemukan.";
    exit;
}

// Define your functions here
function calculateAge($birthDate)
{
    $birthDate = new DateTime($birthDate);
    $today = new DateTime('today');
    $age = $birthDate->diff($today)->y;
    return $age;
}

// Ambil data riwayat penyakit pasien dari database
$riwayatPenyakitQuery = "SELECT p.nama 
                         FROM riwayat_penyakit_pasien rpp
                         JOIN penyakit p ON rpp.id_penyakit = p.id_penyakit
                         WHERE rpp.id_janji_temu = :id_janji_temu";
$riwayatPenyakitStmt = $pdo->prepare($riwayatPenyakitQuery);
$riwayatPenyakitStmt->execute(['id_janji_temu' => $id_janji_temu]);
$riwayatPenyakit = $riwayatPenyakitStmt->fetchAll();

// Ambil data riwayat penyakit keluarga dari database
$riwayatKeluargaQuery = "SELECT p.nama 
                         FROM riwayat_ortu_pasien rop
                         JOIN penyakit p ON rop.id_penyakit = p.id_penyakit
                         WHERE rop.id_janji_temu = :id_janji_temu";
$riwayatKeluargaStmt = $pdo->prepare($riwayatKeluargaQuery);
$riwayatKeluargaStmt->execute(['id_janji_temu' => $id_janji_temu]);
$riwayatKeluarga = $riwayatKeluargaStmt->fetchAll();

// Ambil data riwayat alergi dari database
$riwayatAlergiQuery = "SELECT a.nama 
                       FROM riwayat_alergi_pasien rap
                       JOIN alergi a ON rap.id_alergi = a.id_alergi
                       WHERE rap.id_janji_temu = :id_janji_temu";
$riwayatAlergiStmt = $pdo->prepare($riwayatAlergiQuery);
$riwayatAlergiStmt->execute(['id_janji_temu' => $id_janji_temu]);
$riwayatAlergi = $riwayatAlergiStmt->fetchAll();

// Ambil data riwayat operasi dari database
$riwayatOperasiQuery = "SELECT o.nama 
                        FROM riwayat_operasi_pasien rop
                        JOIN operasi o ON rop.id_operasi = o.id_operasi
                        WHERE rop.id_janji_temu = :id_janji_temu";
$riwayatOperasiStmt = $pdo->prepare($riwayatOperasiQuery);
$riwayatOperasiStmt->execute(['id_janji_temu' => $id_janji_temu]);
$riwayatOperasi = $riwayatOperasiStmt->fetchAll();

// Ambil data riwayat perawatan dari database
$riwayatPerawatanQuery = "SELECT pb.nama 
                          FROM riwayat_pengobatan_besar_pasien rpbp
                          JOIN pengobatan_besar pb ON rpbp.id_pengobatan_besar = pb.id_pengobatan_besar
                          WHERE rpbp.id_janji_temu = :id_janji_temu";
$riwayatPerawatanStmt = $pdo->prepare($riwayatPerawatanQuery);
$riwayatPerawatanStmt->execute(['id_janji_temu' => $id_janji_temu]);
$riwayatPerawatan = $riwayatPerawatanStmt->fetchAll();

// Ambil data obat dari database
$obatQuery = "SELECT id_obat, nama_obat FROM obat";
$obatStmt = $pdo->prepare($obatQuery);
$obatStmt->execute();
$obatList = $obatStmt->fetchAll();

// Ambil data dosis dari database
$dosisQuery = "SELECT id_dosis, deskripsi_dosis FROM dosis";
$dosisStmt = $pdo->prepare($dosisQuery);
$dosisStmt->execute();
$dosisList = $dosisStmt->fetchAll();

// Ambil data frekuensi dari database
$frekuensiQuery = "SELECT id_frekuensi, deskripsi_frekuensi FROM frekuensi";
$frekuensiStmt = $pdo->prepare($frekuensiQuery);
$frekuensiStmt->execute();
$frekuensiList = $frekuensiStmt->fetchAll();

// Ambil data diagnosis dari database
$diagnosisQuery = "SELECT id_diagnosis, nama_diagnosis FROM diagnosis";
$diagnosisStmt = $pdo->prepare($diagnosisQuery);
$diagnosisStmt->execute();
$diagnosisList = $diagnosisStmt->fetchAll();

// Ambil data tindak lanjut dari database
$tindakLanjutQuery = "SELECT id_tindak_lanjut, deskripsi_tindak_lanjut FROM tindak_lanjut";
$tindakLanjutStmt = $pdo->prepare($tindakLanjutQuery);
$tindakLanjutStmt->execute();
$tindakLanjutList = $tindakLanjutStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = isset($_POST['diagnosis']) ? $_POST['diagnosis'] : [];
    $followUp = isset($_POST['followUp']) ? $_POST['followUp'] : [];
    $medications = isset($_POST['medications']) ? $_POST['medications'] : [];
    $suggestions = isset($_POST['suggestions']) ? $_POST['suggestions'] : '';

    // Insert data into riwayat_medis
    $riwayatQuery = "INSERT INTO riwayat_medis (id_janji_temu, saran_dokter) VALUES (:id_janji_temu, :saran_dokter)";
    $riwayatStmt = $pdo->prepare($riwayatQuery);
    $riwayatStmt->execute(['id_janji_temu' => $id_janji_temu, 'saran_dokter' => $suggestions]);
    $riwayatId = $pdo->lastInsertId();

    // Insert data into diagnosis_pasien
    foreach ($diagnosis as $diag) {
        $diagQuery = "INSERT INTO diagnosis_pasien (id_riwayat_medis, diagnosis_id) VALUES (:id_riwayat_medis, :diagnosis_id)";
        $diagStmt = $pdo->prepare($diagQuery);
        $diagStmt->execute(['id_riwayat_medis' => $riwayatId, 'diagnosis_id' => $diag]);
    }

    // Insert data into tindak_lanjut_pasien
    foreach ($followUp as $follow) {
        $followQuery = "INSERT INTO tindak_lanjut_pasien (id_riwayat_medis, id_tindak_lanjut) VALUES (:id_riwayat_medis, :id_tindak_lanjut)";
        $followStmt = $pdo->prepare($followQuery);
        $followStmt->execute(['id_riwayat_medis' => $riwayatId, 'id_tindak_lanjut' => $follow]);
    }

    // Insert data into obat_pasien
    foreach ($medications as $medication) {
        $id_obat = isset($medication['name']) ? $medication['name'] : null;
        $id_dosis = isset($medication['dose']) ? $medication['dose'] : null;
        $id_frekuensi = isset($medication['frequency']) ? $medication['frequency'] : null;
        $tanggal_mulai = isset($medication['period_start']) ? $medication['period_start'] : null;
        $tanggal_selesai = isset($medication['period_end']) ? $medication['period_end'] : null;

        if ($id_obat && $id_dosis && $id_frekuensi && $tanggal_mulai && $tanggal_selesai) {
            $medQuery = "INSERT INTO obat_pasien (id_riwayat_medis, id_obat, id_dosis, id_frekuensi, tanggal_mulai, tanggal_selesai) 
                         VALUES (:id_riwayat_medis, :id_obat, :id_dosis, :id_frekuensi, :tanggal_mulai, :tanggal_selesai)";
            $medStmt = $pdo->prepare($medQuery);
            $medStmt->execute([
                'id_riwayat_medis' => $riwayatId,
                'id_obat' => $id_obat,
                'id_dosis' => $id_dosis,
                'id_frekuensi' => $id_frekuensi,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai
            ]);
        }
    }

    echo "<script>alert('Data berhasil disimpan!'); window.location.href='appointment.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        .patient-info {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .patient-info .info-item {
            margin-bottom: 10px;
        }

        .patient-info .info-item span {
            margin-left: 10px;
        }

        .medical-history {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .medical-history .history-item {
            margin-bottom: 10px;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .info-item i {
            margin-right: 10px;
        }

        .section-header {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }

        .section-divider {
            height: 1px;
            background-color: #d3d3d3;
            border: none;
        }

        .section-content {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
        }

        .add-more-btn {
            margin-top: 10px;
        }

        .medication-fields {
            position: relative;
            padding-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            padding-right: 35px;
        }

        .remove-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
        }

        .history-item {
            margin-bottom: 20px;
        }

        .history-item label {
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            border: 1px solid #ccc;
            padding: 10px;
            display: flex;
            gap: 10px;
        }

        .form-control div {
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 13px;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            display: inline-block;
            margin-bottom: 6px;
        }

        .form-control span.active {
            background-color: #007bff;
            color: white;
        }

        .close-btn {
            margin-left: 5px;
            color: black;
            cursor: pointer;
        }

        .selected-diagnosis {
            display: block;
            height: auto;
            max-width: 100%;
            /* Ensure it doesn't exceed the parent width */
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }

        .selected-diagnosis .badge {
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 13px;
            display: inline-block;
            margin-bottom: 6px;
            color: black;
        }

        .selected-diagnosis .close-btn {
            cursor: pointer;
            display: inline-block;
            margin-left: 5px;
        }

        .selected-diagnosis,
        .selected-followup {
            display: block;
            height: auto;
            max-width: 100%;
            /* Ensure it doesn't exceed the parent width */
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }

        .selected-diagnosis .badge,
        .selected-followup .badge {
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 13px;
            display: inline-block;
            margin-bottom: 6px;
            color: black;
        }

        .selected-diagnosis .close-btn,
        .selected-followup .close-btn {
            cursor: pointer;
            display: inline-block;
            margin-left: 5px;
        }

        .dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }

        .selected-diagnosis .badge, .selected-followup .badge {
            display: inline-block;
            margin-bottom: 5px;
        }
        .dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <?php include NAVIGATION; ?>
    <div class="page-content" id="content">
        <?php include HEADER; ?>

        <div class="container mt-4">
            <div class="row">
                <div class="col-lg-5 col-md-6" style="border: 1px solid #000;">
                    <div class="patient-info text-center">
                        <div class="info-item-profile">
                            <i class="fas fa-user fa-3x rounded-circle"></i>
                            <span class="d-block mt-2"><?php echo htmlspecialchars($appointment['nama_lengkap']); ?></span>
                        </div>
                        <div class="info-item" style="text-align: center;">
                            <i class="fas fa-birthday-cake rounded-circle"></i>
                            <span class="d-block mt-2"> <?php echo calculateAge($appointment['tanggal_lahir']); ?> tahun</span>
                        </div>
                        <div class="info-item" style="text-align: center;">
                            <i class="fas fa-ruler-vertical rounded-circle"></i>
                            <span class="d-block mt-2"><?php echo htmlspecialchars($appointment['tinggi_badan']); ?> cm</span>
                        </div>
                        <div class="info-item" style="text-align: center;">
                            <i class="fas fa-weight rounded-circle"></i>
                            <span class="d-block mt-2"><?php echo htmlspecialchars($appointment['berat_badan']); ?> kg</span>
                        </div>
                        <div class="info-item" style="text-align: center;">
                            <i class="fas fa-tachometer-alt rounded-circle"></i>
                            <span class="d-block mt-2"><?php echo htmlspecialchars($appointment['tensi']); ?> mmHg</span>
                        </div>

                    </div>
                </div>

                <div class="col-lg-7 col-md-6">
                    <h6 class="text-center" style="font-weight: bold;">Tentang Pasien</h6>
                    <hr style="height: 1px; background-color: #d3d3d3; border: none;">
                    <hr style="height: 6px; background-color: #d3d3d3; border: none;">

                    <div class="medical-history">
                        <div class="history-item">
                            <label>Riwayat Penyakit</label>
                            <span class="form-control" style="display: inline-block; height: auto;">
                                <?php
                                if (!empty($riwayatPenyakit)) {
                                    foreach ($riwayatPenyakit as $penyakit) {
                                        echo '<div>' . htmlspecialchars($penyakit['nama']) . '</div>';
                                    }
                                } else {
                                    echo '<div>Tidak ada riwayat penyakit.</div>';
                                }
                                ?>
                            </span>
                        </div>
                        <div class="history-item">
                            <label>Riwayat Penyakit Keluarga</label>
                            <span class="form-control" style="display: inline-block; height: auto;">
                                <?php
                                if (!empty($riwayatKeluarga)) {
                                    foreach ($riwayatKeluarga as $penyakit) {
                                        echo '<div>' . htmlspecialchars($penyakit['nama']) . '</div>';
                                    }
                                } else {
                                    echo '<div>Tidak ada riwayat penyakit keluarga.</div>';
                                }
                                ?>
                            </span>
                        </div>

                        <div class="history-item">
                            <label>Riwayat Alergi</label>
                            <span class="form-control" style="display: inline-block; height: auto;">
                                <?php
                                if (!empty($riwayatAlergi)) {
                                    foreach ($riwayatAlergi as $alergi) {
                                        echo '<div>' . htmlspecialchars($alergi['nama']) . '</div>';
                                    }
                                } else {
                                    echo '<div>Tidak ada riwayat alergi.</div>';
                                }
                                ?>
                            </span>
                        </div>

                        <div class="history-item">
                            <label>Riwayat Operasi 5 Tahun Terakhir</label>
                            <span class="form-control" style="display: inline-block; height: auto;">
                                <?php
                                if (!empty($riwayatOperasi)) {
                                    foreach ($riwayatOperasi as $operasi) {
                                        echo '<div>' . htmlspecialchars($operasi['nama']) . '</div>';
                                    }
                                } else {
                                    echo '<div>Tidak ada riwayat operasi.</div>';
                                }
                                ?>
                            </span>
                        </div>

                        <div class="history-item">
                            <label>Riwayat Perawatan 5 Tahun</label>
                            <span class="form-control" style="display: inline-block; height: auto;">
                                <?php
                                if (!empty($riwayatPerawatan)) {
                                    foreach ($riwayatPerawatan as $perawatan) {
                                        echo '<div>' . htmlspecialchars($perawatan['nama']) . '</div>';
                                    }
                                } else {
                                    echo '<div>Tidak ada riwayat perawatan.</div>';
                                }
                                ?>
                            </span>
                        </div>

                        <div class="history-item">
                            <label>Obat yang sedang dikonsumsi</label>
                            <span class="form-control">Data Obat yang sedang dikonsumsi</span>
                        </div>
                    </div>
                </div>
            </div>
            <a href="#" class="btn btn-link">Tampilkan Riwayat Medis Sebelumnya?</a>
        </div>

        <!-- Mulai Diagnosa Section -->
        <div class="container mt-4">
        <div class="section-header">
            <h6>Mulai Diagnosa</h6>
            <hr class="section-divider" style="height: 6px; width: 350px">
        </div>

        <form id="diagnosis-form" method="POST">
            <div class="section-content">
                <div class="section-title">Diagnosa</div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label>Diagnosis</label>
                        <div class="dropdown">
                            <input type="text" class="form-control dropdown-toggle" id="dropdownDiagnosisButton"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                   placeholder="Pilih Diagnosis" autocomplete="off">
                            <div class="dropdown-menu w-100" aria-labelledby="dropdownDiagnosisButton">
                                <div id="diagnosisList">
                                    <?php
                                    if (!empty($diagnosisList)) {
                                        foreach ($diagnosisList as $diagnosis) {
                                            echo '<a class="dropdown-item" data-id="' . $diagnosis['id_diagnosis'] . '">' . htmlspecialchars($diagnosis['nama_diagnosis']) . '</a>';
                                        }
                                    }
                                    ?>
                               
                               </div>
                            </div>
                        </div>
                        <div class="selected-diagnosis mt-2 form-control" style="height: auto;"></div>
                    </div>

                    <div class="col-md-6">
                        <label>Tindak Lanjut</label>
                        <div class="dropdown">
                            <input type="text" class="form-control dropdown-toggle" id="dropdownFollowUpButton"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                   placeholder="Pilih Tindak Lanjut" autocomplete="off">
                            <div class="dropdown-menu w-100" aria-labelledby="dropdownFollowUpButton">
                                <div id="followUpList">
                                    <?php
                                    if (!empty($tindakLanjutList)) {
                                        foreach ($tindakLanjutList as $tindakLanjut) {
                                            echo '<a class="dropdown-item" data-id="' . $tindakLanjut['id_tindak_lanjut'] . '">' . htmlspecialchars($tindakLanjut['deskripsi_tindak_lanjut']) . '</a>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="selected-followup mt-2 form-control" style="height: auto;"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Saran</label>
                    <textarea name="suggestions" class="form-control" rows="4"></textarea>
                </div>
            </div>

            <div class="section-content" id="medication-section">
                <div class="section-title">Obat</div>
                <div class="medication-fields">
                    <div class="remove-btn">
                        <i class="fas fa-trash-alt" style="color: #E4003A;"></i>
                    </div>

                    <div class="form-group row">
    <div class="col-md-6">
        <label>Nama Obat</label>
        <div class="dropdown">
            <input type="text" class="form-control dropdown-toggle" id="dropdownNamaObat"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                placeholder="Pilih Obat" autocomplete="off">
            <div class="dropdown-menu w-100" aria-labelledby="dropdownNamaObat">
                <div id="obatList">
                    <?php
                    if (!empty($obatList)) {
                        foreach ($obatList as $obat) {
                            echo '<a class="dropdown-item" data-id="' . $obat['id_obat'] . '">' . htmlspecialchars($obat['nama_obat']) . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <input type="hidden" name="medications[0][name]" value="">
    </div>
    <div class="col-md-6">
        <label>Dosis</label>
        <div class="dropdown">
            <input type="text" class="form-control dropdown-toggle" id="dropdownDosis"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                placeholder="Pilih Dosis" autocomplete="off">
            <div class="dropdown-menu w-100" aria-labelledby="dropdownDosis">
                <div id="dosisList">
                    <?php
                    if (!empty($dosisList)) {
                        foreach ($dosisList as $dosis) {
                            echo '<a class="dropdown-item" data-id="' . $dosis['id_dosis'] . '">' . htmlspecialchars($dosis['deskripsi_dosis']) . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <input type="hidden" name="medications[0][dose]" value="">
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <label>Frekuensi</label>
        <div class="dropdown">
            <input type="text" class="form-control dropdown-toggle" id="dropdownFrekuensi"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                placeholder="Pilih Frekuensi" autocomplete="off">
            <div class="dropdown-menu w-100" aria-labelledby="dropdownFrekuensi">
                <div id="frekuensiList">
                    <?php
                    if (!empty($frekuensiList)) {
                        foreach ($frekuensiList as $frekuensi) {
                            echo '<a class="dropdown-item" data-id="' . $frekuensi['id_frekuensi'] . '">' . htmlspecialchars($frekuensi['deskripsi_frekuensi']) . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <input type="hidden" name="medications[0][frequency]" value="">
    </div>

    <div class="col-md-6">
        <label>Periode Konsumsi</label>
        <div class="input-group">
            <input type="date" class="form-control" name="medications[0][period_start]" placeholder="Mulai">
            <div class="input-group-append">
                <span class="input-group-text">s/d</span>
            </div>
            <input type="date" class="form-control" name="medications[0][period_end]" placeholder="Selesai">
        </div>
    </div>
</div>

                </div>
            </div>
            <button type="button" class="btn btn-primary add-more-btn" id="add-more">Tambah</button>

            <div class="form-group row justify-content-center">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success save-btn btn-block">Simpan</button>
                </div>
            </div>
        </form>
    </div>

    <?php include JS_PATH; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        var medicationIndex = 1;

$('#add-more').click(function () {
    var medicationFields = $('.medication-fields:first').clone();
    medicationFields.find('input').val(''); // Clear the values of cloned inputs
    medicationFields.find('input').each(function() {
        var name = $(this).attr('name');
        var newName = name.replace(/\[\d+\]/, '[' + medicationIndex + ']');
        $(this).attr('name', newName);
    });
    $('#medication-section').append(medicationFields);
    $('#medication-section').append($('#add-more')); // Re-append the "Tambah" button to the end
    medicationIndex++;
});

// Remove medication fields
$(document).on('click', '.remove-btn', function () {
    $(this).closest('.medication-fields').remove();
});


            // Dropdown item click for Diagnosis
            $(document).on('click', '#diagnosisList .dropdown-item', function (e) {
                e.preventDefault();
                var selectedItem = $(this).text();
                var selectedDiagnosis = $('.selected-diagnosis');
                var newSpan = $('<span class="badge mr-1" style="color: black; background-color: #f0f0f0; display: inline-block;">' + selectedItem + ' <i class="fas fa-times close-btn" style="cursor: pointer;"></i></span>');

                selectedDiagnosis.append(newSpan);
                selectedDiagnosis.append('<input type="hidden" name="diagnosis[]" value="' + $(this).data('id') + '">');
            });

            // Remove selected diagnosis
            $(document).on('click', '.close-btn', function () {
                $(this).parent().remove();
            });

            // Search functionality for Diagnosis
            $('#dropdownDiagnosisButton').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#diagnosisList a').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Open dropdown on focus and show all items
            $('#dropdownDiagnosisButton').on('focus', function () {
                $(this).dropdown('toggle');
                $('#diagnosisList a').show(); // Show all items
            });

            // Dropdown item click for Follow-up
            $(document).on('click', '#followUpList .dropdown-item', function (e) {
                e.preventDefault();
                var selectedItem = $(this).text();
                var selectedFollowUp = $('.selected-followup');
                var newSpan = $('<span class="badge mr-1" style="color: black; background-color: #f0f0f0; display: inline-block;">' + selectedItem + ' <i class="fas fa-times close-btn" style="cursor: pointer;"></i></span>');

                selectedFollowUp.append(newSpan);
                selectedFollowUp.append('<input type="hidden" name="followUp[]" value="' + $(this).data('id') + '">');
                $('#dropdownFollowUpButton').val(''); // Clear the search input
                $('#dropdownFollowUpButton').dropdown('toggle'); // Close the dropdown after selection
            });

            // Search functionality for Follow-up
            $('#dropdownFollowUpButton').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#followUpList a').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Open dropdown on focus and show all items for Follow-up
            $('#dropdownFollowUpButton').on('focus', function () {
                $(this).dropdown('toggle');
                $('#followUpList a').show(); // Show all items
            });

              // Dropdown item click for Nama Obat
    $(document).on('click', '#obatList .dropdown-item', function (e) {
        e.preventDefault();
        var selectedItem = $(this).text();
        var dropdownToggle = $(this).closest('.dropdown').find('.dropdown-toggle');
        dropdownToggle.val(selectedItem); // Set the value of the input to the selected item
        dropdownToggle.dropdown('toggle'); // Close the dropdown
        dropdownToggle.closest('.form-group').find('input[type=hidden]').val($(this).data('id'));
    });

            // Search functionality for Nama Obat
            $('#dropdownNamaObat').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#obatList a').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Open dropdown on focus and show all items for Nama Obat
            $('#dropdownNamaObat').on('focus', function () {
                $(this).dropdown('toggle');
                $('#obatList a').show(); // Show all items
            });

            // Dropdown item click for Dosis
            $(document).on('click', '#dosisList .dropdown-item', function (e) {
                e.preventDefault();
                var selectedItem = $(this).text();
                var dropdownToggle = $(this).closest('.dropdown').find('.dropdown-toggle');
                dropdownToggle.val(selectedItem); // Set the value of the input to the selected item
                dropdownToggle.dropdown('toggle'); // Close the dropdown
                dropdownToggle.closest('.form-group').append('<input type="hidden" name="medications[][dose]" value="' + $(this).data('id') + '">');
            });

            // Search functionality for Dosis
            $('#dropdownDosis').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#dosisList a').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Open dropdown on focus and show all items for Dosis
            $('#dropdownDosis').on('focus', function () {
                $(this).dropdown('toggle');
                $('#dosisList a').show(); // Show all items
            });

            // Dropdown item click for Frekuensi
            $(document).on('click', '#frekuensiList .dropdown-item', function (e) {
                e.preventDefault();
                var selectedItem = $(this).text();
                var dropdownToggle = $(this).closest('.dropdown').find('.dropdown-toggle');
                dropdownToggle.val(selectedItem); // Set the value of the input to the selected item
                dropdownToggle.dropdown('toggle'); // Close the dropdown
                dropdownToggle.closest('.form-group').append('<input type="hidden" name="medications[][frequency]" value="' + $(this).data('id') + '">');
            });

            // Search functionality for Frekuensi
            $('#dropdownFrekuensi').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('#frekuensiList a').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Open dropdown on focus and show all items for Frekuensi
            $('#dropdownFrekuensi').on('focus', function () {
                $(this).dropdown('toggle');
                $('#frekuensiList a').show(); // Show all items
            });

            // Remove button click functionality (if needed)
            $(document).on('click', '.remove-btn .fas', function () {
                $(this).closest('.medication-fields').remove();
            });
        });
    </script>
</body>

</html>
