<?php
require_once '../config/database.php';
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// Handle delete comment
if(isset($_GET['delete_id'])) {
    $comment_id = $_GET['delete_id'];
    $stmt = $connection->prepare("DELETE FROM comments WHERE comment_id = ?");
    $stmt->bind_param("i", $comment_id);
    if($stmt->execute()) {
        $_SESSION['message'] = "Komentar berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus komentar!";
    }
    $stmt->close();
    header("Location: manage_comments.php");
    exit();
}

// Get all comments with user and book info
$stmt = $connection->prepare("
    SELECT c.*, u.name as user_name, u.username, b.judul as buku_judul 
    FROM comments c 
    LEFT JOIN user u ON c.user_id = u.user_id 
    LEFT JOIN buku b ON c.buku_id = b.buku_id 
    ORDER BY c.disimpan_pada DESC
");
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Komentar - Sistem Manajemen Perpustakaan</title>
    
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
        
        /* Rating Stars */
        .rating-stars {
            color: #ffc107;
            font-size: 0.875rem;
        }
        
        .rating-badge {
            background: #fff3cd;
            color: #856404;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Comment Text */
        .comment-text {
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
                <a href="manage_comments.php" class="nav-link active">
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
                                        <i class="bi bi-chat-text text-primary me-2"></i>
                                        Manajemen Komentar
                                    </h3>
                                    <p class="text-muted mb-0">Kelola komentar dan rating dari pengguna sistem perpustakaan digital</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-filter me-2"></i>
                                            Filter
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="?filter=all">Semua Komentar</a></li>
                                            <li><a class="dropdown-item" href="?filter=recent">Terbaru</a></li>
                                            <li><a class="dropdown-item" href="?filter=high_rating">Rating Tinggi</a></li>
                                            <li><a class="dropdown-item" href="?filter=low_rating">Rating Rendah</a></li>
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

            <!-- Comments Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card table-card animate-fade-in-up" style="animation-delay: 0.1s;">
                        <div class="table-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-list-ul me-2"></i>
                                        Daftar Komentar
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control" placeholder="Cari komentar..." id="searchInput">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="commentsTable">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>User</th>
                                        <th>Buku</th>
                                        <th width="120">Rating</th>
                                        <th>Komentar</th>
                                        <th width="140">Tanggal</th>
                                        <th width="100" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($comments)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="bi bi-chat display-4 text-muted d-block mb-2"></i>
                                                <span class="text-muted">Belum ada komentar</span>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($comments as $comment): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#<?php echo $comment['comment_id']; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="user-avatar">
                                                            <?php echo strtoupper(substr($comment['user_name'], 0, 1)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($comment['user_name']); ?></div>
                                                        <div class="username">@<?php echo htmlspecialchars($comment['username']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="book-title">
                                                    <?php echo htmlspecialchars($comment['buku_judul']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rating-stars">
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <?php if($i <= $comment['rating']): ?>
                                                                <i class="bi bi-star-fill"></i>
                                                            <?php else: ?>
                                                                <i class="bi bi-star"></i>
                                                            <?php endif; ?>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="rating-badge"><?php echo $comment['rating']; ?>.0</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="comment-text">
                                                    <?php echo htmlspecialchars($comment['comment']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    <?php echo date('d M Y', strtotime($comment['disimpan_pada'])); ?>
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?php echo date('H:i', strtotime($comment['disimpan_pada'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="manage_comments.php?delete_id=<?php echo $comment['comment_id']; ?>" 
                                                       class="btn btn-outline-danger btn-action" 
                                                       title="Hapus Komentar"
                                                       onclick="return confirm('Apakah Anda yakin ingin menghapus komentar dari <?php echo htmlspecialchars($comment['user_name']); ?>?')">
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

            <!-- Statistics Cards -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="card-body text-center">
                            <div class="text-primary mb-2">
                                <i class="bi bi-chat-dots fs-1"></i>
                            </div>
                            <h4 class="mb-1"><?php echo count($comments); ?></h4>
                            <p class="text-muted mb-0">Total Komentar</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="bi bi-star fs-1"></i>
                            </div>
                            <?php
                            $avg_rating = $connection->query("SELECT AVG(rating) as avg_rating FROM comments")->fetch_assoc()['avg_rating'];
                            $avg_rating = $avg_rating ? round($avg_rating, 1) : 0;
                            ?>
                            <h4 class="mb-1"><?php echo $avg_rating; ?></h4>
                            <p class="text-muted mb-0">Rating Rata-rata</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="bi bi-people fs-1"></i>
                            </div>
                            <?php
                            $unique_users = $connection->query("SELECT COUNT(DISTINCT user_id) as total FROM comments")->fetch_assoc()['total'];
                            ?>
                            <h4 class="mb-1"><?php echo $unique_users; ?></h4>
                            <p class="text-muted mb-0">User Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.5s;">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="bi bi-graph-up fs-1"></i>
                            </div>
                            <?php
                            $today_comments = $connection->query("SELECT COUNT(*) as total FROM comments WHERE DATE(disimpan_pada) = CURDATE()")->fetch_assoc()['total'];
                            ?>
                            <h4 class="mb-1"><?php echo $today_comments; ?></h4>
                            <p class="text-muted mb-0">Komentar Hari Ini</p>
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

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#commentsTable tbody tr');
            
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