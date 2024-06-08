-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Jun 2024 pada 16.23
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinic_appointment`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(20) NOT NULL,
  `admin_name` varchar(50) NOT NULL,
  `admin_pass` varchar(255) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_token` varchar(255) NOT NULL,
  `admin_registered` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_pass`, `admin_email`, `admin_token`, `admin_registered`) VALUES
(1, 'Admin', '3cVaxePSLtU0JOrUNCgvbxFaSMgH9+lJSOZCR1DHfnSMUo4LNff3bMwLqCg5PS4j', 'admin@admin.com', '1488714734152752384749', '2020-01-15 03:28:10');

-- --------------------------------------------------------

--
-- Struktur dari tabel `announcement`
--

CREATE TABLE `announcement` (
  `ann_id` int(11) NOT NULL,
  `ann_title` varchar(255) NOT NULL,
  `ann_content` text NOT NULL,
  `date_created` datetime NOT NULL,
  `clinic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointment`
--

CREATE TABLE `appointment` (
  `app_id` int(11) NOT NULL,
  `app_date` date NOT NULL,
  `app_time` varchar(255) NOT NULL,
  `treatment_type` varchar(255) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `clinic_id` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '1: Confirm, 0: Not Confirm',
  `consult_status` int(11) NOT NULL COMMENT '1: Visited 0: None',
  `arrive_status` int(11) NOT NULL COMMENT '1: Arrived 0: None'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `appointment`
--

INSERT INTO `appointment` (`app_id`, `app_date`, `app_time`, `treatment_type`, `patient_id`, `doctor_id`, `clinic_id`, `status`, `consult_status`, `arrive_status`) VALUES
(1, '2024-06-05', 'undefined', 'New Patient', 1, 8, 2, 1, 0, 0),
(2, '2024-06-05', 'undefined', 'New Patient', 1, 8, 2, 1, 0, 0),
(3, '2024-06-05', 'undefined', 'New Patient', 1, 8, 2, 1, 0, 0),
(4, '2024-06-05', 'undefined', 'undefined', 1, 9, 4, 1, 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `business_hour`
--

CREATE TABLE `business_hour` (
  `businesshour_id` int(11) NOT NULL,
  `open_week` varchar(255) NOT NULL,
  `close_week` varchar(255) NOT NULL,
  `open_sat` varchar(255) NOT NULL,
  `close_sat` varchar(255) NOT NULL,
  `open_sun` varchar(255) NOT NULL,
  `close_sun` varchar(255) NOT NULL,
  `clinic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `business_hour`
--

INSERT INTO `business_hour` (`businesshour_id`, `open_week`, `close_week`, `open_sat`, `close_sat`, `open_sun`, `close_sun`, `clinic_id`) VALUES
(1, '8:37 AM', '8:37 PM', '8:37 AM', '8:37 PM', '8:37 AM', '8:37 PM', 1),
(2, '8:31 PM', '8:31 PM', '8:31 PM', '8:31 PM', '8:31 PM', '8:31 PM', 2),
(3, '', '', '', '', '', '', 3),
(4, '4:44 AM', '4:44 AM', '4:44 AM', '4:44 PM', '4:44 AM', '4:44 PM', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `clinics`
--

CREATE TABLE `clinics` (
  `clinic_id` int(11) NOT NULL,
  `clinic_name` varchar(255) NOT NULL,
  `clinic_email` varchar(255) NOT NULL,
  `clinic_url` varchar(255) NOT NULL,
  `clinic_contact` varchar(15) NOT NULL,
  `clinic_address` varchar(255) NOT NULL,
  `clinic_city` varchar(255) NOT NULL,
  `clinic_state` varchar(255) NOT NULL,
  `clinic_zipcode` varchar(10) NOT NULL,
  `clinic_status` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `clinics`
--

INSERT INTO `clinics` (`clinic_id`, `clinic_name`, `clinic_email`, `clinic_url`, `clinic_contact`, `clinic_address`, `clinic_city`, `clinic_state`, `clinic_zipcode`, `clinic_status`, `date_created`) VALUES
(1, 'Rumah Sakit Jiwa', 'clinic@gmail.com', 'youtube.com', '0123456', '1234 main', 'batam', 'Terengganu', '123', '1', '2024-05-26 21:37:20'),
(2, 'baka', 'asdfjlsajf@gmail.com', 'youtube.com', '0123456', 'kjhk', 'batam', 'Terengganu', '123', '1', '2024-05-28 21:31:07'),
(3, 'Rumah Sakit Orang Tergila', '', '', '', '', '', '', '', '', '2024-06-05 12:17:45'),
(4, 'Save Walter White', 'walter@example.com', 'https://www.youtube.com/', '12345678', 'batam', 'batam', 'Kelantan', '122', '1', '2024-06-05 17:39:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `clinic_images`
--

CREATE TABLE `clinic_images` (
  `clinicimg_id` int(11) NOT NULL,
  `clinicimg_filename` varchar(255) NOT NULL,
  `clinic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `clinic_manager`
--

CREATE TABLE `clinic_manager` (
  `clinicadmin_id` int(11) NOT NULL,
  `clinicadmin_name` varchar(255) NOT NULL,
  `clinicadmin_password` varchar(255) NOT NULL,
  `clinicadmin_token` varchar(255) NOT NULL,
  `clinicadmin_email` varchar(255) NOT NULL,
  `clinicadmin_contact` varchar(15) NOT NULL,
  `date_created` datetime NOT NULL,
  `clinic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `clinic_manager`
--

INSERT INTO `clinic_manager` (`clinicadmin_id`, `clinicadmin_name`, `clinicadmin_password`, `clinicadmin_token`, `clinicadmin_email`, `clinicadmin_contact`, `date_created`, `clinic_id`) VALUES
(1, 'Heisenberg', 'IbnViHW7CEsh/30pKGFJ2WUPgH7uwllJSLN8kuro2hjo9aVwTZ41HmUW5B7om/wm', '9343738118311163275674', 'clinic@gmail.com', '0123456', '2024-05-26 21:37:20', 1),
(2, 'john pork', 'wuob499cTcHW/DB8D11W/+QTmSYiSo1OXHjQO9SFE9nNtYWTz4mnMXBV6UJxcax7', '1308887125328587382809', 'john@gmail.com', '11111', '2024-05-28 21:31:07', 2),
(3, 'Rusa', 'kaKCISULnMboCTfBxANdcqNNln6WxpLovd0U2y+TwOu2nur7KKvn5dZS27UYgM0+', '9378912930644959485590', 'clinic1@example.com', '012112121122', '2024-06-05 12:17:45', 3),
(4, 'walter white', 'm/oi+uelsRND+ZaXfgz8spiCMfr2RwAKFXUjkvk88eLWdJYcGA3tuGj5REazKawM', '6943476185148610072154', 'walter@example.com', '012112121122', '2024-06-05 17:39:15', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `clinic_reset`
--

CREATE TABLE `clinic_reset` (
  `reset_id` int(11) NOT NULL,
  `reset_email` varchar(255) NOT NULL,
  `reset_selector` text NOT NULL,
  `reset_token` longtext NOT NULL,
  `reset_expires` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `doctor_avatar` varchar(255) NOT NULL,
  `doctor_firstname` varchar(255) NOT NULL,
  `doctor_lastname` varchar(255) NOT NULL,
  `doctor_speciality` varchar(255) NOT NULL,
  `doctor_experience` varchar(10) NOT NULL,
  `doctor_desc` text NOT NULL,
  `doctor_password` varchar(255) NOT NULL,
  `doctor_token` varchar(255) NOT NULL,
  `doctor_spoke` varchar(255) NOT NULL,
  `doctor_gender` varchar(10) NOT NULL,
  `doctor_dob` date NOT NULL,
  `doctor_email` varchar(255) NOT NULL,
  `doctor_contact` varchar(15) NOT NULL,
  `consult_fee` int(11) NOT NULL,
  `date_created` datetime NOT NULL,
  `clinic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `doctor_avatar`, `doctor_firstname`, `doctor_lastname`, `doctor_speciality`, `doctor_experience`, `doctor_desc`, `doctor_password`, `doctor_token`, `doctor_spoke`, `doctor_gender`, `doctor_dob`, `doctor_email`, `doctor_contact`, `consult_fee`, `date_created`, `clinic_id`) VALUES
(1, 'Heisenberg.jpg', 'Jessie', 'Pinkman', '1', '30', 'asdfas', '', '', 'Malay', 'male', '2024-05-26', 'aslasjdaskd@gmail.com', '', 9000000, '2024-05-26 22:08:24', 1),
(6, '', '', '', '', '', '', '$2y$10$A.vkGRjAWoGrdByjSxCaUeWShVTfPeugMkK61LuUeB9ZOoPTQDbIm', '', '', '', '0000-00-00', 'naufal@example.com', '', 0, '0000-00-00 00:00:00', 3),
(8, 'jessie.jpg', 'Walter', 'White', '5', '12', 'error mulu totlol', '$2y$10$R4d/1m.0HAbWsUzaC4mWZeZR.0czH0acnORmPK8vkRUIdE0Wpp27u', '', 'English,Tamil', 'male', '2024-06-05', 'heisenberg@example.com', '12112121122', 1212, '0000-00-00 00:00:00', 2),
(9, 'Heisenberg.jpg', 'Walter', 'White', '5', '12', 'this is motherfucker', '$2y$10$B7CxsPdwurvHEQmNupIkveKuiWPHHx/j/CsZ6.aPKx6A0P0vfdOKW', '', 'Malay', 'male', '2024-06-05', 'walter@example.com', '12112121122', 1212, '0000-00-00 00:00:00', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctor_reset`
--

CREATE TABLE `doctor_reset` (
  `reset_id` int(11) NOT NULL,
  `reset_email` varchar(255) NOT NULL,
  `reset_selector` text NOT NULL,
  `reset_token` longtext NOT NULL,
  `reset_expires` text NOT NULL,
  `activate_token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `doctor_reset`
--

INSERT INTO `doctor_reset` (`reset_id`, `reset_email`, `reset_selector`, `reset_token`, `reset_expires`, `activate_token`) VALUES
(1, 'aslasjdaskd@gmail.com', 'c073aebad45b7ef0', '$2y$10$xNd4v6TR1npMWSxlAiVZl.MRL..xJ.NDZT90UFMqCmccGrtMEWNli', '1716818904', 'c33ba283f54a267b479325c67e2ea466'),
(4, 'fake@example.com', '58732201a5271d35', '$2y$10$BJsIRTSRRRYcj04voRYwtulFD6Q1jHtrsYec7OEXd14F4LOo8.Mia', '1717563608', ''),
(8, 'dapeqa.equled@rungel.net', '18b1ea3faa4b767d', '$2y$10$P7kXcQmM7GfY4M.GyItoeuBznDmNfel0HIYJeBkHocjHHY91W7Gfy', '1717563970', ''),
(9, 'jesse@example.com', '3dc73637da97a67a', '$2y$10$.FRM58LcovReXElGvCxay.J6GUs9lXGzrWvVnO0ghjy0REPbg5PzO', '1717651930', 'e69b19f155159cfd3ce84b2294c935ba'),
(10, 'heisenberg@example.com', 'ec09ec44fbc6e8b4', '$2y$10$iU8.RJeYZgjYUnpc6OdpcOr3YioTYnyvT3eAFafzcIBiE0/V8yHbO', '1717652766', 'a575e2213fbf686bda7f35b211381b24'),
(11, 'walter@example.com', '6801ec24b821978a', '$2y$10$PDaRnnmpV9VQrdkKb5FEj.3dqgkscRpPDa2oXbI4a2mKyhKgdicEu', '1717667289', 'd958c4448d89750f39a62b5d20f45fe8');

-- --------------------------------------------------------

--
-- Struktur dari tabel `medical_record`
--

CREATE TABLE `medical_record` (
  `med_id` int(11) NOT NULL,
  `med_sympton` text NOT NULL,
  `med_diagnosis` text NOT NULL,
  `med_date` datetime NOT NULL,
  `med_advice` text NOT NULL,
  `patient_id` int(11) NOT NULL,
  `clinic_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `patient_avatar` varchar(255) NOT NULL,
  `patient_firstname` varchar(255) NOT NULL,
  `patient_lastname` varchar(255) NOT NULL,
  `patient_email` varchar(255) NOT NULL,
  `patient_password` varchar(255) NOT NULL,
  `patient_token` varchar(255) NOT NULL,
  `patient_identity` varchar(255) NOT NULL,
  `patient_nationality` varchar(255) NOT NULL,
  `patient_gender` varchar(255) NOT NULL,
  `patient_maritalstatus` varchar(255) NOT NULL,
  `patient_dob` date NOT NULL,
  `patient_age` varchar(11) NOT NULL,
  `patient_contact` varchar(255) NOT NULL,
  `patient_address` varchar(255) NOT NULL,
  `patient_city` varchar(255) NOT NULL,
  `patient_state` varchar(255) NOT NULL,
  `patient_zipcode` varchar(255) NOT NULL,
  `patient_country` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `patients`
--

INSERT INTO `patients` (`patient_id`, `patient_avatar`, `patient_firstname`, `patient_lastname`, `patient_email`, `patient_password`, `patient_token`, `patient_identity`, `patient_nationality`, `patient_gender`, `patient_maritalstatus`, `patient_dob`, `patient_age`, `patient_contact`, `patient_address`, `patient_city`, `patient_state`, `patient_zipcode`, `patient_country`, `date_created`) VALUES
(1, '', 'naufal', 'naufal', 'naufal@example.com', 'r1TkEQQhQY+1ZowolwnqprouwwzZl3Qlt9+ld5Fi9IxXTU0t4FbuP99PI6AB7c+2', '3302768451825579238145', '1234567890', '', '', '', '0000-00-00', '', '', '', '', '', '', '', '2024-06-04 17:25:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `patient_reset`
--

CREATE TABLE `patient_reset` (
  `reset_id` int(11) NOT NULL,
  `reset_email` varchar(255) NOT NULL,
  `reset_selector` text NOT NULL,
  `reset_token` longtext NOT NULL,
  `reset_expires` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `resep`
--

CREATE TABLE `resep` (
  `id_resep` int(11) NOT NULL,
  `nama_obat` varchar(200) NOT NULL,
  `dosis` varchar(100) NOT NULL,
  `frekuensi_minum` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `resep`
--

INSERT INTO `resep` (`id_resep`, `nama_obat`, `dosis`, `frekuensi_minum`) VALUES
(1, 'Heroin', 'sampe mati overdosis', 'sampe mati overdosis'),
(2, 'Meth', 'sampe overdosis', 'sampe overdosis'),
(3, 'Ganja', 'sampe overdosis', 'sampe overdosis');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `date` date NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedule`
--

CREATE TABLE `schedule` (
  `schedule_id` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `schedule_week` varchar(255) NOT NULL,
  `status` int(5) NOT NULL COMMENT '1=Active | 0= Inactive',
  `doctor_id` int(11) NOT NULL,
  `clinic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `schedule`
--

INSERT INTO `schedule` (`schedule_id`, `date_from`, `date_to`, `schedule_week`, `status`, `doctor_id`, `clinic_id`) VALUES
(1, '2024-06-05', '2024-06-15', 'Tuesday', 1, 7, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `schedule_detail`
--

CREATE TABLE `schedule_detail` (
  `schdetail_id` int(11) NOT NULL,
  `time_slot` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '1= Active 0 = Inactive',
  `schedule_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `schedule_detail`
--

INSERT INTO `schedule_detail` (`schdetail_id`, `time_slot`, `duration`, `status`, `schedule_id`) VALUES
(1, '12:38 PM', 45, 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `speciality`
--

CREATE TABLE `speciality` (
  `speciality_id` int(11) NOT NULL,
  `speciality_name` varchar(255) NOT NULL,
  `speciality_icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `speciality`
--

INSERT INTO `speciality` (`speciality_id`, `speciality_name`, `speciality_icon`) VALUES
(1, 'GP/Family', 'family.png'),
(2, 'Dentist', 'dentist.png'),
(3, 'Acupuncturist', 'acupuncture.png'),
(4, 'Audiologist', 'hearing.png'),
(5, 'Anaesthetist', 'anaesthetist.png'),
(6, 'Optometrist', 'optometrist.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `treatment_type`
--

CREATE TABLE `treatment_type` (
  `treatment_id` int(11) NOT NULL,
  `treatment_name` varchar(255) NOT NULL,
  `doctor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `treatment_type`
--

INSERT INTO `treatment_type` (`treatment_id`, `treatment_name`, `doctor_id`) VALUES
(1, 'New Patient', 1),
(2, 'New Patient', 2),
(3, 'New Patient', 3),
(4, 'New Patient', 4),
(5, 'New Patient', 7),
(6, 'New Patient', 8),
(7, 'New Patient', 9),
(8, 'Motherfucker', 9);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indeks untuk tabel `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`ann_id`);

--
-- Indeks untuk tabel `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`app_id`);

--
-- Indeks untuk tabel `business_hour`
--
ALTER TABLE `business_hour`
  ADD PRIMARY KEY (`businesshour_id`);

--
-- Indeks untuk tabel `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`clinic_id`);

--
-- Indeks untuk tabel `clinic_images`
--
ALTER TABLE `clinic_images`
  ADD PRIMARY KEY (`clinicimg_id`);

--
-- Indeks untuk tabel `clinic_manager`
--
ALTER TABLE `clinic_manager`
  ADD PRIMARY KEY (`clinicadmin_id`);

--
-- Indeks untuk tabel `clinic_reset`
--
ALTER TABLE `clinic_reset`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indeks untuk tabel `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD KEY `clinic_id` (`clinic_id`);

--
-- Indeks untuk tabel `doctor_reset`
--
ALTER TABLE `doctor_reset`
  ADD PRIMARY KEY (`reset_id`);

--
-- Indeks untuk tabel `medical_record`
--
ALTER TABLE `medical_record`
  ADD PRIMARY KEY (`med_id`);

--
-- Indeks untuk tabel `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indeks untuk tabel `resep`
--
ALTER TABLE `resep`
  ADD PRIMARY KEY (`id_resep`);

--
-- Indeks untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indeks untuk tabel `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indeks untuk tabel `schedule_detail`
--
ALTER TABLE `schedule_detail`
  ADD PRIMARY KEY (`schdetail_id`);

--
-- Indeks untuk tabel `speciality`
--
ALTER TABLE `speciality`
  ADD PRIMARY KEY (`speciality_id`);

--
-- Indeks untuk tabel `treatment_type`
--
ALTER TABLE `treatment_type`
  ADD PRIMARY KEY (`treatment_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `announcement`
--
ALTER TABLE `announcement`
  MODIFY `ann_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `appointment`
--
ALTER TABLE `appointment`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `business_hour`
--
ALTER TABLE `business_hour`
  MODIFY `businesshour_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `clinics`
--
ALTER TABLE `clinics`
  MODIFY `clinic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `clinic_images`
--
ALTER TABLE `clinic_images`
  MODIFY `clinicimg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `clinic_manager`
--
ALTER TABLE `clinic_manager`
  MODIFY `clinicadmin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `doctor_reset`
--
ALTER TABLE `doctor_reset`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `medical_record`
--
ALTER TABLE `medical_record`
  MODIFY `med_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `resep`
--
ALTER TABLE `resep`
  MODIFY `id_resep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `schedule`
--
ALTER TABLE `schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `schedule_detail`
--
ALTER TABLE `schedule_detail`
  MODIFY `schdetail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `speciality`
--
ALTER TABLE `speciality`
  MODIFY `speciality_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `treatment_type`
--
ALTER TABLE `treatment_type`
  MODIFY `treatment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`clinic_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
