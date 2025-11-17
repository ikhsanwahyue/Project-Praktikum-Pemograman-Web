<?php
// views/beranda.php
require_once '../models/bukuModels.php';
require_once '../models/kategoriModels.php';

$bukuModel = new BukuModel();
$kategoriModel = new KategoriModel();

// Ambil data untuk beranda
$bukuPopuler = $bukuModel->getPopularBooks(6);
$bukuTerbaru = $bukuModel->getRecentBooks(6);
$bukuRekomendasi = $bukuModel->getRecommendedBooks(6);
$kategori = $kategoriModel->getAllCategories();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azizi.io - Digital Book Platform</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include '../includes/headerGuest.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Temukan Dunia dalam Setiap Halaman</h1>
                <p class="hero-subtitle">Jelajahi koleksi buku digital terbaik di Azizi.io. Baca kapan saja, di mana saja.</p>
                
                <!-- Search Bar -->
                <div class="search-container">
                    <form action="buku.php" method="GET" class="search-form">
                        <input type="text" name="search" placeholder="Cari buku, penulis, atau kategori..." class="search-input">
                        <button type="submit" class="search-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Buku Digital</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Penulis</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Pembaca</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="../public/assets/hero-books.png" alt="Digital Books" class="hero-img">
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">Jelajahi Kategori</h2>
            <div class="categories-grid">
                <?php foreach ($kategori as $kat): ?>
                <a href="buku.php?kategori=<?= $kat->id ?>" class="category-card">
                    <div class="category-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M12 6.25278V19.2528M12 6.25278C10.8321 5.47686 9.24649 5 7.5 5C5.75351 5 4.16789 5.47686 3 6.25278V19.2528C4.16789 18.4769 5.75351 18 7.5 18C9.24649 18 10.8321 18.4769 12 19.2528M12 6.25278C13.1679 5.47686 14.7535 5 16.5 5C18.2465 5 19.8321 5.47686 21 6.25278V19.2528C19.8321 18.4769 18.2465 18 16.5 18C14.7535 18 13.1679 18.4769 12 19.2528" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3 class="category-name"><?= htmlspecialchars($kat->nama) ?></h3>
                    <span class="category-count"><?= $kat->book_count ?? '0' ?> buku</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Recommended Books -->
    <section class="featured-books">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Rekomendasi Untuk Anda</h2>
                <a href="buku.php" class="view-all">Lihat Semua</a>
            </div>
            <div class="books-grid">
                <?php foreach ($bukuRekomendasi as $buku): ?>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="../uploads/covers/<?= $buku->cover_path ?>" alt="<?= htmlspecialchars($buku->judul) ?>" class="cover-img">
                        <div class="book-overlay">
                            <a href="detail-buku.php?id=<?= $buku->id ?>" class="btn-read">Baca</a>
                            <a href="../uploads/books/<?= $buku->file_path ?>" download class="btn-download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title"><?= htmlspecialchars($buku->judul) ?></h3>
                        <p class="book-author"><?= htmlspecialchars($buku->nama_penulis) ?></p>
                        <div class="book-meta">
                            <span class="book-category"><?= htmlspecialchars($buku->nama_kategori) ?></span>
                            <span class="book-downloads"><?= $buku->download_count ?> unduh</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Popular Books -->
    <section class="popular-books">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Buku Populer</h2>
                <a href="buku.php?sort=popular" class="view-all">Lihat Semua</a>
            </div>
            <div class="books-grid">
                <?php foreach ($bukuPopuler as $buku): ?>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="../uploads/covers/<?= $buku->cover_path ?>" alt="<?= htmlspecialchars($buku->judul) ?>" class="cover-img">
                        <div class="book-badge">Hot</div>
                        <div class="book-overlay">
                            <a href="detail-buku.php?id=<?= $buku->id ?>" class="btn-read">Baca</a>
                        </div>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title"><?= htmlspecialchars($buku->judul) ?></h3>
                        <p class="book-author"><?= htmlspecialchars($buku->nama_penulis) ?></p>
                        <div class="book-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= 4 ? 'active' : '' ?>">★</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- New Books -->
    <section class="new-books">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Buku Terbaru</h2>
                <a href="buku.php?sort=recent" class="view-all">Lihat Semua</a>
            </div>
            <div class="books-grid">
                <?php foreach ($bukuTerbaru as $buku): ?>
                <div class="book-card">
                    <div class="book-cover">
                        <img src="../uploads/covers/<?= $buku->cover_path ?>" alt="<?= htmlspecialchars($buku->judul) ?>" class="cover-img">
                        <div class="book-badge new">New</div>
                        <div class="book-overlay">
                            <a href="detail-buku.php?id=<?= $buku->id ?>" class="btn-read">Baca</a>
                        </div>
                    </div>
                    <div class="book-info">
                        <h3 class="book-title"><?= htmlspecialchars($buku->judul) ?></h3>
                        <p class="book-author"><?= htmlspecialchars($buku->nama_penulis) ?></p>
                        <div class="book-meta">
                            <span class="book-year"><?= $buku->tahun_terbit ?></span>
                            <span class="book-pages"><?= $buku->halaman ?> hlm</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Mulai Petualangan Membaca Anda</h2>
                <p class="cta-text">Bergabung dengan ribuan pembaca lainnya dan temukan buku-buku menarik yang menunggu untuk dibaca.</p>
                <div class="cta-buttons">
                    <a href="daftar.php" class="btn btn-primary">Daftar Sekarang</a>
                    <a href="buku.php" class="btn btn-secondary">Jelajahi Koleksi</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script src="../public/js/main.js"></script>
</body>
</html>