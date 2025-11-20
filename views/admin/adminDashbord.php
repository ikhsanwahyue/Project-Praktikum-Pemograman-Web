<?php
// file: views/admin/adminDashboard.php
// TANGGUNG JAWAB: VIEW (FRONTEND)

// PERINGATAN: Logika PHP di atas (termasuk Cek Sesi dan Koneksi) harus menggunakan
// PDO dan UserModel/Model lain sesuai arsitektur MVC yang disarankan oleh Aziz.
// Bagian ini hanya fokus pada styling HTML/CSS.

require_once '../../config/database.php';
session_start();

// Cek apakah admin sudah login (tetap dipertahankan)
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// ... (Logika PHP di sini, diasumsikan sudah diperbaiki untuk menggunakan PDO/Model) ...
// ... (Saya akan menjaga variabel yang sama: $stats, $recent_books, $recent_users) ...

// Logout
if(isset($_POST['logout'])) {
    session_destroy();
    header("Location: adminLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Azizi.io</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* ========================================================= */
        /* PALET WARNA AZIZI.IO + FIGMA DASHBOARD */
        /* ========================================================= */
        :root {
            /* Warna Dasar Azizi.io */
            --bg-dark: #2d3250;     /* Ungu Gelap (Sidebar Background) */
            --bg-card: #424769;     /* Ungu Kebiruan (Aksen Dashboard) */
            --color-accent: #f9b17a; /* Oranye (Aksen Kecil) */
            --color-secondary: #676f9d; /* Ungu Sekunder */
            
            /* Warna Figma Dashboard */
            --color-white: #ffffff;
            --color-light-grey: #f8f9fa; /* Latar Belakang Main Content */
            --color-primary-figma: #5f20e4; /* Ungu Primer Figma (Blok Besar) */
            --color-blue-figma: #3388ff; /* Biru Figma (Blok Besar) */
            --font-family: 'Raleway', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background: var(--color-light-grey);
        }

        /* ------------------- Header (Top Nav) ------------------- */
        .header {
            /* Sesuai Figma: Header Biru Muda atau Garis Biru */
            background: var(--color-white); 
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--bg-dark);
            border-bottom: 3px solid var(--color-blue-figma); /* Garis Biru Aksen Figma */
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--bg-dark);
        }
        
        /* Input Search di Figma */
        .search-container {
            flex-grow: 1;
            margin: 0 50px;
            max-width: 400px;
        }
        .search-container input {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 8px 15px;
            width: 100%;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background: var(--color-accent); 
            color: var(--color-white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: var(--bg-card);
        }

        /* ------------------- Layout Grid ------------------- */
        .container-grid {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: calc(100vh - 67px); /* Tinggi dikurangi Header */
        }

        /* ------------------- Sidebar ------------------- */
        .sidebar {
            background: var(--color-white); /* Sidebar Putih Sesuai Figma */
            padding: 2rem 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            color: #333;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav a,
        .sidebar .menu-title {
            display: block;
            padding: 10px 2rem;
            color: #333;
            text-decoration: none;
            transition: background 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar .menu-title {
            color: var(--color-secondary);
            font-size: 0.85rem;
            margin-top: 15px;
            text-transform: uppercase;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            /* Warna aktif dari Figma: Ungu Muda */
            background: #e0e0f8; 
            border-left-color: var(--color-primary-figma);
            color: var(--color-primary-figma);
        }

        /* ------------------- Main Content ------------------- */
        .main-content {
            padding: 2rem;
        }

        /* Grid Statistik / Cards Utama Sesuai Figma */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--color-white);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .stat-card .number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--bg-dark); /* Angka Statistik */
        }
        
        /* Box-box besar sesuai warna Figma */
        .box-purple {
            background: var(--color-primary-figma);
            height: 150px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .box-blue {
            background: var(--color-blue-figma);
            height: 150px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .section {
            background: var(--color-white);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section h2 {
            color: var(--bg-card);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
            font-weight: 600;
        }
        
        /* Tombol */
        .btn {
            background: var(--bg-card);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn:hover {
            background: var(--color-accent);
        }
        
        /* Footer (Versi kecil di sidebar) */
        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 0.7rem;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
        
        <div class="search-container">
            <form>
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 10px; color: #888;"></i>
                    <input type="text" placeholder="Search..." style="padding-left: 40px;">
                    <i class="fas fa-sliders-h" style="position: absolute; right: 15px; top: 10px; color: #888;"></i>
                </div>
            </form>
        </div>
        
        <div class="user-info">
            <i class="far fa-user-circle fa-2x" style="color: var(--bg-card);"></i>
            <i class="fas fa-cog fa-2x" style="color: #999;"></i>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="container-grid">
        <aside class="sidebar">
            <nav>
                <a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                
                <div class="menu-title">Pages</div>
                <ul>
                    <li><a href="#"><i class="fas fa-users"></i> Manajemen User</a></li>
                    <li><a href="#"><i class="fas fa-book"></i> Manajemen Buku</a></li>
                    <li><a href="#"><i class="fas fa-pen"></i> Manajemen Penulis</a></li>
                    <li><a href="#"><i class="fas fa-tag"></i> Manajemen Kategori</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> Pesan Kontak</a></li>
                </ul>
                
                <div class="menu-title">Utilities</div>
                <ul>
                    <li><a href="#">Error 404</a></li>
                    <li><a href="#">Pengaturan</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                v1.3.0
            </div>
        </aside>

        <main class="main-content">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px;">
                <div class="box-purple"></div>
                <div class="box-blue"></div>
                <div style="display: grid; gap: 20px;">
                    <div class="box-blue"></div>
                    <div style="background: #e9e5ff; height: 150px; border-radius: 10px;"></div>
                </div>
            </div>
            
            <h2 style="color: var(--bg-dark); margin-top: 2rem;">Overview Statistik</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="number"><?php echo $stats['total_users'] ?? '0'; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Buku</h3>
                    <div class="number"><?php echo $stats['total_books'] ?? '0'; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Pesan Baru</h3>
                                        <div class="number">
                        <?php echo $stats['pesan_baru'] ?? '0'; ?>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Total Kategori</h3>
                    <div class="number"><?php echo $stats['total_categories'] ?? '0'; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Penulis</h3>
                    <div class="number"><?php echo $stats['total_authors'] ?? '0'; ?></div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 20px;">
                <div class="section">
                    <h2>Buku Terbaru</h2>
                    </div>
                
                <div class="section">
                    <h2>User Terbaru</h2>
                    </div>
            </div>

            <div class="section">
                <h2>Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="add_book.php" class="btn">Tambah Buku Baru</a>
                    <a href="add_user.php" class="btn">Tambah User Baru</a>
                    <a href="add_author.php" class="btn">Tambah Penulis Baru</a>
                    <a href="add_category.php" class="btn">Tambah Kategori Baru</a>
                </div>
            </div>
            
        </main>
    </div>
</body>
</html>