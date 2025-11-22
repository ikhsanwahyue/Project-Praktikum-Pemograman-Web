<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Section - Perpustakaan Digital</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Search Section */
        .search-section {
            background-color: white;
            padding: 40px 0;
            text-align: center;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        
        .search-section h1 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .search-box {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 15px 20px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .search-box button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: #3498db;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .search-box button:hover {
            background: #2980b9;
        }
        
        /* Book Section */
        .book-section {
            padding: 50px 0;
            flex: 1;
        }
        
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #2c3e50;
            border-left: 5px solid #3498db;
            padding-left: 15px;
        }
        
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .book-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }
        
        .book-cover {
            height: 200px;
            background-color: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .book-info {
            padding: 20px;
        }
        
        .book-author {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .book-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .book-category {
            display: inline-block;
            background: #ecf0f1;
            color: #7f8c8d;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .book-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
        include '../includes/headerUser.php';
    } else {
        include '../includes/headerGuest.php';
    }
    ?>

    <!-- Search Section -->
    <section class="search-section">
        <div class="container">
            <h1>Temukan Buku Impian Anda</h1>
            <div class="search-box">
                <input type="text" placeholder="Cari buku atau paket...">
                <button>Cari</button>
            </div>
        </div>
    </section>

    <!-- Book Section -->
    <section class="book-section">
        <div class="container">
            <h2 class="section-title">Pencarian yang Dipilih</h2>
            <div class="book-grid">
                <!-- Book 1 -->
                <div class="book-card">
                    <div class="book-cover">EKONOMI</div>
                    <div class="book-info">
                        <p class="book-author">oleh George Ikhsan James</p>
                        <h3 class="book-title">Ekonomi Bisnis</h3>
                        <span class="book-category">Ekonomi</span>
                    </div>
                </div>
                
                <!-- Book 2 -->
                <div class="book-card">
                    <div class="book-cover" style="background-color: #e74c3c;">BISNIS</div>
                    <div class="book-info">
                        <p class="book-author">oleh George Ikhsan James</p>
                        <h3 class="book-title">Ekonomi Bisnis</h3>
                        <span class="book-category">Bisnis</span>
                    </div>
                </div>
                
                <!-- Book 3 -->
                <div class="book-card">
                    <div class="book-cover" style="background-color: #9b59b6;">MANAJEMEN</div>
                    <div class="book-info">
                        <p class="book-author">oleh George Ikhsan James</p>
                        <h3 class="book-title">Ekonomi Bisnis</h3>
                        <span class="book-category">Manajemen</span>
                    </div>
                </div>
                
                <!-- Book 4 -->
                <div class="book-card">
                    <div class="book-cover" style="background-color: #1abc9c;">KEUANGAN</div>
                    <div class="book-info">
                        <p class="book-author">oleh George Ikhsan James</p>
                        <h3 class="book-title">Ekonomi Bisnis</h3>
                        <span class="book-category">Keuangan</span>
                    </div>
                </div>
                
                <!-- Book 5 -->
                <div class="book-card">
                    <div class="book-cover" style="background-color: #f39c12;">PEMASARAN</div>
                    <div class="book-info">
                        <p class="book-author">oleh George Ikhsan James</p>
                        <h3 class="book-title">Ekonomi Bisnis</h3>
                        <span class="book-category">Pemasaran</span>
                    </div>
                </div>
                
                <!-- Book 6 -->
                <div class="book-card">
                    <div class="book-cover" style="background-color: #16a085;">STRATEGI</div>
                    <div class="book-info">
                        <p class="book-author">oleh George Ikhsan James</p>
                        <h3 class="book-title">Ekonomi Bisnis</h3>
                        <span class="book-category">Strategi</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

        <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>