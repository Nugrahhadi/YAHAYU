-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Des 2024 pada 19.34
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wisata_brazil`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun`
--

CREATE TABLE `akun` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nomor_telepon` varchar(15) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `role` enum('admin','peserta') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_profil` varchar(255) DEFAULT 'images/default-avatar.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `akun`
--

INSERT INTO `akun` (`id`, `username`, `nama`, `email`, `password`, `nomor_telepon`, `tanggal_lahir`, `role`, `created_at`, `foto_profil`) VALUES
(1, 'admin', 'Admin Utama', 'admin@brazil.com', '$2y$10$vrqsVdRsILiMcnXVO48pLeCXzERm2KtR6DeJepZp/hpa8GTJsIWr2', '081256786540', '2002-09-03', 'admin', '2024-11-26 03:30:45', 'images/default-avatar.jpg'),
(2, 'Hadi', 'nugrahhadi', 'nugrahhadi11@gmail.com', '$2y$10$xCkgPZ.WVszaAoUM8/a0L..B/MvCVCZhlIG0.6R.Ete17zkzLsY4y', '089567876678', '2004-06-15', 'peserta', '2024-12-02 12:17:17', 'images/default-avatar.jpg'),
(10, 'ayu', 'ayu', 'ayu123@gmail.com', '$2y$10$YanSffkAd6z1HEnXxC.pjelUOqteQexZAZ23a6MhDtHwvljWphrgS', '08907898765', '2004-02-03', 'peserta', '2024-12-09 11:55:56', 'images/default-avatar.jpg'),
(11, 'jery', 'Jery Jeremy', 'jery@gmail.com', '$2y$10$jRmNEc79QHhew/rv/qNpWuQMul2gwAw4O8M24T3exGkz35pMvcMDK', '0898789657623', '2003-11-12', 'peserta', '2024-12-13 15:22:40', 'images/default-avatar.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `destinasi_id` int(11) NOT NULL,
  `jumlah_tiket` int(11) NOT NULL,
  `tanggal_kunjungan` date NOT NULL,
  `total_pembayaran` decimal(10,2) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `payment_status` varchar(50) DEFAULT 'pending',
  `payment_token` varchar(100) DEFAULT NULL,
  `payment_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `destinasi_id`, `jumlah_tiket`, `tanggal_kunjungan`, `total_pembayaran`, `metode_pembayaran`, `nama_lengkap`, `email`, `status`, `created_at`, `payment_status`, `payment_token`, `payment_time`) VALUES
(18, 2, 7, 2, '2024-12-13', 1895388.00, 'transfer_bank', 'nugrahhadi', 'nugrahhadi11@gmail.com', 'confirmed', '2024-12-12 19:02:39', 'paid', 'PAY-1734004965-18', '2024-12-12 19:02:45'),
(19, 1, 10, 2, '2024-12-13', 952992.00, 'transfer_bank', 'Admin Utama', 'admin@brazil.com', 'confirmed', '2024-12-12 20:39:26', 'paid', 'PAY-1734010804-19', '2024-12-12 20:40:04'),
(20, 1, 7, 1, '2024-12-13', 947694.00, '', 'Admin Utama', 'admin@brazil.com', 'pending', '2024-12-12 23:36:08', 'pending', NULL, NULL),
(21, 1, 7, 1, '2024-12-14', 947694.00, '', 'Admin Utama', 'admin@brazil.com', 'confirmed', '2024-12-13 19:19:03', 'paid', 'PAY-1734092382-21', '2024-12-13 19:19:42'),
(22, 2, 11, 1, '2024-12-15', 582600.00, '', 'nugrahhadi', 'nugrahhadi11@gmail.com', 'confirmed', '2024-12-14 16:38:02', 'paid', 'PAY-1734169091-22', '2024-12-14 16:38:11'),
(23, 2, 7, 1, '2024-12-26', 947694.00, '', 'nugrahhadi', 'nugrahhadi11@gmail.com', 'confirmed', '2024-12-14 20:17:48', 'paid', 'PAY-1734182274-23', '2024-12-14 20:17:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `destinasi`
--

CREATE TABLE `destinasi` (
  `id` int(11) NOT NULL,
  `nama_destinasi` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `destinasi`
--

INSERT INTO `destinasi` (`id`, `nama_destinasi`, `deskripsi`, `harga`, `kategori`, `gambar`) VALUES
(7, 'Praia do Sancho, Fernando de Noronha', 'Praia do Sancho adalah pantai kelas dunia yang terkenal dengan air jernih berwarna emerald, pasir keemasan, dan tebing tinggi. Pantai ini berada di kawasan taman laut nasional dan hanya bisa diakses melalui tangga curam di antara tebing atau menggunakan perahu. Lingkungannya yang terlindungi membuatnya menjadi surga bagi pencinta alam dan fotografer. Kegiatan yang dapat dilakukan yaitu ada Snorkeling dan Menyelam. Tempat ini sangat cocok untuk bersantai.', 947694.00, 'pantai', 'uploads/675a5536421e4.png'),
(10, 'Praia de Pipa, Rio Grande do Norte', 'Pantai ini dikelilingi oleh tebing megah dan terkenal dengan lumba-lumba yang sering muncul di perairannya. Praia de Pipa juga menjadi pusat kehidupan malam dengan restoran dan bar yang ramai. Kegiatan yang dapat dilakukan yaitu ada berselancar, tur perahu mengamati lumba-lumba, dan paddleboarding. Alamat tepatnya yaitu ada di Praia de Pipa, Tibau do Sul, Rio Grande do Norte.', 476496.00, 'pantai', 'uploads/675ae73d63408.png'),
(11, 'Lençóis Maranhenses, Maranhão', 'Lençóis Maranhenses adalah taman nasional dengan pemandangan luar biasa berupa hamparan pasir putih yang membentuk laguna-laguna biru dan hijau selama musim hujan. Tempat ini sering disebut sebagai salah satu keajaiban alam Brasil. Kegiatan yang dapat dilakukan disana yaitu antara lain Jelajah Dune Buggy Off-road, Berenang di Laguna, dan Trekking. Alamat tepat dari destinasi ini yaitu berada di Taman Nasional Lençóis Maranhenses, Maranhão.', 582600.00, 'gurun', 'uploads/675c671538040.png'),
(12, 'Ouro Preto, Minas Gerais', 'Ouro Preto adalah kota bersejarah yang terkenal dengan arsitektur baroknya, jalanan berbatu, dan peninggalan tambang emas. Dikelilingi oleh pegunungan dan bangunan bersejarah, kota ini adalah situs Warisan Dunia UNESCO yang menggambarkan kejayaan Brasil kolonial. Kegiatan yang dapat dilakukan disana yaitu ada Menjelajahi gereja bersejarah, Mengunjungi museum seperti Inconfidência, dan Berjalan di pusat kota. Alamat tepatnya ada di Praça Tiradentes, Ouro Preto - MG, Brasil.', 90000.00, 'Cultural Sites', 'uploads/675c69057ca7a.png'),
(13, 'Cachoeira do Tabuleiro, Minas Gerais', 'Cachoeira do Tabuleiro adalah air terjun tertinggi di Minas Gerais, dengan ketinggian sekitar 273 meter. Terletak di Taman Nasional Serra do Cipó, air terjun ini menawarkan pemandangan yang menakjubkan serta lingkungan alami yang indah. Alamat tepatnya ada di Taman Nasional Serra do Cipó, Minas Gerais, Brasil.', 132000.00, 'air terjun', 'uploads/675c6a0ce9f8e.jpg'),
(14, 'Pico das Agulhas Negras, Rio de Janeiro', 'Puncak gunung ini adalah yang tertinggi di Negara Bagian Rio de Janeiro, menawarkan panorama alam yang spektakuler. Pendakian ke puncak menyediakan pemandangan menakjubkan dari pegunungan dan lembah sekitarnya. Kegiatan yang dapat dilakukan yaitu antara lain Mendaki, Fotografi alam, dan Menikmati pemandangan dari puncak.', 120000.00, 'pegunungan', 'uploads/675c6ad174f87.png'),
(15, 'Praia dos Carneiros, Pernambuco', 'Pantai ini menawarkan suasana tropis dengan pasir putih, pohon kelapa berjajar, dan air laut yang tenang. Ikon utama adalah Kapel São Benedito yang berada tepat di tepi pantai, menjadikannya lokasi favorit untuk wisata budaya dan fotografi. Kegiatan yang dapat dilakukan ketika sedang berada disana yaitu Jelajah Hutan Bakau, Berenang, dan Mengunjungi Kapel São Benedito.', 661800.00, 'pantai', 'uploads/675dc154a6339.png'),
(16, 'Praia do Espelho, Bahia', 'Terkenal dengan keindahannya yang alami, pantai ini menawarkan air biru kehijauan yang tenang dan pasir putih. Saat air surut, pantai ini berubah menjadi laguna alami yang indah. Kegiatan yang dapat dilakukan ketika sedang berada disana yaitu Piknik, Fotografi, dan Menikmati Kuliner Lokal. Alamat tepatnya berada di Transoco, Bahia, Brasil.', 317644.00, 'pantai', 'uploads/675dc19296663.png'),
(17, 'Ilha do Mel, Paraná', 'Ilha do Mel adalah pulau yang menawarkan pantai terpencil, gua laut, dan mercusuar bersejarah. Tanpa kendaraan bermotor, pulau ini menjadi tempat yang sempurna untuk menikmati suasana damai dan hiking. kegiatan yang dapat dilakukan disana yaitu bisa Hiking ke Gua Laut atau Mercusuar Farol das Conchas, Surfing, dan Eksplorasi Flora & Fauna. Alamat tepatnya di Ilha do Mel, Paranaguá, Parana.', 529440.00, 'pantai', 'uploads/675dc1d02f9b9.png'),
(18, 'Jalapão, Tocantins', 'Jalapão adalah kawasan ekowisata yang terletak di negara bagian Tocantins, Brasil. Dikenal sebagai \"Tanah Emas,\" Jalapão menawarkan pemandangan alam yang menakjubkan dengan bukit pasir, air terjun, dan danau yang jernih. Area ini juga memiliki keanekaragaman flora dan fauna yang luar biasa, menjadikannya destinasi populer bagi para pecinta alam dan petualangan.', 264720.00, 'gurun', 'uploads/675dc241c5c5f.png'),
(19, 'Areias Brancas, Maranhão', 'Destinasi ini dikenal karena pasir putihnya yang murni dan suasana yang tenang. Terletak tidak jauh dari Lençóis Maranhenses, Areias Brancas menawarkan pengalaman relaksasi di tengah keindahan alam. Kegiatan yang dapat dilakukan disana yaitu antara lain Piknik, Jelajah Pasir, dan Berenang. Alamat tepat dari destinasi ini yaitu berada di Barreirinhas, Maranhão.', 264720.00, 'gurun', 'uploads/675dc28f3cb26.png'),
(20, 'Morro Branco, Ceará', 'Morro Branco adalah pantai yang terkenal di Ceará, Brasil, dengan tebing-tebing pasir berwarna-warni yang menciptakan pemandangan yang menakjubkan. Dikenal karena formasi alamnya yang unik, tempat ini merupakan destinasi populer bagi para pengunjung yang ingin menikmati keindahan alam tropis.', 132000.00, 'gurun', 'uploads/675dc2e772a8c.png'),
(21, 'Dunas do Peró, Rio de Janeiro', 'Dunas do Peró adalah kawasan pantai dengan bukit pasir yang luas di Rio de Janeiro, Brasil. Terkenal dengan keindahan alamnya yang memukau, tempat ini ideal bagi mereka yang ingin menikmati suasana pantai yang tenang sambil menikmati pemandangan pasir putih dan laut biru yang luas.', 397000.00, 'gurun', 'uploads/675dc32c07e52.png'),
(22, 'Véu da Noiva, Mato Grosso', 'Véu da Noiva adalah air terjun spektakuler yang terletak di Chapada dos Guimarães, Mato Grosso. Air terjun ini terkenal dengan keindahan aliran air yang jatuh seperti tirai, menciptakan pemandangan yang memukau. Kegiatan yang dapat dilakukan disana yaitu antara lainnya bisa Berjalan, Fotografi air terjun dan lanskap, dan Menikmati pemandangan dari lookout point.', 70000.00, 'air terjun', 'uploads/675dc3735ff58.jpg'),
(23, 'Rio da Prata, Bonito', 'Rio da Prata di Bonito, Mato Grosso do Sul, terkenal dengan keindahan perairan yang sangat jernih dan kehidupan bawah air yang kaya. Sungai ini ideal untuk kegiatan snorkeling dan menyelam. Kegiatan yang dapat dilakukan disana yaitu antara lainnya bisa Snorkeling, Menyelam, dan Observasi alam.', 792000.00, 'air terjun', 'uploads/675dc3d4d42fb.jpg'),
(24, 'Cachoeira do Sossego, Bahia', 'Cachoeira do Sossego adalah air terjun di daerah Chapada Diamantina, Bahia, terkenal dengan keindahan alamnya dan suasana yang menenangkan. Tempat ini ideal bagi pencinta alam yang mencari kedamaian. Alamat tepatnya ada di Chapada Diamantina, Bahia, Brasil.', 1400000.00, 'air terjun', 'uploads/675dc40fcd667.jpg'),
(25, 'Rio Sucuri, Mato Grosso do Sul', 'Rio Sucuri adalah sungai yang terletak di Bonito, Mato Grosso do Sul, terkenal dengan airnya yang sangat jernih, ideal untuk snorkeling dan menyelam. Sungai ini menawarkan pengalaman menyelam yang mempesona dengan pemandangan bawah air yang luar biasa. Kegiatan yang dapat dilakukan disana yaitu antara lainnya bisa Snorkeling, Menyelam, dan Mengamati fauna.', 792000.00, 'air terjun', 'uploads/675dc44bc9874.jpg'),
(26, 'Vila de Caraíva, Bahia', 'Terletak di pesisir Bahia, Caraíva adalah desa kecil dengan suasana santai dan keindahan alam yang mempesona. Dengan pantai berpasir putih, air laut biru jernih, dan suasana yang bebas dari keramaian, Caraíva menjadi destinasi ideal untuk pelancong yang mencari ketenangan. Kegiatan yang dapat dilaksanakan disana yaitu Berselancar, Menikmati hidangan lokal, dan Menyaksikan matahari terbenam. Alamat tepatnya yaitu berada di Caraíva, Porto Seguro - BA, Brasil.', 120000.00, 'Cultural Sites', 'uploads/675dc49f10a6a.png'),
(27, 'São Francisco do Sul, Santa Catarina', 'São Francisco do Sul adalah kota bersejarah dengan nuansa kolonial yang kental, terkenal dengan pelabuhannya yang sibuk dan pelabuhan ikan yang ramai. Tempat ini memiliki bangunan bersejarah yang terawat dengan baik dan pesona pantai yang menenangkan. Kegiatan yang dapat dilakukan disana yaitu ada Mengunjungi Museum Nasional Marine, Berjalan di pelabuhan, dan Bersantai. Alamat tepatnya ada di Centro, São Francisco do Sul - SC, Brasil.', 120000.00, 'Cultural Sites', 'uploads/675dc4e3da805.png'),
(28, 'Paraty, Rio de Janeiro', 'Paraty adalah kota pelabuhan yang terletak di pesisir timur Brasil, dikenal dengan jalan-jalan berbatu dan rumah-rumah berwarna cerah. Paraty memiliki kekayaan sejarah dan budaya, serta merupakan tempat populer untuk festival seni dan musik. Kegiatan yang dapat dilakukan disana yaitu ada Jelajah kota tua, Berlayar, dan Menghadiri festival seni dan budaya tahunan. Alamat tepatnya ada di Rua do Comércio, Paraty - RJ, Brasil.', 150000.00, 'Cultural Sites', 'uploads/675dc51ba9448.png'),
(29, 'Pirenópolis, Goiás', 'Pirenópolis adalah kota yang dikelilingi oleh alam liar, terkenal dengan air terjun dan jalur pendakian yang menawan. Kota ini juga memiliki pasar lokal yang ramai, di mana pengunjung dapat menemukan kerajinan tangan dan produk lokal. Kegiatan yang dapat dilakukan disana yaitu ada Trekking menuju Cachoeira do Abade, Bersepeda, dan Jelajah pasar lokal. Alamat tepatnya ada di Rua do Rosário, Pirenópolis - GO, Brasil.', 90000.00, 'Cultural Sites', 'uploads/675dc55b3a502.png'),
(30, 'Serra da Capivara, Piauí', 'Taman Nasional Serra da Capivara di Piauí adalah situs arkeologi yang luar biasa dengan gua-gua yang dihiasi lukisan prasejarah, bukti kehidupan manusia purba yang mengesankan. Pegunungan ini menyajikan pemandangan alam yang dramatis dengan formasi batuan yang menakjubkan.', 180000.00, 'pegunungan', 'uploads/675dc6b71624e.png'),
(31, 'Serra do Caparaó, Espírito Santo', 'Pegunungan ini terletak di perbatasan antara Espírito Santo dan Minas Gerais. Dikenal dengan keindahan alam yang menakjubkan, termasuk flora endemik dan jalur pendakian yang memukau. Kegiatan yang dapat dilakukan yaitu antara lain Mendaki, Berkemah, dan Mengamati flora fauna.', 150000.00, 'pegunungan', 'uploads/675dc73dd80a4.png'),
(32, 'Chapada dos Guimarães, Mato Grosso', 'Destinasi alam ini menawarkan pemandangan dramatis berupa tebing tinggi, air terjun yang indah, dan gua-gua eksotis. Chapada dos Guimarães adalah tempat ideal bagi para pencinta alam. Kegiatan yang dapat dilakukan yaitu antara lain Trekking, Berenang di air terjun, dan Berkunjung ke gua.', 90000.00, 'pegunungan', 'uploads/675dc775834b6.png'),
(33, 'Pico Paraná, Paraná', 'Gunung ini dikenal sebagai puncak tertinggi di Negara Bagian Paraná dan menjadi tujuan populer bagi para pendaki yang ingin menjelajahi alam liar Brasil. Pemandangan dari puncak menakjubkan dengan panorama pegunungan dan lembah yang luas.', 120000.00, 'pegunungan', 'uploads/675dc7c802207.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `destinasi_gambar`
--

CREATE TABLE `destinasi_gambar` (
  `id` int(11) NOT NULL,
  `destinasi_id` int(11) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `destinasi_gambar`
--

INSERT INTO `destinasi_gambar` (`id`, `destinasi_id`, `gambar`) VALUES
(5, 7, 'uploads/675a553643c0a.png'),
(6, 7, 'uploads/675a55364519d.png'),
(11, 10, 'uploads/675ae73d6452c.png'),
(12, 10, 'uploads/675ae73d66b69.png'),
(13, 11, 'uploads/675c67153945e.png'),
(14, 11, 'uploads/675c67153a4c6.png'),
(15, 12, 'uploads/675c69057d7b7.png'),
(16, 12, 'uploads/675c69057e334.png'),
(17, 13, 'uploads/675c6a0ceb361.jpg'),
(18, 13, 'uploads/675c6a0cebc90.jpg'),
(19, 14, 'uploads/675c6ad175999.png'),
(20, 14, 'uploads/675c6ad1763c0.png'),
(21, 15, 'uploads/675dc154a980d.png'),
(22, 15, 'uploads/675dc154aa180.png'),
(23, 16, 'uploads/675dc1929a8a6.png'),
(24, 16, 'uploads/675dc1929eab5.jpg'),
(25, 17, 'uploads/675dc1d030468.png'),
(26, 17, 'uploads/675dc1d030bbc.png'),
(27, 18, 'uploads/675dc241c66de.png'),
(28, 18, 'uploads/675dc241c6da1.png'),
(29, 19, 'uploads/675dc28f3d45b.png'),
(30, 19, 'uploads/675dc28f3e0e2.png'),
(31, 20, 'uploads/675dc2e7736e0.png'),
(32, 20, 'uploads/675dc2e773df5.png'),
(33, 21, 'uploads/675dc32c0960e.png'),
(34, 21, 'uploads/675dc32c0a097.png'),
(35, 22, 'uploads/675dc3736091f.jpg'),
(36, 22, 'uploads/675dc37361498.jpg'),
(37, 23, 'uploads/675dc3d4d4c9f.jpg'),
(38, 23, 'uploads/675dc3d4d549c.jpg'),
(39, 24, 'uploads/675dc40fcde33.jpg'),
(40, 24, 'uploads/675dc40fceb80.jpg'),
(41, 25, 'uploads/675dc44bca207.jpg'),
(42, 25, 'uploads/675dc44bcab52.png'),
(43, 26, 'uploads/675dc49f11535.png'),
(44, 26, 'uploads/675dc49f11d2b.png'),
(45, 27, 'uploads/675dc4e3db046.png'),
(46, 27, 'uploads/675dc4e3dbc50.png'),
(47, 28, 'uploads/675dc51ba9d36.png'),
(48, 28, 'uploads/675dc51baa56a.png'),
(49, 29, 'uploads/675dc55b3ad7f.png'),
(50, 29, 'uploads/675dc55b3b711.png'),
(51, 30, 'uploads/675dc6b716d65.png'),
(52, 30, 'uploads/675dc6b717609.png'),
(53, 31, 'uploads/675dc73dd8c9c.png'),
(54, 31, 'uploads/675dc73dd9578.png'),
(55, 32, 'uploads/675dc775840a4.png'),
(56, 32, 'uploads/675dc775848b9.png'),
(57, 33, 'uploads/675dc7c802bec.png'),
(58, 33, 'uploads/675dc7c8040ca.png');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `destinasi_id` (`destinasi_id`);

--
-- Indeks untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `destinasi_gambar`
--
ALTER TABLE `destinasi_gambar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destinasi_id` (`destinasi_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `akun`
--
ALTER TABLE `akun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `destinasi`
--
ALTER TABLE `destinasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `destinasi_gambar`
--
ALTER TABLE `destinasi_gambar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `akun` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`);

--
-- Ketidakleluasaan untuk tabel `destinasi_gambar`
--
ALTER TABLE `destinasi_gambar`
  ADD CONSTRAINT `destinasi_gambar_ibfk_1` FOREIGN KEY (`destinasi_id`) REFERENCES `destinasi` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
