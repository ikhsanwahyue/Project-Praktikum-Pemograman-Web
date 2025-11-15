-- Database: perpustakaan
-- Description: Database sistem manajemen buku dan favorit user
-- Created by: Mahasiswa Informatika

-- Tabel user (ID dimulai dari 1000)
CREATE TABLE `user` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `foto_profil` VARCHAR(255) NULL, -- ⭐ UBAH: VARCHAR untuk nama file
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) AUTO_INCREMENT = 1000; 

-- Tabel admin (ID dimulai dari 500)
CREATE TABLE `admin` (
    `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) AUTO_INCREMENT = 500;

-- Tabel penulis (ID dimulai dari 200)
CREATE TABLE `penulis` (
    `penulis_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) AUTO_INCREMENT = 200;

-- Tabel kategori (ID dimulai dari 10)
CREATE TABLE `kategori` (
    `kategori_id` INT AUTO_INCREMENT PRIMARY KEY,
    `kategori` VARCHAR(100) UNIQUE NOT NULL,
    `deskripsi` TEXT,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) AUTO_INCREMENT = 10;

-- Tabel buku (ID dimulai dari 1000)
CREATE TABLE `buku` (
    `buku_id` INT AUTO_INCREMENT PRIMARY KEY,
    `penulis_id` INT,
    `kategori_id` INT,
    `judul` VARCHAR(255) NOT NULL,
    `cover` VARCHAR(255) NULL, -- ⭐ UBAH: VARCHAR untuk nama file cover
    `deskripsi` TEXT,
    `file_buku` VARCHAR(255) NULL, -- ⭐ UBAH: VARCHAR untuk nama file PDF
    `penerbit` VARCHAR(100),
    `terbit_pada` DATE,
    `dibuat_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`penulis_id`) REFERENCES `penulis`(`penulis_id`),
    FOREIGN KEY (`kategori_id`) REFERENCES `kategori`(`kategori_id`)
) AUTO_INCREMENT = 1000;

-- Tabel buku_favorit (ID dimulai dari 5000)
CREATE TABLE `buku_favorit` (
    `favorit_id` INT AUTO_INCREMENT PRIMARY KEY,
    `buku_id` INT,
    `user_id` INT,
    `ditambahkan_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`buku_id`) REFERENCES `buku`(`buku_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`)
) AUTO_INCREMENT = 5000;

-- Tabel simpan_buku (ID dimulai dari 3000)
CREATE TABLE `simpan_buku` (
    `simpan_id` INT AUTO_INCREMENT PRIMARY KEY,
    `buku_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `disimpan_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`buku_id`) REFERENCES `buku`(`buku_id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`),
    CONSTRAINT `unique_simpan_buku` UNIQUE (`user_id`, `buku_id`)
) AUTO_INCREMENT = 3000;

-- Tabel comments (ID dimulai dari 7000)
CREATE TABLE `comments` (
   `comment_id` INT AUTO_INCREMENT PRIMARY KEY,
   `user_id` INT NOT NULL,
   `buku_id` INT NOT NULL,
   `rating` INT NOT NULL,
   `comment` TEXT NOT NULL,
   `disimpan_pada` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`),
   FOREIGN KEY (`buku_id`) REFERENCES `buku`(`buku_id`)
) AUTO_INCREMENT = 7000;

-- Insert sample data untuk testing
-- INSERT INTO `admin` (`username`, `password`) VALUES 
-- ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- INSERT INTO `penulis` (`name`, `email`) VALUES 
-- ('Andrea Hirata', 'andrea.hirata@example.com'),
-- ('Tere Liye', 'tere.liye@example.com'),
-- ('Dee Lestari', 'dee.lestari@example.com');

-- INSERT INTO `kategori` (`kategori`, `deskripsi`) VALUES 
-- ('Fiksi', 'Buku-buku fiksi dan novel'),
-- ('Pendidikan', 'Buku-buku pendidikan dan akademik'),
-- ('Teknologi', 'Buku-buku tentang teknologi dan programming');

-- INSERT INTO `user` (`name`, `username`, `email`, `password`) VALUES 
-- ('Budi Santoso', 'budi123', 'budi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
-- ('Sari Indah', 'sari_indah', 'sari@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- End of script