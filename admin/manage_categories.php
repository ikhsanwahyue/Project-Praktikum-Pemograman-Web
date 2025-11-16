<?php
require_once '../config/database.php';
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// Handle delete category
if(isset($_GET['delete_id'])) {
    $kategori_id = $_GET['delete_id'];
    
    // Check if category has books
    $check_stmt = $connection->prepare("SELECT COUNT(*) as book_count FROM buku WHERE kategori_id = ?");
    $check_stmt->bind_param("i", $kategori_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if($result['book_count'] > 0) {
        $_SESSION['error'] = "Tidak dapat menghapus kategori karena masih memiliki buku terkait!";
    } else {
        $stmt = $connection->prepare("DELETE FROM kategori WHERE kategori_id = ?");
        $stmt->bind_param("i", $kategori_id);
        if($stmt->execute()) {
            $_SESSION['message'] = "Kategori berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus kategori!";
        }
        $stmt->close();
    }
    header("Location: manage_categories.php");
    exit();
}

// Get all categories with book counts
$stmt = $connection->prepare("SELECT k.*, COUNT(b.buku_id) as total_buku FROM kategori k LEFT JOIN buku b ON k.kategori_id = b.kategori_id GROUP BY k.kategori_id ORDER BY k.kategori");
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kategori - Sistem Manajemen Perpustakaan</title>
    
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
        
        /* Category Icon */
        .category-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
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
        
        /* Book Count Badge */
        .book-count {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Description Text */
        .description-text {
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
                <a href="manage_categories.php" class="nav-link active">
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
                                        <i class="bi bi-tags text-primary me-2"></i>
                                        Manajemen Kategori
                                    </h3>
                                    <p class="text-muted mb-0">Kelola kategori buku dalam sistem perpustakaan digital</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="add_category.php" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        Tambah Kategori Baru
                                    </a>
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

            <!-- Categories Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card table-card animate-fade-in-up" style="animation-delay: 0.1s;">
                        <div class="table-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-list-ul me-2"></i>
                                        Daftar Kategori
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" class="form-control" placeholder="Cari kategori..." id="searchInput">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th>Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Total Buku</th>
                                        <th>Tanggal Dibuat</th>
                                        <th width="150" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($categories)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="bi bi-tags display-4 text-muted d-block mb-2"></i>
                                                <span class="text-muted">Belum ada kategori terdaftar</span>
                                                <div class="mt-3">
                                                    <a href="add_category.php" class="btn btn-primary btn-sm">
                                                        <i class="bi bi-plus-circle me-2"></i>
                                                        Tambah Kategori Pertama
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($categories as $category): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">#<?php echo $category['kategori_id']; ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="category-icon">
                                                            <i class="bi bi-tag"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($category['kategori']); ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="description-text">
                                                    <?php echo !empty($category['deskripsi']) ? htmlspecialchars($category['deskripsi']) : '<span class="text-muted">Tidak ada deskripsi</span>'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="book-count">
                                                    <i class="bi bi-book me-1"></i>
                                                    <?php echo $category['total_buku']; ?> buku
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    <?php echo date('d M Y', strtotime($category['dibuat_pada'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="edit_category.php?id=<?php echo $category['kategori_id']; ?>" class="btn btn-outline-primary btn-action" title="Edit Kategori">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="manage_categories.php?delete_id=<?php echo $category['kategori_id']; ?>" 
                                                       class="btn btn-outline-danger btn-action <?php echo $category['total_buku'] > 0 ? 'disabled' : ''; ?>" 
                                                       title="<?php echo $category['total_buku'] > 0 ? 'Tidak dapat menghapus kategori yang memiliki buku' : 'Hapus Kategori'; ?>"
                                                       onclick="<?php echo $category['total_buku'] > 0 ? 'return false;' : "return confirm('Apakah Anda yakin ingin menghapus kategori " . htmlspecialchars($category['kategori']) . "?')"; ?>">
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
                                <i class="bi bi-tags fs-1"></i>
                            </div>
                            <h4 class="mb-1"><?php echo count($categories); ?></h4>
                            <p class="text-muted mb-0">Total Kategori</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="card-body text-center">
                            <div class="text-success mb-2">
                                <i class="bi bi-book fs-1"></i>
                            </div>
                            <?php
                            $total_books = $connection->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
                            ?>
                            <h4 class="mb-1"><?php echo $total_books; ?></h4>
                            <p class="text-muted mb-0">Total Buku</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="card-body text-center">
                            <div class="text-info mb-2">
                                <i class="bi bi-collection fs-1"></i>
                            </div>
                            <?php
                            $categories_with_books = $connection->query("SELECT COUNT(DISTINCT kategori_id) as total FROM buku")->fetch_assoc()['total'];
                            ?>
                            <h4 class="mb-1"><?php echo $categories_with_books; ?></h4>
                            <p class="text-muted mb-0">Kategori Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.5s;">
                        <div class="card-body text-center">
                            <div class="text-warning mb-2">
                                <i class="bi bi-graph-up fs-1"></i>
                            </div>
                            <h4 class="mb-1">
                                <?php 
                                $avg_books = count($categories) > 0 ? round($total_books / count($categories), 1) : 0;
                                echo $avg_books;
                                ?>
                            </h4>
                            <p class="text-muted mb-0">Rata-rata Buku</p>
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
            const rows = document.querySelectorAll('#categoriesTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Enhanced delete confirmation and tooltip for disabled buttons
        document.querySelectorAll('.btn-action').forEach(button => {
            if (button.classList.contains('disabled')) {
                button.setAttribute('data-bs-toggle', 'tooltip');
                button.setAttribute('data-bs-placement', 'top');
                button.setAttribute('data-bs-title', button.getAttribute('title'));
            }
        });

        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>
</body>
</html>