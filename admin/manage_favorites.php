<?php
require_once '../config/database.php';
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// Handle delete favorite
if(isset($_GET['delete_favorite'])) {
    $favorit_id = $_GET['delete_favorite'];
    $stmt = $connection->prepare("DELETE FROM buku_favorit WHERE favorit_id = ?");
    $stmt->bind_param("i", $favorit_id);
    if($stmt->execute()) {
        $_SESSION['message'] = "Favorit berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus favorit!";
    }
    $stmt->close();
    header("Location: manage_favorites.php");
    exit();
}

// Handle delete saved book
if(isset($_GET['delete_saved'])) {
    $simpan_id = $_GET['delete_saved'];
    $stmt = $connection->prepare("DELETE FROM simpan_buku WHERE simpan_id = ?");
    $stmt->bind_param("i", $simpan_id);
    if($stmt->execute()) {
        $_SESSION['message'] = "Buku tersimpan berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus buku tersimpan!";
    }
    $stmt->close();
    header("Location: manage_favorites.php");
    exit();
}

// Get all favorites with user and book info
$stmt = $connection->prepare("
    SELECT f.*, u.name as user_name, u.username, b.judul as buku_judul, b.cover
    FROM buku_favorit f 
    LEFT JOIN user u ON f.user_id = u.user_id 
    LEFT JOIN buku b ON f.buku_id = b.buku_id 
    ORDER BY f.ditambahkan_pada DESC
");
$stmt->execute();
$favorites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get all saved books with user and book info
$stmt = $connection->prepare("
    SELECT s.*, u.name as user_name, u.username, b.judul as buku_judul, b.cover
    FROM simpan_buku s 
    LEFT JOIN user u ON s.user_id = u.user_id 
    LEFT JOIN buku b ON s.buku_id = b.buku_id 
    ORDER BY s.disimpan_pada DESC
");
$stmt->execute();
$saved_books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get statistics
$total_favorites = count($favorites);
$total_saved = count($saved_books);
$unique_users_fav = $connection->query("SELECT COUNT(DISTINCT user_id) as total FROM buku_favorit")->fetch_assoc()['total'];
$unique_users_saved = $connection->query("SELECT COUNT(DISTINCT user_id) as total FROM simpan_buku")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit & Simpan - Sistem Manajemen Perpustakaan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
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
        
        /* Form Styles */
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .form-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .form-body {
            padding: 1.5rem;
        }
        
        /* Table Styles */
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
            overflow: hidden;
        }
        
        .table-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-responsive {
            border-radius: 0 0 15px 15px;
        }
        
        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #374151;
            padding: 1rem 0.75rem;
        }
        
        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-color: #f1f5f9;
        }
        
        .table tbody tr:hover {
            background-color: #f8fafc;
        }
        
        /* Action Buttons */
        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 8px;
        }
        
        /* Status Badges */
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        
        /* User Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        /* Book Cover */
        .book-cover {
            width: 40px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .book-cover-placeholder {
            width: 40px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
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
            
            .table-responsive {
                font-size: 0.875rem;
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
        
        /* Search and Filter */
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 2.5rem;
        }
        
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        /* Book Title */
        .book-title {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.3;
        }
        
        /* Username */
        .username {
            color: #6b7280;
            font-size: 0.75rem;
        }
        
        /* Section Tabs */
        .nav-tabs .nav-link {
            border: none;
            color: #6b7280;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
        }
        
        .nav-tabs .nav-link.active {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            background: none;
        }
        
        /* Favorite Badge */
        .favorite-badge {
            background: #fef3f2;
            color: #dc2626;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Saved Badge */
        .saved-badge {
            background: #f0f9ff;
            color: #0369a1;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="adminDashboard.php" class="sidebar-brand">
                <i class="bi bi-book-half"></i>
                <span>Perpustakaan</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="adminDashboard.php" class="nav-link">
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
                <a href="manage_favorites.php" class="nav-link active">
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
                                    <form method="POST" action="adminDashboard.php" class="d-inline">
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
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card form-card animate-fade-in-up">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-2">
                                        <i class="bi bi-heart text-primary me-2"></i>
                                        Favorit & Simpan
                                    </h3>
                                    <p class="text-muted mb-0">Kelola buku favorit dan buku yang disimpan oleh pengguna</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-filter me-2"></i>
                                            Filter
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="?filter=all">Semua</a></li>
                                            <li><a class="dropdown-item" href="?filter=favorites">Favorit Saja</a></li>
                                            <li><a class="dropdown-item" href="?filter=saved">Disimpan Saja</a></li>
                                            <li><a class="dropdown-item" href="?filter=recent">Terbaru</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show animate-fade-in-up" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show animate-fade-in-up" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.1s;">
                        <div class="card-body text-center">
                            <div class="text-danger mb-2">
                                <i class="bi bi-heart-fill fs-1"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $total_favorites; ?></h4>
                            <p class="text-muted mb-0">Total Favorit</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="bi bi-bookmark-check fs-1"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $total_saved; ?></h4>
                            <p class="text-muted mb-0">Total Disimpan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="bi bi-people fs-1"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $unique_users_fav; ?></h4>
                            <p class="text-muted mb-0">User Favorit</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="bi bi-person-check fs-1"></i>
                            </div>
                            <h4 class="mb-1"><?php echo $unique_users_saved; ?></h4>
                            <p class="text-muted mb-0">User Menyimpan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="row mb-4">
                <div class="col-12">
                    <ul class="nav nav-tabs" id="favoritesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="favorites-tab" data-bs-toggle="tab" data-bs-target="#favorites" type="button" role="tab">
                                <i class="bi bi-heart me-2"></i>
                                Buku Favorit
                                <span class="badge bg-danger ms-2"><?php echo $total_favorites; ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="saved-tab" data-bs-toggle="tab" data-bs-target="#saved" type="button" role="tab">
                                <i class="bi bi-bookmark me-2"></i>
                                Buku Disimpan
                                <span class="badge bg-primary ms-2"><?php echo $total_saved; ?></span>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="favoritesTabsContent">
                <!-- Favorites Tab -->
                <div class="tab-pane fade show active" id="favorites" role="tabpanel">
                    <div class="card table-card animate-fade-in-up">
                        <div class="table-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-heart-fill text-danger me-2"></i>
                                        Daftar Buku Favorit
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control" placeholder="Cari favorit..." id="searchFavorites">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="favoritesTable">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>User</th>
                                        <th>Buku</th>
                                        <th width="140">Tanggal</th>
                                        <th width="100" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($favorites)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="bi bi-heart display-4 text-muted d-block mb-2"></i>
                                                <span class="text-muted">Belum ada buku favorit</span>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($favorites as $favorite): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#<?php echo $favorite['favorit_id']; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="user-avatar">
                                                            <?php echo strtoupper(substr($favorite['user_name'], 0, 1)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($favorite['user_name']); ?></div>
                                                        <div class="username">@<?php echo htmlspecialchars($favorite['username']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <?php if(!empty($favorite['cover'])): ?>
                                                            <img src="../uploads/covers/<?php echo htmlspecialchars($favorite['cover']); ?>" alt="Cover" class="book-cover">
                                                        <?php else: ?>
                                                            <div class="book-cover-placeholder">
                                                                <i class="bi bi-book"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="book-title"><?php echo htmlspecialchars($favorite['buku_judul']); ?></div>
                                                        <span class="favorite-badge">
                                                            <i class="bi bi-heart-fill me-1"></i>Favorit
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    <?php echo date('d M Y', strtotime($favorite['ditambahkan_pada'])); ?>
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?php echo date('H:i', strtotime($favorite['ditambahkan_pada'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="manage_favorites.php?delete_favorite=<?php echo $favorite['favorit_id']; ?>" 
                                                       class="btn btn-outline-danger btn-action" 
                                                       title="Hapus Favorit"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus favorit <?php echo htmlspecialchars($favorite['buku_judul']); ?> dari <?php echo htmlspecialchars($favorite['user_name']); ?>?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Saved Books Tab -->
                <div class="tab-pane fade" id="saved" role="tabpanel">
                    <div class="card table-card animate-fade-in-up">
                        <div class="table-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-bookmark-check text-primary me-2"></i>
                                        Daftar Buku Disimpan
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control" placeholder="Cari buku disimpan..." id="searchSaved">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="savedTable">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>User</th>
                                        <th>Buku</th>
                                        <th width="140">Tanggal</th>
                                        <th width="100" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($saved_books)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="bi bi-bookmark display-4 text-muted d-block mb-2"></i>
                                                <span class="text-muted">Belum ada buku disimpan</span>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($saved_books as $saved): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#<?php echo $saved['simpan_id']; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="user-avatar">
                                                            <?php echo strtoupper(substr($saved['user_name'], 0, 1)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($saved['user_name']); ?></div>
                                                        <div class="username">@<?php echo htmlspecialchars($saved['username']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <?php if(!empty($saved['cover'])): ?>
                                                            <img src="../uploads/covers/<?php echo htmlspecialchars($saved['cover']); ?>" alt="Cover" class="book-cover">
                                                        <?php else: ?>
                                                            <div class="book-cover-placeholder">
                                                                <i class="bi bi-book"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="book-title"><?php echo htmlspecialchars($saved['buku_judul']); ?></div>
                                                        <span class="saved-badge">
                                                            <i class="bi bi-bookmark-check me-1"></i>Disimpan
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    <?php echo date('d M Y', strtotime($saved['disimpan_pada'])); ?>
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?php echo date('H:i', strtotime($saved['disimpan_pada'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="manage_favorites.php?delete_saved=<?php echo $saved['simpan_id']; ?>" 
                                                       class="btn btn-outline-danger btn-action" 
                                                       title="Hapus Buku Disimpan"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus buku tersimpan <?php echo htmlspecialchars($saved['buku_judul']); ?> dari <?php echo htmlspecialchars($saved['user_name']); ?>?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
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

        // Search functionality for favorites
        document.getElementById('searchFavorites').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#favoritesTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Search functionality for saved books
        document.getElementById('searchSaved').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#savedTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Enhanced delete confirmation
        document.querySelectorAll('.btn-action').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm(this.getAttribute('onclick')?.match(/'([^']+)'/)?.[1] || 'Apakah Anda yakin?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>