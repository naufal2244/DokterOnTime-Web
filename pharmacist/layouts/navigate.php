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
                    <p class="sidebar-heading px-3 pb-1 mb-0">Main</p>
                    <li class="nav-item <?php if (stripos($_SERVER['REQUEST_URI'],'index.php') !== FALSE) {echo 'mm-active';} ?>">
                        <a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt mr-3 fa-fw"></i>Beranda</a>
                    </li>
                </ul>
            </div>
            <!-- Sidebar Inner -->
        </div>
    </div>
    <!-- End vertical navbar -->