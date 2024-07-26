<div id="preloader">
    <div class="loader"></div>
</div>
<!-- Vertical navbar -->
<div class="navbar-sidebar">
    <div class="vertical-nav bg-white" id="sidebar">
        <div class="sidebar-header">
            <a href="#" data-toggle="tooltip" data-placement="bottom" title="" class="logo-src" data-original-title="Clinic"><?php echo $BRAND_NAME?></a>
        </div>
        <!-- Sidebar Inner -->
        <div class="sidebar-inner">
            <ul class="nav flex-column bg-white mb-0" id="metismenu">
                <!-- Upper -->
                <p class="sidebar-heading px-3 pb-1 mb-0">Utama</p>
                <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'index.php') !== FALSE) {echo 'mm-active';} ?>">
                    <a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt mr-3 fa-fw"></i>Beranda</a>
                </li>
                <?php if ($clinic_row["clinic_status"] == 1) { ?>
                    <li class="nav-item <?php if (preg_match('/(profile)/',$_SERVER["REQUEST_URI"]) == TRUE) {echo 'mm-active';} ?>">
                        <a href="profile.php" class="nav-link"><i class="fas fa-clinic-medical mr-3 fa-fw"></i>Profil Klinik</a> 
                    </li>
                    <li class="nav-item <?php if (preg_match('/(doctor)/',$_SERVER["REQUEST_URI"]) == TRUE) {echo 'mm-active';} ?>">
                        <a href="#" class="nav-link has-arrow" aria-expanded="false"><i class="fas fa-stethoscope mr-3 fa-fw"></i>Dokter</a>
                        <ul class="side-collapse">
                            <a href="doctor-list.php" class="nav-link"><i class="fas fa-list-ol mr-3 fa-fw"></i>Daftar Dokter</a>
                            <a href="doctor-add.php" class="nav-link"><i class="fa fa-user-plus mr-3 fa-fw"></i>Tambah Dokter</a>
                        </ul>
                    </li>
                    <li class="nav-item <?php if (preg_match('/(apoteker)/',$_SERVER["REQUEST_URI"]) == TRUE) {echo 'mm-active';} ?>">
                        <a href="#" class="nav-link has-arrow" aria-expanded="false"><i class="fas fa-pills mr-3 fa-fw"></i>Apoteker</a>
                        <ul class="side-collapse">
                            <a href="apoteker-list.php" class="nav-link"><i class="fas fa-users mr-3 fa-fw"></i>Daftar Apoteker</a>
                            <a href="apoteker-add.php" class="nav-link"><i class="fas fa-user-plus mr-3 fa-fw"></i>Tambah Apoteker</a>
                        </ul>
                    </li>
                    <li class="nav-item <?php if (preg_match('/(perawat)/',$_SERVER["REQUEST_URI"]) == TRUE) {echo 'mm-active';} ?>">
                        <a href="#" class="nav-link has-arrow" aria-expanded="false"><i class="fas fa-stethoscope mr-3 fa-fw"></i>Perawat</a>
                        <ul class="side-collapse">
                            <a href="perawat-list.php" class="nav-link"><i class="fas fa-users mr-3 fa-fw"></i>Daftar Perawat</a>
                            <a href="perawat-add.php" class="nav-link"><i class="fas fa-user-plus mr-3 fa-fw"></i>Tambah Perawat</a>
                        </ul>
                    </li>
                    <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'patient-list.php') !== FALSE) {echo 'mm-active';} ?>">
                        <a href="patient-list.php" class="nav-link" ><i class="fas fa-calendar-check mr-3 fa-fw"></i>Riwayat Janji Temu</a>
                    </li>
                    <!-- Lower -->
                    <p class="sidebar-heading px-3 pb-1 mb-0">Lainnya</p>
                    <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'announcement.php') !== FALSE) {echo 'mm-active';} ?>">
                        <a href="announcement.php" class="nav-link"><i class="fa fa-bullhorn mr-3 fa-fw"></i>Pengumuman</a>
                    </li>
                    <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'speciality.php') !== FALSE) {echo 'mm-active';} ?>">
                        <a href="speciality.php" class="nav-link"><i class="fas fa-tags mr-3 fa-fw"></i>Layanan Poli</a>
                    </li>
                <?php } ?>
                <!-- End Lower -->
            </ul>
        </div>
        <!-- Sidebar Inner -->  
    </div>
</div>
<!-- End vertical navbar -->
