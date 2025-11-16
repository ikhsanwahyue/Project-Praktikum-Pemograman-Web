<?php
require_once '../config/database.php';
session_start();

// Cek apakah admin sudah login
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// Ambil statistik
$stats = [];
try {
    // Total Users
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM user");
    $stmt->execute();
    $stats['total_users'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total Books
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM buku");
    $stmt->execute();
    $stats['total_books'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total Authors
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM penulis");
    $stmt->execute();
    $stats['total_authors'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total Categories
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM kategori");
    $stmt->execute();
    $stats['total_categories'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total Comments
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM comments");
    $stmt->execute();
    $stats['total_comments'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Recent Books
    $stmt = $connection->prepare("
        SELECT b.*, p.name as penulis_name, k.kategori 
        FROM buku b 
        LEFT JOIN penulis p ON b.penulis_id = p.penulis_id 
        LEFT JOIN kategori k ON b.kategori_id = k.kategori_id 
        ORDER BY b.dibuat_pada DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Recent Users
    $stmt = $connection->prepare("SELECT * FROM user ORDER BY dibuat_pada DESC LIMIT 5");
    $stmt->execute();
    $recent_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

} catch (Exception $e) {
    $error = $e->getMessage();
}

// Logout
if(isset($_POST['logout'])) {
    session_destroy();
    header("Location: adminLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Manajemen Perpustakaan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary-gradient);
            color: white;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }
        
        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .sidebar.collapsed .sidebar-brand span {
            display: none;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }
        
        .nav-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        .sidebar.collapsed ~ .main-content {
            margin-left: var(--sidebar-collapsed);
        }
        
        /* Header */
        .top-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        /* Section Cards */
        .section-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .section-header {
            padding: 1.5rem 1.5rem 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .section-body {
            padding: 1rem 1.5rem;
        }
        
        /* Quick Actions */
        .quick-action-card {
            background: var(--primary-gradient);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
            border: none;
        }
        
        .quick-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        /* Table Styles */
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
        
        /* Hamburger Menu */
        .hamburger-btn {
            border: none;
            background: none;
            font-size: 1.25rem;
            color: #495057;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .hamburger-btn:hover {
            background: #f8f9fa;
        }
        
        /* User Dropdown */
        .user-dropdown .dropdown-toggle {
            border: none;
            background: none;
            color: #495057;
            font-weight: 500;
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            
            .sidebar.show ~ .sidebar-overlay {
                display: block;
            }
        }
        
        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="#" class="sidebar-brand">
                <i class="bi bi-book-half"></i>
                <span>Perpustakaan</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="#" class="nav-link active">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="manage_users.php" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span>Manajemen User</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="manage_books.php" class="nav-link">
                    <i class="bi bi-book"></i>
                    <span>Manajemen Buku</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="manage_authors.php" class="nav-link">
                    <i class="bi bi-pencil"></i>
                    <span>Manajemen Penulis</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="manage_categories.php" class="nav-link">
                    <i class="bi bi-tags"></i>
                    <span>Manajemen Kategori</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="manage_comments.php" class="nav-link">
                    <i class="bi bi-chat"></i>
                    <span>Manajemen Komentar</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="manage_favorites.php" class="nav-link">
                    <i class="bi bi-heart"></i>
                    <span>Favorit & Simpan</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col">
                        <button class="hamburger-btn" id="sidebarToggle">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                    <div class="col-auto">
                        <div class="dropdown user-dropdown">
                            <button class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <div class="me-2">
                                    <i class="bi bi-person-circle fs-4"></i>
                                </div>
                                <div class="d-none d-sm-block">
                                    <div class="fw-semibold"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
                                    <small class="text-muted">Administrator</small>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" class="d-inline">
                                        <button type="submit" name="logout" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <div class="container-fluid py-4">
            <!-- Welcome Banner -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card section-card animate-fade-in-up">
                        <div class="card-body py-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-2">Selamat Datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! 👋</h3>
                                    <p class="text-muted mb-0">Berikut adalah ringkasan aktivitas sistem perpustakaan digital Anda.</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 d-inline-block">
                                        <i class="bi bi-graph-up-arrow fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s;">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-number text-primary"><?php echo $stats['total_users']; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="stat-number text-success"><?php echo $stats['total_books']; ?></div>
                        <div class="stat-label">Total Buku</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-pencil"></i>
                        </div>
                        <div class="stat-number text-info"><?php echo $stats['total_authors']; ?></div>
                        <div class="stat-label">Total Penulis</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-card animate-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div class="stat-number text-warning"><?php echo $stats['total_categories']; ?></div>
                        <div class="stat-label">Total Kategori</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-card animate-fade-in-up" style="animation-delay: 0.5s;">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-chat"></i>
                        </div>
                        <div class="stat-number text-danger"><?php echo $stats['total_comments']; ?></div>
                        <div class="stat-label">Total Komentar</div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="stat-card animate-fade-in-up" style="animation-delay: 0.6s;">
                        <div class="stat-icon bg-purple bg-opacity-10 text-purple">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div class="stat-number text-purple"><?php echo array_sum($stats); ?></div>
                        <div class="stat-label">Total Data</div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Recent Books -->
                <div class="col-xl-6">
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Buku Terbaru
                            </h5>
                        </div>
                        <div class="section-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Judul</th>
                                            <th>Penulis</th>
                                            <th>Kategori</th>
                                            <th>Tanggal Terbit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($recent_books as $book): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-journal-text text-muted me-2"></i>
                                                    <span class="text-truncate" style="max-width: 150px;">
                                                        <?php echo htmlspecialchars($book['judul']); ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($book['penulis_name'] ?? '-'); ?></td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo htmlspecialchars($book['kategori'] ?? '-'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($book['terbit_pada'] ?? '-'); ?>
                                                </small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="col-xl-6">
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-plus text-success me-2"></i>
                                User Terbaru
                            </h5>
                        </div>
                        <div class="section-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Bergabung</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($recent_users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="bi bi-person text-muted"></i>
                                                    </div>
                                                    <?php echo htmlspecialchars($user['name']); ?>
                                                </div>
                                            </td>
                                            <td>@<?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($user['dibuat_pada']); ?>
                                                </small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="col-12">
                    <div class="card section-card">
                        <div class="section-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightning text-warning me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="section-body">
                            <div class="row g-3">
                                <div class="col-xl-3 col-md-6">
                                    <a href="add_book.php" class="quick-action-card">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-plus-circle fs-2 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Tambah Buku</h6>
                                                <small class="opacity-75">Tambah buku baru</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <a href="add_user.php" class="quick-action-card">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-plus fs-2 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Tambah User</h6>
                                                <small class="opacity-75">Buat akun user baru</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <a href="add_author.php" class="quick-action-card">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-pencil-square fs-2 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Tambah Penulis</h6>
                                                <small class="opacity-75">Tambah data penulis</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <a href="add_category.php" class="quick-action-card">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-tag fs-2 me-3"></i>
                                            <div>
                                                <h6 class="mb-1">Tambah Kategori</h6>
                                                <small class="opacity-75">Buat kategori baru</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        
        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
        
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
        });
        
        // Auto-collapse sidebar on mobile
        function handleResize() {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('show');
            } else {
                sidebar.classList.remove('show');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize(); // Initial check
        
        // Add some interactivity to stats cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>