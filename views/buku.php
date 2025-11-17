<?php
// views/buku.php
require_once '../models/bukuModels.php';
require_once '../models/kategoriModels.php';
require_once '../models/penulisModels.php';

$bukuModel = new BukuModel();
$kategoriModel = new KategoriModel();
$penulisModel = new PenulisModel();

// Ambil parameter
$search = $_GET['search'] ?? '';
$kategori_id = $_GET['kategori'] ?? '';
$penulis_id = $_GET['penulis'] ?? '';
$sort = $_GET['sort'] ?? 'recent';
$page = $_GET['page'] ?? 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Ambil data berdasarkan parameter
if (!empty($search)) {
    $buku = $bukuModel->searchBooks($search);
    $totalBooks = count($buku);
    $buku = array_slice($buku, $offset, $limit);
} elseif (!empty($kategori_id)) {
    $buku = $bukuModel->getBooksByCategory($kategori_id);
    $totalBooks = count($buku);
    $buku = array_slice($buku, $offset, $limit);
} elseif (!empty($penulis_id)) {
    $buku = $bukuModel->getBooksByAuthor($penulis_id);
    $totalBooks = count($buku);
    $buku = array_slice($buku, $offset, $limit);
} else {
    // Default: semua buku dengan pagination
    $buku = $bukuModel->getBooksWithPagination($limit, $offset);
    $totalBooks = $bukuModel->getBookCount();
}

// Ambil data untuk filter
$kategori = $kategoriModel->getAllCategories();
$penulis = $penulisModel->getAllAuthors();

// Hitung total pages
$totalPages = ceil($totalBooks / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Azizi.io</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include '../includes/headerGuest.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <div class="header-content">
                <h1 class="page-title">Katalog Buku</h1>
                <p class="page-subtitle">Temukan buku digital favorit Anda dari koleksi kami</p>
                
                <!-- Breadcrumb -->
                <nav class="breadcrumb">
                    <a href="beranda.php">Beranda</a>
                    <span>/</span>
                    <span>Katalog Buku</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="catalog">
        <div class="container">
            <div class="catalog-layout">
                <!-- Sidebar Filter -->
                <aside class="sidebar">
                    <!-- Search Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Pencarian</h3>
                        <form method="GET" class="filter-form">
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                                   placeholder="Cari buku..." class="filter-input">
                            <button type="submit" class="filter-btn">Cari</button>
                        </form>
                    </div>

                    <!-- Kategori Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Kategori</h3>
                        <div class="filter-list">
                            <a href="buku.php" class="filter-item <?= empty($kategori_id) ? 'active' : '' ?>">
                                Semua Kategori
                                <span class="filter-count"><?= $bukuModel->getBookCount() ?></span>
                            </a>
                            <?php foreach ($kategori as $kat): ?>
                            <a href="buku.php?kategori=<?= $kat->id ?>" 
                               class="filter-item <?= $kategori_id == $kat->id ? 'active' : '' ?>">
                                <?= htmlspecialchars($kat->nama) ?>
                                <span class="filter-count"><?= $kat->book_count ?? '0' ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Penulis Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Penulis</h3>
                        <div class="filter-list">
                            <a href="buku.php" class="filter-item <?= empty($penulis_id) ? 'active' : '' ?>">
                                Semua Penulis
                            </a>
                            <?php foreach (array_slice($penulis, 0, 10) as $pen): ?>
                            <a href="buku.php?penulis=<?= $pen->id ?>" 
                               class="filter-item <?= $penulis_id == $pen->id ? 'active' : '' ?>">
                                <?= htmlspecialchars($pen->nama) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Sort Filter -->
                    <div class="filter-group">
                        <h3 class="filter-title">Urutkan</h3>
                        <div class="filter-list">
                            <a href="?sort=recent<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($kategori_id) ? '&kategori=' . $kategori_id : '' ?>" 
                               class="filter-item <?= $sort == 'recent' ? 'active' : '' ?>">
                                Terbaru
                            </a>
                            <a href="?sort=popular<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($kategori_id) ? '&kategori=' . $kategori_id : '' ?>" 
                               class="filter-item <?= $sort == 'popular' ? 'active' : '' ?>">
                                Populer
                            </a>
                            <a href="?sort=title<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($kategori_id) ? '&kategori=' . $kategori_id : '' ?>" 
                               class="filter-item <?= $sort == 'title' ? 'active' : '' ?>">
                                Judul A-Z
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="main-content">
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-info">
                            <h2 class="results-title">
                                <?php if (!empty($search)): ?>
                                    Hasil pencarian: "<?= htmlspecialchars($search) ?>"
                                <?php elseif (!empty($kategori_id)): ?>
                                    Kategori: <?= htmlspecialchars($kategori[array_search($kategori_id, array_column($kategori, 'id'))]->nama ?? 'Unknown' ?>
                                <?php elseif (!empty($penulis_id)): ?>
                                    Penulis: <?= htmlspecialchars($penulis[array_search($penulis_id, array_column($penulis, 'id'))]->nama ?? 'Unknown' ?>
                                <?php else: ?>
                                    Semua Buku
                                <?php endif; ?>
                            </h2>
                            <p class="results-count"><?= $totalBooks ?> buku ditemukan</p>
                        </div>

                        <!-- View Options -->
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

                    <!-- Books Grid -->
                    <div class="books-container view-grid">
                        <?php if (empty($buku)): ?>
                            <div class="empty-state">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 6.25278V19.2528M12 6.25278C10.8321 5.47686 9.24649 5 7.5 5C5.75351 5 4.16789 5.47686 3 6.25278V19.2528C4.16789 18.4769 5.75351 18 7.5 18C9.24649 18 10.8321 18.4769 12 19.2528M12 6.25278C13.1679 5.47686 14.7535 5 16.5 5C18.2465 5 19.8321 5.47686 21 6.25278V19.2528C19.8321 18.4769 18.2465 18 16.5 18C14.7535 18 13.1679 18.4769 12 19.2528" stroke="#667eea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
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

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($kategori_id) ? '&kategori=' . $kategori_id : '' ?><?= !empty($penulis_id) ? '&penulis=' . $penulis_id : '' ?><?= !empty($sort) ? '&sort=' . $sort : '' ?>" 
                               class="pagination-btn">← Sebelumnya</a>
                        <?php endif; ?>

                        <div class="pagination-numbers">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                    <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($kategori_id) ? '&kategori=' . $kategori_id : '' ?><?= !empty($penulis_id) ? '&penulis=' . $penulis_id : '' ?><?= !empty($sort) ? '&sort=' . $sort : '' ?>" 
                                       class="pagination-number <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($kategori_id) ? '&kategori=' . $kategori_id : '' ?><?= !empty($penulis_id) ? '&penulis=' . $penulis_id : '' ?><?= !empty($sort) ? '&sort=' . $sort : '' ?>" 
                               class="pagination-btn">Selanjutnya →</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </section>

    <!-- Footer -->
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