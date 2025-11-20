<?php
// file: views/buku.php

// ----------------------------------------------------
// 1. BLOK PHP LOGIC (FINAL)
// ----------------------------------------------------

// PENTING: Hanya panggil Model. Model yang bertanggung jawab memuat Database.
require_once '../models/bukuModels.php';

// Inisialisasi Model
$bukuModel = new BukuModel();

// Model Kategori dan Penulis (Jika modelnya sudah ada, baris ini bisa diaktifkan)
$kategoriModel = null;
$penulisModel = null;
$kategori = [];
$penulis = [];

// Ambil parameter dari URL
$search = $_GET['search'] ?? '';
$kategori_id = $_GET['kategori'] ?? ''; 
$penulis_id = $_GET['penulis'] ?? '';   
$sort = $_GET['sort'] ?? 'recent';
$page = $_GET['page'] ?? 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Ambil data berdasarkan parameter
if (!empty($search)) {
    // Cari buku, lalu potong hasilnya untuk pagination
    $buku = $bukuModel->searchBooks($search);
    $totalBooks = count($buku);
    $buku = array_slice($buku, $offset, $limit);
} else {
    // Ambil data dengan sorting dan filtering (jika logika di model sudah lengkap)
    $buku = $bukuModel->getBooksWithPagination($limit, $offset, $sort, $kategori_id, $penulis_id);
    // Hitung total buku untuk pagination
    $totalBooks = $bukuModel->getBookCount($kategori_id, $penulis_id);
}

// Hitung total pages
$totalPages = ceil($totalBooks / $limit);

// Ambil nama Kategori/Penulis aktif untuk ditampilkan di header
$active_filter_name = "Semua Buku";

if (!empty($search)) {
    $active_filter_name = 'Hasil pencarian: "' . htmlspecialchars($search) . '"';
} 
// Catatan: Jika ada filter kategori/penulis aktif, logika untuk $active_filter_name 
// harus ditambahkan di sini (membutuhkan model Kategori/Penulis).

// Fungsi helper untuk membangun URL query string (untuk filter kategori/penulis)
$buildQuery = function ($newParams = []) use ($search, $sort, $kategori_id, $penulis_id) {
    $params = [
        'search' => $search,
        'sort' => $sort,
        'kategori' => $kategori_id,
        'penulis' => $penulis_id,
    ];
    
    $params = array_merge($params, $newParams);
    
    $query = http_build_query(array_filter($params, fn($v, $k) => !empty($v) || $k === 'page', ARRAY_FILTER_USE_BOTH));
    
    return empty($query) ? '?' : '?' . $query;
};

// Fungsi buildQuery untuk Sort
$buildSortQuery = function($newSort) use ($search, $kategori_id, $penulis_id) {
    $params = [
        'sort' => $newSort,
        'search' => $search,
        'kategori' => $kategori_id,
        'penulis' => $penulis_id,
    ];
    $query = http_build_query(array_filter($params));
    return '?' . $query;
};

// Fungsi buildQuery untuk Pagination (mengembalikan &param=value)
$buildQueryForPagination = function () use ($search, $sort, $kategori_id, $penulis_id) {
    $params = [
        'search' => $search,
        'sort' => $sort,
        'kategori' => $kategori_id,
        'penulis' => $penulis_id,
    ];
    
    // Hapus parameter 'page' dari parameter dasar
    $query = http_build_query(array_filter($params));
    
    return empty($query) ? '' : '&' . $query;
};

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Azizi.io</title>
        <link rel="stylesheet" href="../public/css/style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ========================================================= */
        /* PALET WARNA AZIZI.IO (Konsisten dengan Figma) */
        /* ========================================================= */
        :root {
            --color-primary: #424769; /* Ungu Kebiruan (Teks Utama) */
            --color-secondary: #676f9d; /* Ungu Sekunder */
            --color-bg-dark: #2d3250; /* Ungu Gelap (Header/Footer Background) */
            --color-accent: #f9b17a; /* Oranye (Aksen) */
            --color-white: #ffffff;
            --color-light-bg: #f8f9fa; /* Latar Belakang Body */
            --color-link: #3388ff; /* Biru Link */
            --font-family: 'Raleway', sans-serif;
        }

        body {
            font-family: var(--font-family);
            background: var(--color-light-bg);
            color: var(--color-primary);
        }
        
        a {
            color: var(--color-link);
            text-decoration: none;
        }
        a:hover {
            color: var(--color-accent);
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ------------------- Page Header (Banner) ------------------- */
        .page-header {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            color: var(--color-white);
            padding: 3rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 1rem;
        }

        /* Breadcrumb Styling */
        .breadcrumb a, .breadcrumb span {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        .breadcrumb span:last-child {
            font-weight: 600;
            color: var(--color-white);
        }

        /* ------------------- Catalog Layout ------------------- */
        .catalog-layout {
            display: grid;
            grid-template-columns: 250px 1fr; /* Sidebar 250px, Content 1fr */
            gap: 2rem;
        }

        /* ------------------- Sidebar (Filter) ------------------- */
        .sidebar {
            background: var(--color-white);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .filter-group {
            margin-bottom: 2rem;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-bg-dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .filter-form {
            display: flex;
            gap: 5px;
        }

        .filter-input {
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            flex-grow: 1;
        }

        .filter-btn {
            background: var(--color-accent);
            color: var(--color-white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .filter-btn:hover {
            background: #e69d65;
        }

        .filter-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            border-radius: 4px;
            color: var(--color-primary);
            transition: background 0.2s;
        }
        
        .filter-item:hover {
            background: #f0f0f5;
        }

        .filter-item.active {
            background: var(--color-secondary);
            color: var(--color-white);
            font-weight: 600;
        }
        
        .filter-item.active .filter-count {
            color: var(--color-accent);
            background: var(--color-bg-dark);
            padding: 2px 8px;
            border-radius: 10px;
        }

        .filter-count {
            font-size: 0.8rem;
            color: var(--color-secondary);
        }

        /* ------------------- Main Content (Results) ------------------- */
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .results-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-bg-dark);
        }

        .results-count {
            font-size: 0.9rem;
            color: var(--color-secondary);
        }

        .view-options {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .view-label {
            font-size: 0.9rem;
            color: var(--color-secondary);
        }

        .view-btn {
            background: #fff;
            border: 1px solid #ccc;
            color: var(--color-secondary);
            width: 35px;
            height: 35px;
            border-radius: 4px;
            cursor: pointer;
        }

        .view-btn.active {
            background: var(--color-primary);
            color: var(--color-white);
            border-color: var(--color-primary);
        }

        /* Books Grid (Default View) */
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        /* Books List View (Diaktifkan oleh JS) */
        .books-container.view-list .books-grid {
            grid-template-columns: 1fr;
        }

        /* Book Card */
        .book-card {
            background: var(--color-white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
            text-align: center; /* Default grid align center */
        }
        
        .books-container.view-list .book-card {
            display: flex;
            text-align: left;
            padding: 15px;
            gap: 15px;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
        }

        .book-cover {
            position: relative;
            height: 300px; /* Tinggi cover di Grid */
            overflow: hidden;
        }
        
        .books-container.view-list .book-cover {
            width: 120px; /* Lebar cover di List */
            height: 180px; 
            flex-shrink: 0;
        }

        .cover-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        .books-container.view-grid .book-info {
            padding: 1rem;
        }
        
        .books-container.view-list .book-info {
            padding: 0;
            flex-grow: 1;
        }

        .book-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--color-bg-dark);
            margin-bottom: 0.3rem;
            line-height: 1.4;
        }

        .book-author {
            font-size: 0.9rem;
            color: var(--color-secondary);
            margin-bottom: 0.5rem;
        }
        
        .book-meta {
            font-size: 0.8rem;
            margin-top: 5px;
            color: #999;
        }
        .books-container.view-grid .book-meta {
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .book-category {
            display: inline-block;
            background: #e9e5ff;
            color: var(--color-primary);
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .book-downloads {
            margin-left: 10px;
        }

        /* Overlay & Action Buttons */
        .book-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(45, 50, 80, 0.8); /* Dark Overlay */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .book-cover:hover .book-overlay {
            opacity: 1;
        }

        .btn-read, .btn-download {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            text-transform: uppercase;
            transition: background 0.3s;
        }

        .btn-read {
            background: var(--color-accent);
            color: var(--color-white);
        }
        .btn-read:hover {
            background: #e69d65;
        }

        .btn-download {
            background: var(--color-link);
            color: var(--color-white);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-download:hover {
            background: #2578e9;
        }

        /* Book Description (Hanya di List View) */
        .book-description {
            font-size: 0.9rem;
            color: #555;
            margin-top: 10px;
            display: none;
        }
        .books-container.view-list .book-description {
            display: block;
        }
        .books-container.view-grid .book-description {
            display: none;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 2rem;
            padding: 1rem 0;
        }

        .pagination-btn, .pagination-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            color: var(--color-primary);
            transition: background 0.3s;
        }
        .pagination-btn:hover, .pagination-number:not(.active):hover {
            background: #eee;
        }

        .pagination-number.active {
            background: var(--color-primary);
            color: var(--color-white);
            border-color: var(--color-primary);
            font-weight: 600;
        }
        
        .pagination-ellipsis {
            padding: 8px 10px;
            color: #999;
        }
        
        /* Empty State */
        .empty-state {
            grid-column: 1 / -1; /* Ambil seluruh lebar grid */
            text-align: center;
            padding: 4rem;
            border: 2px dashed #ccc;
            border-radius: 10px;
            background: var(--color-white);
        }

        .empty-state h3 {
            color: var(--color-bg-dark);
            margin-top: 1rem;
        }
        .empty-state p {
            color: var(--color-secondary);
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background: var(--color-link);
            color: var(--color-white);
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #2578e9;
        }
    </style>
</head>
<body>
    <?php include '../includes/headerGuest.php'; ?>

    <section class="page-header">
        <div class="container">
            <div class="header-content">
                <h1 class="page-title">Katalog Buku</h1>
                <p class="page-subtitle">Temukan buku digital favorit Anda dari koleksi kami</p>
                
                <nav class="breadcrumb">
                    <a href="beranda.php">Beranda</a>
                    <span>/</span>
                    <span>Katalog Buku</span>
                </nav>
            </div>
        </div>
    </section>

    <section class="catalog">
        <div class="container">
            <div class="catalog-layout">
                <aside class="sidebar">
                    <div class="filter-group">
                        <h3 class="filter-title">Pencarian</h3>
                        <form method="GET" class="filter-form">
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                                        placeholder="Cari buku..." class="filter-input">
                            <?php if (!empty($sort)): ?><input type="hidden" name="sort" value="<?= $sort ?>"><?php endif; ?>
                            <button type="submit" class="filter-btn">Cari</button>
                        </form>
                    </div>

                    <?php if (count($kategori) > 0): ?> 
                    <div class="filter-group">
                        <h3 class="filter-title">Kategori</h3>
                        <div class="filter-list">
                            <a href="buku.php?<?= ltrim($buildQuery(['kategori' => '', 'penulis' => '', 'page' => '']), '?') ?>" 
                               class="filter-item <?= empty($kategori_id) && empty($penulis_id) ? 'active' : '' ?>">
                                Semua Kategori
                                <span class="filter-count"><?= $bukuModel->getBookCount() ?></span>
                            </a>
                            <?php foreach ($kategori as $kat): ?>
                            <a href="buku.php?<?= ltrim($buildQuery(['kategori' => $kat->id, 'penulis' => '', 'page' => '']), '?') ?>" 
                               class="filter-item <?= $kategori_id == $kat->id ? 'active' : '' ?>">
                                <?= htmlspecialchars($kat->nama) ?>
                                <span class="filter-count"><?= $kat->book_count ?? '0' ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (count($penulis) > 0): ?>
                    <div class="filter-group">
                        <h3 class="filter-title">Penulis</h3>
                        <div class="filter-list">
                            <a href="buku.php?<?= ltrim($buildQuery(['kategori' => '', 'penulis' => '', 'page' => '']), '?') ?>" 
                               class="filter-item <?= empty($kategori_id) && empty($penulis_id) ? 'active' : '' ?>">
                                Semua Penulis
                            </a>
                            <?php foreach (array_slice($penulis, 0, 10) as $pen): ?>
                            <a href="buku.php?<?= ltrim($buildQuery(['penulis' => $pen->id, 'kategori' => '', 'page' => '']), '?') ?>" 
                               class="filter-item <?= $penulis_id == $pen->id ? 'active' : '' ?>">
                                <?= htmlspecialchars($pen->nama) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="filter-group">
                        <h3 class="filter-title">Urutkan</h3>
                        <div class="filter-list">
                            <a href="<?= $buildSortQuery('recent') ?>" 
                               class="filter-item <?= $sort == 'recent' ? 'active' : '' ?>">
                                Terbaru
                            </a>
                            <a href="<?= $buildSortQuery('popular') ?>" 
                               class="filter-item <?= $sort == 'popular' ? 'active' : '' ?>">
                                Populer
                            </a>
                            <a href="<?= $buildSortQuery('title') ?>" 
                               class="filter-item <?= $sort == 'title' ? 'active' : '' ?>">
                                Judul A-Z
                            </a>
                        </div>
                    </div>
                </aside>

                <main class="main-content">
                    <div class="results-header">
                        <div class="results-info">
                            <h2 class="results-title">
                                <?= $active_filter_name ?>
                            </h2>
                            <p class="results-count"><?= $totalBooks ?> buku ditemukan</p>
                        </div>

                        <div class="view-options">
                            <span class="view-label">Tampilan:</span>
                            <button class="view-btn active" data-view="grid">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <rect x="1" y="1" width="6" height="6" rx="1"/>
                                    <rect x="9" y="1" width="6" height="6" rx="1"/>
                                    <rect x="1" y="9" width="6" height="6" rx="1"/>
                                    <rect x="9" y="9" width="6" height="6" rx="1"/>
                                </svg>
                            </button>
                            <button class="view-btn" data-view="list">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <rect x="1" y="1" width="14" height="2" rx="1"/>
                                    <rect x="1" y="7" width="14" height="2" rx="1"/>
                                    <rect x="1" y="13" width="14" height="2" rx="1"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="books-container view-grid"> 
                        <?php if ($totalBooks === 0): ?>
                            <div class="empty-state">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 6.25278V19.2528M12 6.25278C10.8321 5.47686 9.24649 5 7.5 5C5.75351 5 4.16789 5.47686 3 6.25278V19.2528C4.16789 18.4769 5.75351 18 7.5 18C9.24649 18 10.8321 18.4769 12 19.2528M12 6.25278C13.1679 5.47686 14.7535 5 16.5 5C18.2465 5 19.8321 5.47686 21 6.25278V19.2528C19.8321 18.4769 18.2465 18 16.5 18C14.7535 18 13.1679 18.4769 12 19.2528" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <h3>Buku tidak ditemukan</h3>
                                <p>Maaf, tidak ada buku yang sesuai dengan kriteria pencarian Anda.</p>
                                <a href="buku.php" class="btn btn-primary">Lihat Semua Buku</a>
                            </div>
                        <?php else: ?>
                            <div class="books-grid">
                                <?php foreach ($buku as $b): ?>
                                <div class="book-card">
                                    <div class="book-cover">
                                        <img src="../uploads/covers/<?= $b->cover_path ?>" 
                                             alt="<?= htmlspecialchars($b->judul) ?>" 
                                             class="cover-img">
                                        <div class="book-overlay">
                                            <a href="detail-buku.php?id=<?= $b->id ?>" class="btn-read">Baca</a>
                                            <a href="../uploads/books/<?= $b->file_path ?>" 
                                               download 
                                               class="btn-download"
                                               onclick="incrementDownload(<?= $b->id ?>)">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                    <polyline points="7 10 12 15 17 10"/>
                                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                                </svg>
                                                Unduh
                                            </a>
                                        </div>
                                    </div>
                                    <div class="book-info">
                                        <h3 class="book-title"><?= htmlspecialchars($b->judul) ?></h3>
                                        <p class="book-author"><?= htmlspecialchars($b->nama_penulis) ?></p>
                                        <div class="book-meta">
                                            <span class="book-category"><?= htmlspecialchars($b->nama_kategori) ?></span>
                                            <span class="book-downloads"><?= $b->download_count ?> unduh</span>
                                        </div>
                                        <div class="book-description">
                                            <?= substr($b->deskripsi, 0, 100) . '...' ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php 
                        // Ambil query string saat ini (kecuali 'page') untuk menjaga filter
                        $current_query = $buildQueryForPagination();
                        $current_query = preg_replace('/&?page=\d+/', '', $current_query); // Hapus page lama
                        ?>
                        
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?><?= $current_query ?>" 
                               class="pagination-btn">← Sebelumnya</a>
                        <?php endif; ?>

                        <div class="pagination-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                    <a href="?page=<?= $i ?><?= $current_query ?>" 
                                       class="pagination-number <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?><?= $current_query ?>" 
                               class="pagination-btn">Selanjutnya →</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="../public/js/main.js"></script>
    <script>
    // View Toggle
    const viewBtns = document.querySelectorAll('.view-btn');
    const booksContainer = document.querySelector('.books-container');

    viewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const view = btn.dataset.view;
            
            // Update active button
            viewBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Update view
            booksContainer.className = 'books-container view-' + view;
        });
    });

    // Increment Download Count 
    function incrementDownload(bookId) {
        // Panggil controller untuk memperbarui jumlah unduhan
        fetch('../controllers/bukuController.php?action=download&id=' + bookId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Download count updated');
                }
            })
            .catch(error => console.error('Error:', error));
    }
    </script>
</body>
</html>