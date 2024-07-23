<div id="preloader">
    <div class="loader"></div>
</div>

<!-- Vertical navbar -->
<div class="navbar-sidebar">
    <div class="vertical-nav bg-white sidebar-shadow" id="sidebar">
        <div class="sidebar-header">
            <a href="#" data-toggle="tooltip" data-placement="bottom" title="" class="logo-src" data-original-title="Clinic"><?php echo $BRAND_NAME?></a>
        </div>
        <!-- Sidebar Inner -->
        <div class="sidebabr-inner">
            <ul class="nav flex-column bg-white mb-0" id="metismenu">
                <!-- Upper -->
                <p class="sidebar-heading px-3 pb-1 mb-0">Utama </p>
                <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'index.php') !== FALSE) {echo 'mm-active';} ?>">
                    <a href="index.php" class="nav-link"><i class="fas fa-th-large mr-3 fa-fw"></i>Beranda</a>
                </li>
                <li class="nav-item <?php if (preg_match('/(clinic)/',$_SERVER["REQUEST_URI"]) == TRUE) {echo 'mm-active';} ?>">
                    <a href="clinic-list.php" class="nav-link " ><i class="fas fa-clinic-medical mr-3 fa-fw"></i>Konfirmasi Rumah Sakit</a>
                    
                </li>
                
                <!-- <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'schedule.php') !== FALSE) {echo 'mm-active';} ?>">
                    <a href="schedule.php" class="nav-link" ><i class="fa fa-user-clock mr-3 fa-fw"></i>Schedule</a>
                </li> -->
                <!-- <li class="nav-item <?php if (preg_match('/(schedule)/',$_SERVER["REQUEST_URI"]) == TRUE) {echo 'mm-active';} ?>">
                    <a href="#" class="nav-link has-arrow" aria-expanded="false"><i class="fa fa-user-clock mr-3 fa-fw"></i>Schedule</a>
                    <ul class="side-collapse">
                        <a href="#" class="nav-link"><i class="fa fa-calendar mr-3 fa-fw"></i>View Schedule</a>
                        <a href="#" class="nav-link"><i class="fa fa-plus mr-3 fa-fw"></i>Add Schedule</a>
                    </ul>
                </li> -->
                <!-- End Upper -->
                <!-- Lower -->
                <!-- <p class="sidebar-heading px-3 pb-1 mb-0">Others</p>
                <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'speciality.php') !== FALSE) {echo 'mm-active';} ?>">
                    <a href="speciality.php" class="nav-link"><i class="fas fa-tags mr-3 fa-fw"></i>Speciality</a>
                </li> -->
                <!-- <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'language.php') !== FALSE) {echo 'mm-active';} ?>">
                    <a href="language.php" class="nav-link"><i class="fas fa-language mr-3 fa-fw"></i>Langauge</a>
                </li> -->
                <!-- <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'report.php') !== FALSE) {echo 'mm-active';} ?>">
                    <a href="report.php" class="nav-link"><i class="fa fa-chart-bar mr-3 fa-fw"></i>Report</a>
                </li> -->
                <!-- End Lower -->
            </ul>
        </div>
        <!-- Sidebar Inner -->
    </div>
</div>
<!-- End vertical navbar -->