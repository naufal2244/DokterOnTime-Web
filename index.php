<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="icon" href="assets/img/icon/favicon.ico" type="image/ico" sizes="16x16">
	<title>DokterOnTime</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
		integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="assets/css/all.min.css">
	<link href="https://fonts.googleapis.com/css?family=Lato|Poppins&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
	<style>
	html {
    scroll-behavior: smooth;
}

h2,
h3,
h4,
h5,
h6 {
    font-weight: 700;
}

body {
    /* font-family: 'Open Sans', sans-serif; */
    font-family: 'Lato', sans-serif;
    /* font-family: 'Poppins', sans-serif; */
}

/* .wrapper {
    background: #fff;
    margin-bottom: 270px;
    box-shadow: 0px 25px 10px -15px rgba(0,0,0,0.08); 
} */

.navbar {
    padding-top: 2rem;
}

.navbar .nav-item {
    margin: 0 .75rem 0;
}

.navbar-brand a {
    box-shadow: 0px 25px 10px -15px rgba(0, 0, 0, 0.08);
}

.nav-dropdown {
    border-radius: 10px;
    border: 0;
    padding: 0 1.2rem;
    background: linear-gradient(to right, #87e7ae 0%, #87e7ae 100%);
    box-shadow: 0px 25px 10px -10px rgba(0, 0, 0, 0.08);
}

.nav-dropdown a.dropdown-link {
    color: #f5f5f5 !important;
}

.btn-primary {
    color: #fff;
    background: linear-gradient(to right, #87e7ae 0%, #87e7ae 100%) !important;
    border-color: #87e7ae !important;
}

.btn-primary:hover {
    color: #fff;
    background-color: #87e7ae !important;
    border-color: #87e7ae !important;
    -webkit-box-shadow: none;
    box-shadow: none;
}

.btn-primary:focus {
    box-shadow: 0 0 0 0.2rem rgba(135, 231, 174, .5) !important;
}

.jumbotron {
    padding: 20% 0;
    background: url('./assets/img/background.jpg');
    background-repeat: no-repeat;
    background-position: 100% 10%;
    background-size: cover; /* memastikan gambar menutupi area dengan baik */
}

@media (max-width: 768px) {
    .jumbotron {
        background-position: 80% 10%; /* pindahkan gambar ke kanan ketika layar kecil */
    }
}

@media (max-width: 576px) {
    .jumbotron {
        background-position: 70% 10%; /* pindahkan gambar lebih ke kanan ketika layar sangat kecil */
    }
}


.jumbotron-title {
    font-size: 3rem;
    text-transform: capitalize;
}

section {
    padding: 3rem 0 6rem;
}

.section-title {
    margin-bottom: 6rem;
}

section img {
    margin-bottom: 2rem;
}

.feature-section {
    background: url('./assets/img/getty_patient_care.jpg');
    background-position: 0% 100%;
    background-size: 600px;
    background-repeat: no-repeat;
}

.feature-section .card {
    border-radius: 10px;
    box-shadow: 0px 25px 10px -15px rgba(0, 0, 0, 0.08);
    transition: .4s;
}

.feature-section .card:hover {
    transform: scale(1.1);
    box-shadow: 0px 25px 10px -15px rgba(0, 0, 0, 0.08);
}

.footer {
    width: 100%;
    height: auto;
    color: #333;
    bottom: 0px;
    left: 0px;
    padding: 45px 0 40px;
}

.footer a {
    color: #333;
}

.footer a:hover {
    color: #87e7ae;
    text-decoration: none;
}

.footer ul li {
    margin: .8rem 0;
}

.upper-footer {
    border-bottom: #f8f8f9;
    width: 100%;
}

.bottom-footer {
    margin-top: 10px;
}

.footer ul {
    list-style-type: none;
}

.footer ul li {
    margin-left: -40px;
}

.footer-link {
    text-align: right;
}

.bottom-footer-link {
    margin: 0 5px;
}

.top-button {
    position: absolute;
    right: 30px;
}

.top-scroll {
    padding: 10px 16px;
    background-color: #f2f2f2;
    border-radius: 5px;
    font-size: 20px;
    transition: .3s;
}

.top-scroll:hover {
    background-color: #dfdddd;
}

img.banner {
    width: 380px !important;
    height: 450px !important;
}

/*
FOR ANIMATION
*/

.slideanim {
    visibility: hidden;
}

.slide {
    /* The name of the animation */
    animation-name: slide;
    -webkit-animation-name: slide;
    /* The duration of the animation */
    animation-duration: 1s;
    -webkit-animation-duration: 1s;
    /* Make the element visible */
    visibility: visible;
}

/* Go from 0% to 100% opacity (see-through) and specify the percentage from when to slide in the element along the Y-axis */
@keyframes slide {
    0% {
        opacity: 0;
        transform: translateY(70%);
    }

    100% {
        opacity: 1;
        transform: translateY(0%);
    }
}

@-webkit-keyframes slide {
    0% {
        opacity: 0;
        -webkit-transform: translateY(70%);
    }

    100% {
        opacity: 1;
        -webkit-transform: translateY(0%);
    }
}

.developer-section {
    background-color: white;
    padding: 3rem 0;
}

.developer-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.developer-card {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    margin: 2rem 1;
    transition: transform 0.3s, box-shadow 0.3s;
    height: 300px; /* Tinggi total untuk memastikan kartu sejajar */
}

.developer-img {
    width: 150px;
    height: 150px;
    border-radius: 10%;
    margin-bottom: 1rem;
}

.developer-name {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    height: 2rem; /* Tinggi tetap untuk nama */
    display: flex;
    align-items: center;
    justify-content: center;
}

.developer-role {
    color: black;
    font-size: 1rem;
    margin-bottom: 0.5rem;
    height: 2rem; /* Tinggi tetap untuk peran */
    display: flex;
    align-items: center;
    justify-content: center;
}

.developer-description {
    font-size: 0.9rem;
    margin-bottom: 1rem;
    height: 4rem; /* Tinggi tetap untuk deskripsi */
    display: flex;
    align-items: center;
    justify-content: center;
}

.social-icons {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: auto; /* Untuk memastikan ikon sosial berada di bawah */
}


.social-icons a {
    margin: 0 0.5rem;
    background: #f09433; 
background: -moz-linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); 
background: -webkit-linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); 
background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); 
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f09433', endColorstr='#bc1888',GradientType=1 );
    font-size: 1.2rem;

	width: 28px;
	height: 28px;
	border-radius: 50px;
}

.social-icons a:hover {
    color: #0056b3;
}

@media (max-width: 768px) {
    .developer-card {
        margin-top: 2rem;
        margin-left: 1rem;
        margin-right: 1rem;
    }
}

@media (max-width: 768px) {
    .developer-card {
        margin: 1rem auto;
    }
}

@media (min-width: 1200px) {
    .col-lg-2 {
        max-width: 16.666667%;
    }
}


	</style>
</head>

<body>
	<div class="wrapper">
		<nav class="navbar navbar-expand-lg navbar-light">
			<div class="container">
				<a class="navbar-brand" href="#"><b>DokterOnTime</b></a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
					aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarNav">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
							<a class="nav-link" href="#about">Tentang</a>
						</li>
						<!-- <li class="nav-item">
							<a class="nav-link" href="#howitwork">Cara Kerja</a>
						</li> -->
						<li class="nav-item">
							<a class="nav-link" href="#feature">Fitur</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#testimonial">Kata Mereka</a>
						</li>
						<li class="nav-item">
							<!-- Go Buy some van to cook -->
							<a class="nav-link" href="#developer">Developer</a>
						</li>
						<li class="nav-item dropdown nav-dropdown">
							<a class="nav-link dropdown-toggle dropdown-link" href="#" id="navbarDropdownMenuLink"
								role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Masuk / Daftar
							</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
								<a class="dropdown-item" href="#patient">Pasien</a>
								<a class="dropdown-item" href="clinic/login.php">Admin Rumah Sakit</a>
								<a class="dropdown-item" href="doctor/login.php">Dokter</a>
								<a class="dropdown-item" href="pharmacist/login.php">Apoteker</a>
								<a class="dropdown-item" href="perawat/login.php">Perawat</a>
							
							</div>
						</li>
					</ul>
				</div>
			</div>
		</nav>

		<div class="jumbotron jumbotron-fluid">
			<div class="container">
				<h2 class="jumbotron-title">Bergabung Sekarang!</h2>
				<p>Kesehatan Anda adalah Prioritas Kami</p>
			</div>
		</div>

		<section class="about-section text-center" id="about">
			<h2 class="section-title">Tentang</h2>
			<div class="container">
				<div class="row slideanim">
					<div class="col-6 col-md-4">
						<div class="image">
							<img src="https://img.icons8.com/dusk/128/000000/counselor.png" alt="" title="">
						</div>
						<div class="desc">
							<h4 class="mb-3">Dapatkan No Antri <br> dari Rumah</h4>
							<p class="paragraph">
							Buat janji temu kapan saja dan dari mana saja. Dapatkan nomor antrian 
							dengan waktu periksa yang pasti sehingga tahu persis kapan harus tiba di 
							rumah sakit tanpa perlu menunggu antrian panjang melelahkan.
							</p>
						</div>
					</div>
					<div class="col-6 col-md-4">
						<div class="image">
							<img src="assets/img/clinic-letter.png" alt="" title="">
						</div>
						<div class="desc">
							<h4 class="mb-3">Ambil Obat</h4>
							<p class="paragraph">
							Cek status pembuatan obat dari ponsel. 
							Ketahui apakah obat sudah siap diambil sehingga tidak perlu menunggu antrian di rumah sakit.
							</p>
						</div>
					</div>
					<div class="col-6 col-md-4">
						<div class="image">
							<img src="https://img.icons8.com/dusk/128/000000/heart-with-pulse.png" alt="" title="">
						</div>
						<div class="desc">
							<h4 class="mb-3">Riwayat Medis Digital</h4>
							<p class="paragraph">
							Lihat riwayat medis secara digital di ponsel. 
							 Nikmati kemudahan akses tanpa ribet, semua informasi kesehatan tersedia dalam genggaman.
							</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- <section class="text-center" id="howitwork">
			<h2 class="section-title">How it Work</h2>
			<div class="container">
				<div class="row slideanim">
				</div>
			</div>
		</section> -->

		<section class="feature-section text-center" id="feature">
			<h2 class="section-title">Fitur</h2>
			<div class="container">
				<div class="row slideanim mb-5">
					<div class="col-sm-4">
						<div class="card">
							<div class="card-body">
							<img width="96" height="96" src="https://img.icons8.com/color/96/triangular-bandage.png" alt="triangular-bandage"/>
								<h5 class="card-title">Pasien</h5>
								<p class="card-text">Buat Janji Temu dengan satu klik. <br>
								Nikmati efisiensi dan kemudahan dalam satu aplikasi!
								</p>
								<a href="#" class="btn btn-primary">Pasien</a>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<div class="card-body">
								<img src="https://img.icons8.com/officexs/80/000000/triangular-bandage.png">
								<h5 class="card-title">Dokter</h5>
								<p class="card-text">Kelola janji temu dengan mudah dan efisien. <br>
									Lihat jadwal harian dan atur sesi hadir dengan mudah.
								</p>
								<a href="doctor/index.php" class="btn btn-primary">Dokter</a>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card">
							<div class="card-body">
								<img src="https://img.icons8.com/officexs/80/000000/medical-doctor.png">
								<h5 class="card-title">Perawat</h5>
								<p class="card-text">Pantau dan kelola janji temu dengan efisien. <br>
								Dukung dokter dalam memberikan perawatan optimal.
								</p>
								<a href="perawat/index.php" class="btn btn-primary">Perawat</a>
							</div>
						</div>
					</div>
				</div>
				<div class="container">
    <div class="row slideanim justify-content-center">
        <div class="col-sm-4">
            <div class="card">
                <div class="card-body text-center">
                    <img width="96" height="96" src="https://img.icons8.com/fluency/96/pharmacy-shop.png" alt="pharmacy-shop"/>
                    <h5 class="card-title">Apoteker</h5>
                    <p class="card-text">Kelola dan pantau resep obat dengan mudah.</p>
                    <a href="pharmacist/index.php" class="btn btn-primary">Apoteker</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="https://img.icons8.com/officexs/80/000000/clinic.png" alt="clinic"/>
                    <h5 class="card-title">Admin Rumah Sakit</h5>
                    <p class="card-text">Atur dan kelola operasional rumah sakit secara efisien untuk memastikan layanan yang optimal.</p>
                    <a href="clinic/index.php" class="btn btn-primary">Admin RS</a>
                </div>
            </div>
        </div>
    </div>
</div>

			</div>
		</section>

		<section class="text-center" id="testimonial">
			<h2 class="section-title">Kata Mereka</h2>
			<div id="carouselContent" class="carousel slide" data-ride="carousel">
				<div class="carousel-inner text-center" role="listbox">
					<div class="carousel-item active">
						<div class="avatar"><img
						src="https://img.icons8.com/color/48/000000/circled-user-female-skin-type-7.png">
								</div>
						<p>Sangat Mmebantu Saya dalam memantau antrian.</p>
						<div class="quote">
							<p><strong>- Risa</strong><br>Ibu Rumah Tangga</p>
						</div>
					</div>
					<div class="carousel-item">
						<div class="avatar"><img
						src="https://img.icons8.com/color/48/000000/circled-user-male-skin-type-7.png">
								</div>
						<p>Mempermudah saya membuat janji temu ditengah kesibukkan saya.</p>
						<div class="quote">
							<p><strong>- Andi</strong><br>Karyawan Swasta</p>
						</div>
					</div>
				</div>
				<a class="carousel-control-prev" href="#carouselContent" role="button" data-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="sr-only">Previous</span>
				</a>
				<a class="carousel-control-next" href="#carouselContent" role="button" data-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="sr-only">Next</span>
				</a>
			</div>
		</section>

		<section class="developer-section text-center" id="developer">
    <h2 class="section-title">Developers</h2>
    <div class="container">
        <div class="row slideanim">
		<div class="col-sm-6 col-md-4 col-lg-4">
                <div class="developer-card">
                    <img src="assets/img/naufal1.jpeg" alt="Team Member 2" class="developer-img">
                    <h5 class="developer-name">Naufal Fadhilah Ardhani</h5>
                    <p class="developer-role">Ketua</p>
                  
                    <div class="social-icons">
          
                        <a href="https://www.instagram.com/ardhaninaufal_?igsh=MWxmOHVsM3hzNDR3bA=="><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-4">
                <div class="developer-card">
                    <img src="assets/img/bulutfhiya.jpg" alt="Team Member 1" class="developer-img">
                    <h5 class="developer-name">Luthfiya Ratna Sari</h5>
                    <p class="developer-role">Pembimbing</p>
                    
                    <div class="social-icons">
                  
                        <a href="https://www.instagram.com/luthfifiya?igsh=ZjJwMmw3NDBnempz"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
         
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="developer-card">
                    <img src="assets/img/risi.jpg" alt="Team Member 3" class="developer-img">
                    <h5 class="developer-name">Risi Gustiar</h5>
                    <p class="developer-role">Anggota</p>
    
                    <div class="social-icons">
               
                        <a href="https://www.instagram.com/risigustiar28?igsh=M29jcDAxNmZjYzA0"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            </div>

			<div class="row slideanim mt-2">
            <div class="col-sm-6 col-md-4 col-lg-4">
                <div class="developer-card">
                    <img src="assets/img/rendi.jpg" alt="Team Member 4" class="developer-img">
                    <h5 class="developer-name">Rendi Sinaga</h5>
                    <p class="developer-role">Anggota</p>
                
                    <div class="social-icons">
                
                        <a href="https://www.instagram.com/rendi_snaga?igsh=MTE0dGY3NTkxcDJqOA=="><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-4">
                <div class="developer-card">
                    <img src="assets/img/agung.jpg" alt="Team Member 5" class="developer-img">
                    <h5 class="developer-name">Said Agung Dwiyanto</h5>
                    <p class="developer-role">Anggota</p>
                 
                    <div class="social-icons">
       
                        <a href="https://www.instagram.com/agung_1456?igsh=amQwN29taGN5bTFy"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4">
                <div class="developer-card">
                    <img src="assets/img/dzakwan.jpg" alt="Team Member 5" class="developer-img">
                    <h5 class="developer-name">Muhammad Dzakwan Naufal</h5>
                    <p class="developer-role">Anggota</p>
        
                    <div class="social-icons">
             
                        <a href="https://www.instagram.com/dzkwn_naufal?igsh=MWpzbWt1M3RzNGh6cw=="><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>


		<!-- <section id="patient">
			<div class="container">
				<div class="row slideanim">
					<div class="col-5 img-banner">
						<img src="./assets/img/app-banner.webp" alt="" width="380px">
					</div>
					<div class="col-7">
						<h1>Download the ClinicMe app</h1>
						<p>Book appointments and health checkups;<br>Order medicines and consult doctors online</p>
						<img src="./assets/img/google-play.png" alt="Play Button" width="180px">
					</div>
				</div>
			</div>
		</section> -->

	</div>

	<footer class="footer">
		<div class="upper-footer">
			<div class="container">
				<div class="row">
					<div class="col-12 text-center">
						<a class="navbar-brand mb-3" href="#">DokterOnTime</a><br>
						<a class="mr-3" href="https://www.youtube.com/channel/UCu75qh7vj6zP4WhOGN-KEqg"><i class="fab fa-youtube"></i></a>
						<a class="mr-3" href="https://www.instagram.com/dokter.on.time/"><i class="fab fa-instagram"></i></a> <!-- Instagram icon added here -->
						<div class="mt-3">
							<a href="mailto:pkmkc.dokter@gmail.com">
								<i class="fas fa-envelope mr-1" ></i> pkmkc.dokter@gmail.com
							</a>
						</div>

					</div>
					
				</div>
			</div>
		</div>
		<div class="bottom-footer mt-5">
			<div class="container">
				<div class="row">
					<div class="col-6">
						<p> 
								
						</p>
					</div>
					
					<div class="top-button"><a href="#top" class="top-scroll"><i class="fas fa-angle-up"></i></a></div>
				</div>
			</div>
		</div>
	</footer>


	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
		integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
		crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
		integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
		crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
		integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
		crossorigin="anonymous"></script>
	<script>
		$(window).scroll(function () {
			$(".slideanim").each(function () {
				var pos = $(this).offset().top;

				var winTop = $(window).scrollTop();
				if (pos < winTop + 600) {
					$(this).addClass("slide");
				}
			});
		});
	</script>
</body>

</html>