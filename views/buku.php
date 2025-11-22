<?php
require_once './config/database.php';

// Fungsi pencarian buku
$search_query = "";
$books = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $search_term = "%" . $search_query . "%";
    
    $sql = "SELECT b.*, p.name as penulis_name, k.kategori 
            FROM buku b 
            JOIN penulis p ON b.penulis_id = p.penulis_id 
            JOIN kategori k ON b.kategori_id = k.kategori_id 
            WHERE b.judul LIKE ? OR p.name LIKE ? OR k.kategori LIKE ? 
            ORDER BY b.dibuat_pada DESC";
    
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Tampilkan semua buku jika tidak ada pencarian
    $sql = "SELECT b.*, p.name as penulis_name, k.kategori 
            FROM buku b 
            JOIN penulis p ON b.penulis_id = p.penulis_id 
            JOIN kategori k ON b.kategori_id = k.kategori_id 
            ORDER BY b.dibuat_pada DESC 
            LIMIT 15";
    
    $result = $connection->query($sql);
    $books = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #2a2d44;
            --primary-medium: #4a4f73;
            --primary-light: #648599;
            --accent-color: #5d69b6;
            --accent-secondary: #f9b17a;
            --text-light: #ffffff;
            --text-dark: #2d3250;
            --text-muted: #676f9d;
            --bg-white: #ffffff;
            --bg-gray: #f8f9fa;
        }
        
        body {
            font-family: 'Raleway', sans-serif;
            background-color: var(--bg-gray);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Search Section */
        .search-section {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-medium) 100%);
            color: var(--text-light);
            padding: 60px 0;
            text-align: center;
        }
        
        .search-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .search-section .lead {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .search-box {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-box .input-group {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            background: var(--bg-white);
        }
        
        .search-box input {
            border: none;
            padding: 15px 20px;
            font-size: 1rem;
            background: var(--bg-white);
            flex: 1;
        }
        
        .search-box input:focus {
            outline: none;
            box-shadow: none;
            background: var(--bg-white);
        }
        
        .search-box button {
            background: var(--accent-color);
            border: none;
            color: var(--text-light);
            padding: 15px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .search-box button:hover {
            background: var(--primary-medium);
        }
        
        /* Book Section */
        .book-section {
            padding: 60px 0;
            flex: 1;
        }
        
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--text-dark);
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--accent-secondary);
            border-radius: 2px;
        }
        
        .book-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 25px;
        }
        
        .book-card {
            background: var(--bg-white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .book-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }
        
        .book-cover {
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-size: 1.2rem;
            font-weight: 700;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--primary-medium) 100%);
        }
        
        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .book-cover:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.3) 100%);
            z-index: 1;
        }
        
        .book-cover span {
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .book-info {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .book-author {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .book-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-dark);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        
        .book-category {
            display: inline-block;
            background: var(--bg-gray);
            color: var(--text-muted);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(0, 0, 0, 0.05);
            align-self: flex-start;
            margin-bottom: 15px;
        }
        
        .book-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }
        
        .btn-read {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            flex: 1;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-read:hover {
            background: var(--primary-medium);
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-save {
            background: transparent;
            border: 2px solid var(--accent-secondary);
            color: var(--text-dark);
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-save:hover {
            background: var(--accent-secondary);
            transform: translateY(-2px);
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
        
        .search-results-info {
            background: var(--bg-white);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        /* Responsive Design */
        @media (max-width: 1400px) {
            .book-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        @media (max-width: 1200px) {
            .book-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .book-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .search-section {
                padding: 40px 0;
            }
            
            .search-section h1 {
                font-size: 2rem;
            }
            
            .book-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .book-section {
                padding: 40px 0;
            }
        }
        
        @media (max-width: 576px) {
            .book-grid {
                grid-template-columns: 1fr;
            }
            
            .search-box input {
                padding: 12px 15px;
            }
            
            .search-box button {
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <h1>Temukan Buku Impian Anda</h1>
            <p class="lead">Jelajahi koleksi buku digital terbaik dari berbagai kategori</p>
            
            <form method="GET" action="">
                <div class="search-box">
                    <div class="input-group">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Cari judul buku, penulis, atau kategori...">
                        <button type="submit">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Book Section -->
    <section class="book-section">
        <div class="container">
            <h2 class="section-title">
                <?php echo !empty($search_query) ? 'Hasil Pencarian' : 'Koleksi Buku Terpopuler'; ?>
            </h2>
            
            <?php if (!empty($search_query)): ?>
                <div class="search-results-info">
                    <p class="mb-0">
                        Menampilkan hasil pencarian untuk: 
                        <strong>"<?php echo htmlspecialchars($search_query); ?>"</strong>
                        <?php if (!empty($books)): ?>
                            - Ditemukan <strong><?php echo count($books); ?> buku</strong>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
            
            <?php if (empty($books)): ?>
                <div class="no-results">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <h4>Buku tidak ditemukan</h4>
                    <p>Coba gunakan kata kunci yang berbeda atau lihat koleksi buku kami yang lain.</p>
                    <?php if (!empty($search_query)): ?>
                        <a href="?" class="btn-read mt-3" style="display: inline-block; width: auto; padding: 10px 20px;">
                            <i class="fas fa-book me-2"></i>Lihat Semua Buku
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="book-grid">
                    <?php foreach ($books as $book): ?>
                        <div class="book-card" onclick="window.location.href='buku_detail.php?id=<?php echo $book['buku_id']; ?>'">
                            <div class="book-cover">
                                <?php if (!empty($book['cover'])): ?>
                                    <img src="uploads/covers/<?php echo htmlspecialchars($book['cover']); ?>" alt="<?php echo htmlspecialchars($book['judul']); ?>">
                                <?php else: ?>
                                    <span><?php echo strtoupper(substr($book['kategori'], 0, 3)); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="book-info">
                                <p class="book-author">oleh <?php echo htmlspecialchars($book['penulis_name']); ?></p>
                                <h3 class="book-title"><?php echo htmlspecialchars($book['judul']); ?></h3>
                                <span class="book-category"><?php echo htmlspecialchars($book['kategori']); ?></span>
                                <div class="book-actions">
                                    <a href="buku_detail.php?id=<?php echo $book['buku_id']; ?>" class="btn-read">
                                        <i class="fas fa-book-open me-2"></i>Baca
                                    </a>
                                    <button class="btn-save" onclick="event.stopPropagation(); saveBook(<?php echo $book['buku_id']; ?>)">
                                        <i class="fas fa-bookmark"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function saveBook(bookId) {
            // Implement save book functionality
            alert('Buku dengan ID ' + bookId + ' akan disimpan');
            // You can add AJAX call here to save the book
        }
    </script>
</body>
</html>