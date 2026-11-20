-- ============================================================
-- Database : portal_berita  (Portal Berita - PHP native + MySQL)
-- Import lewat phpMyAdmin (tab Import) atau HeidiSQL (Run SQL file)
--
-- Login dashboard:
--   admin  / admin123   (level admin  : akses penuh)
--   editor / editor123  (level editor : kelola berita, komentar, galeri)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `portal_berita`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portal_berita`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `galeri_foto`, `galeri_album`, `berita_foto`, `komentar`, `berita`, `kategori`, `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- users (Manajemen User)
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `level` ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`,`nama`,`username`,`email`,`password`,`level`,`aktif`) VALUES
(1,'Administrator','admin','admin@portalberita.test','$2b$10$wkWk7UjWlTxfpRwVLti.yeQQpiJ2W2wmOiSEoJSILxFFgx2votCWG','admin',1),
(2,'Redaksi','editor','editor@portalberita.test','$2b$10$O2wTpsj2X35cUIOqRP2be.zFziEeE.5ep67iv6AewgixKJGQo40TS','editor',1);

-- ------------------------------------------------------------
-- kategori
-- ------------------------------------------------------------
CREATE TABLE `kategori` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kategori_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`id`,`nama`,`slug`) VALUES
(1,'Politik','politik'),
(2,'Teknologi','teknologi'),
(3,'Olahraga','olahraga'),
(4,'Ekonomi','ekonomi'),
(5,'Hiburan','hiburan');

-- ------------------------------------------------------------
-- berita
-- ------------------------------------------------------------
CREATE TABLE `berita` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(280) NOT NULL,
  `kategori_id` INT UNSIGNED NOT NULL,
  `isi` LONGTEXT NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `penulis_id` INT UNSIGNED DEFAULT NULL,
  `status` ENUM('publish','draft') NOT NULL DEFAULT 'publish',
  `dilihat` INT UNSIGNED NOT NULL DEFAULT 0,
  `tanggal` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_berita_slug` (`slug`),
  KEY `idx_kategori` (`kategori_id`),
  KEY `idx_penulis` (`penulis_id`),
  CONSTRAINT `fk_berita_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_berita_penulis` FOREIGN KEY (`penulis_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `berita` (`judul`,`slug`,`kategori_id`,`isi`,`penulis_id`,`status`,`dilihat`,`tanggal`) VALUES
('Pemerintah Siapkan Program Layanan Publik Digital Terpadu','pemerintah-siapkan-program-layanan-publik-digital-terpadu',1,'<p>Pemerintah menyiapkan program layanan publik digital terpadu agar warga dapat mengurus berbagai dokumen secara daring.</p><p>Program ini ditargetkan mulai diuji coba di beberapa daerah sebelum diperluas secara nasional.</p>',1,'publish',120,'2026-10-23 08:00:00'),
('DPRD Bahas Rancangan Anggaran Daerah Tahun Depan','dprd-bahas-rancangan-anggaran-daerah-tahun-depan',1,'<p>Rapat pembahasan rancangan anggaran daerah berlangsung selama dua hari dan dihadiri seluruh anggota dewan.</p><p>Sejumlah fraksi menyoroti alokasi untuk pendidikan dan infrastruktur.</p>',1,'publish',85,'2026-10-22 14:30:00'),
('Aplikasi Lokal Raih Penghargaan Inovasi Terbaik Tahun Ini','aplikasi-lokal-raih-penghargaan-inovasi-terbaik-tahun-ini',2,'<p>Sebuah aplikasi buatan pengembang lokal meraih penghargaan inovasi terbaik dalam ajang teknologi tahunan.</p><p>Aplikasi tersebut membantu pelaku usaha kecil mencatat keuangan dengan lebih mudah.</p>',2,'publish',240,'2026-10-22 09:15:00'),
('Peluncuran Ponsel Pintar Terbaru Hadirkan Baterai Tahan Dua Hari','peluncuran-ponsel-pintar-terbaru-hadirkan-baterai-tahan-dua-hari',2,'<p>Produsen perangkat seluler meluncurkan ponsel pintar terbaru dengan kapasitas baterai besar dan layar yang lebih terang.</p><p>Perangkat ini mulai dijual bulan depan.</p>',2,'publish',310,'2026-10-21 16:45:00'),
('Tim Nasional Persiapkan Laga Uji Coba Jelang Kualifikasi','tim-nasional-persiapkan-laga-uji-coba-jelang-kualifikasi',3,'<p>Tim nasional menggelar pemusatan latihan untuk menghadapi laga uji coba sebelum babak kualifikasi dimulai.</p><p>Pelatih memanggil sejumlah pemain muda untuk memperkuat skuad.</p>',2,'publish',190,'2026-10-21 10:00:00'),
('Harga Kebutuhan Pokok Stabil Menjelang Akhir Tahun','harga-kebutuhan-pokok-stabil-menjelang-akhir-tahun',4,'<p>Harga sejumlah kebutuhan pokok di pasar tradisional relatif stabil dalam sepekan terakhir.</p><p>Pedagang berharap pasokan tetap lancar hingga akhir tahun.</p>',1,'publish',75,'2026-10-20 11:20:00'),
('Festival Film Pendek Digelar Tiga Hari di Pusat Kota','festival-film-pendek-digelar-tiga-hari-di-pusat-kota',5,'<p>Festival film pendek tahunan kembali digelar dan menampilkan puluhan karya sineas muda dari berbagai daerah.</p><p>Penonton dapat menikmati pemutaran gratis setiap sore.</p>',2,'publish',140,'2026-10-19 19:00:00'),
('Kampus Gelar Kontes Robot Tingkat Nasional','kampus-gelar-kontes-robot-tingkat-nasional',2,'<p>Sebuah perguruan tinggi menjadi tuan rumah kontes robot tingkat nasional yang diikuti puluhan tim mahasiswa.</p><p>Panitia menyiapkan beberapa kategori lomba, mulai dari robot penyelamat hingga robot pemadam api.</p>',1,'draft',0,'2026-10-18 13:10:00');

-- ------------------------------------------------------------
-- komentar
-- ------------------------------------------------------------
CREATE TABLE `komentar` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `berita_id` INT UNSIGNED NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `isi` TEXT NOT NULL,
  `status` ENUM('pending','approved') NOT NULL DEFAULT 'pending',
  `tanggal` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_komentar_berita` (`berita_id`),
  CONSTRAINT `fk_komentar_berita` FOREIGN KEY (`berita_id`) REFERENCES `berita` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `komentar` (`berita_id`,`nama`,`email`,`isi`,`status`,`tanggal`) VALUES
(1,'Budi','budi@example.com','Semoga programnya benar-benar mempermudah warga.','approved','2026-10-23 09:00:00'),
(3,'Sari','sari@example.com','Keren, bangga dengan karya anak bangsa!','approved','2026-10-22 12:00:00'),
(4,'Andi',NULL,'Harganya kira-kira berapa ya?','pending','2026-10-22 18:30:00');

-- ------------------------------------------------------------
-- berita_foto (Berita Foto: tampil sebagai slider di beranda)
-- ------------------------------------------------------------
CREATE TABLE `berita_foto` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `keterangan` VARCHAR(500) DEFAULT NULL,
  `tanggal` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `berita_foto` (`judul`,`keterangan`,`tanggal`) VALUES
('Suasana Pagi di Pusat Kota','Aktivitas warga di pusat kota pada pagi hari.','2026-10-23 07:00:00'),
('Pawai Budaya Akhir Pekan','Peserta pawai mengenakan busana adat daerah.','2026-10-22 08:00:00');

-- ------------------------------------------------------------
-- galeri (Album + Foto)
-- ------------------------------------------------------------
CREATE TABLE `galeri_album` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(170) NOT NULL,
  `keterangan` VARCHAR(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_album_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `galeri_album` (`id`,`nama`,`slug`,`keterangan`) VALUES
(1,'Kegiatan Redaksi','kegiatan-redaksi','Dokumentasi kegiatan tim redaksi'),
(2,'Liputan Lapangan','liputan-lapangan','Foto-foto dari liputan di lapangan');

CREATE TABLE `galeri_foto` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `album_id` INT UNSIGNED NOT NULL,
  `judul` VARCHAR(150) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `tanggal` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_foto_album` (`album_id`),
  CONSTRAINT `fk_foto_album` FOREIGN KEY (`album_id`) REFERENCES `galeri_album` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
