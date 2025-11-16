<?php
require_once '../config/database.php';
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// Handle delete book
if(isset($_GET['delete_id'])) {
    $buku_id = $_GET['delete_id'];
    
    // Get book data to delete files
    $stmt = $connection->prepare("SELECT cover, file_buku FROM buku WHERE buku_id = ?");
    $stmt->bind_param("i", $buku_id);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Delete files from uploads folder
    $base_dir = "../uploads/";
    if($book['cover']) {
        $cover_path = $base_dir . 'covers/' . $book['cover'];
        if(file_exists($cover_path)) @unlink($cover_path);
    }
    if($book['file_buku']) {
        $book_path = $base_dir . 'books/' . $book['file_buku'];
        if(file_exists($book_path)) @unlink($book_path);
    }
    
    // Delete from database
    $stmt = $connection->prepare("DELETE FROM buku WHERE buku_id = ?");
    $stmt->bind_param("i", $buku_id);
    if($stmt->execute()) {
        $_SESSION['message'] = "Buku berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus buku!";
    }
    $stmt->close();
    header("Location: manage_books.php");
    exit();
}

// Get all books
$stmt = $connection->prepare("
    SELECT b.*, p.name as penulis_name, k.kategori 
    FROM buku b 
    LEFT JOIN penulis p ON b.penulis_id = p.penulis_id 
    LEFT JOIN kategori k ON b.kategori_id = k.kategori_id 
    ORDER BY b.dibuat_pada DESC
");
$stmt->execute();
$books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$base_dir = "../uploads/";
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku - Sistem Manajemen Perpustakaan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
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
        
        /* Section Cards */
        .section-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .section-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .section-body {
            padding: 1.5rem;
        }
        
        /* Table Styles */
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: none;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8f9fa;
        }
        
        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
        
        /* Book Cover */
        .book-cover {
            width: 50px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .no-cover {
            width: 50px;
            height: 70px;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        
        /* Action Buttons */
        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 8px;
            margin: 0 2px;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
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
        
        /* Search and Filter */
        .search-box {
            max-width: 300px;
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
        
        /* DataTable Customization */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            margin: 0 2px;
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
                <a href="manage_books.php" class="nav-link active">
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
                    <div class="card section-card animate-fade-in-up">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-2">
                                        <i class="bi bi-book text-primary me-2"></i>
                                        Manajemen Buku
                                    </h3>
                                    <p class="text-muted mb-0">Kelola koleksi buku perpustakaan digital Anda</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="add_book.php" class="btn btn-primary btn-lg">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        Tambah Buku Baru
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

            <!-- Books Table -->
            <div class="card table-card animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="card-body">
                    <!-- Table Controls -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <label class="me-2 mb-0">Show:</label>
                                <select class="form-select form-select-sm" style="width: auto;" id="entriesSelect">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <label class="ms-2 mb-0">entries</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="search-box ms-md-auto">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Cari buku..." id="searchInput">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Books Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="booksTable">
                            <thead>
                                <tr>
                                    <th width="60">Cover</th>
                                    <th>Judul Buku</th>
                                    <th>Penulis</th>
                                    <th>Kategori</th>
                                    <th>File</th>
                                    <th>Tanggal</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($books as $book): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $cover_path = $base_dir . 'covers/' . $book['cover'];
                                        if($book['cover'] && file_exists($cover_path)): 
                                        ?>
                                            <img src="../view_image.php?file=<?php echo urlencode($book['cover']); ?>" 
                                                 alt="Cover" 
                                                 class="book-cover"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="no-cover" style="display: none;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php else: ?>
                                            <div class="no-cover">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($book['judul']); ?></div>
                                        <small class="text-muted">ID: <?php echo $book['buku_id']; ?></small>
                                    </td>
                                    <td>
                                        <?php if($book['penulis_name']): ?>
                                            <span class="badge bg-light text-dark">
                                                <?php echo htmlspecialchars($book['penulis_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($book['kategori']): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                <?php echo htmlspecialchars($book['kategori']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $book_path = $base_dir . 'books/' . $book['file_buku'];
                                        if($book['file_buku'] && file_exists($book_path)): 
                                        ?>
                                            <a href="../download_book.php?file=<?php echo urlencode($book['file_buku']); ?>" 
                                               class="btn btn-sm btn-outline-success btn-action"
                                               data-bs-toggle="tooltip" 
                                               title="Download PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                No File
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php 
                                            if($book['terbit_pada']) {
                                                echo date('d M Y', strtotime($book['terbit_pada']));
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="./edit_book.php?id=<?php echo $book['buku_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary btn-action"
                                               data-bs-toggle="tooltip" 
                                               title="Edit Buku">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="manage_books.php?delete_id=<?php echo $book['buku_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger btn-action"
                                               data-bs-toggle="tooltip" 
                                               title="Hapus Buku"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus buku <?php echo addslashes($book['judul']); ?>?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Footer -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="text-muted" id="tableInfo">
                                Menampilkan 0 sampai 0 dari 0 entri
                            </div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Table pagination" class="d-flex justify-content-end">
                                <ul class="pagination pagination-sm mb-0" id="pagination">
                                    <!-- Pagination will be generated by JavaScript -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (for simple table functionality) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
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

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Simple table search and pagination
        $(document).ready(function() {
            let currentPage = 1;
            let entriesPerPage = 10;
            let currentData = [];

            // Initialize table
            function initializeTable() {
                currentData = $('#booksTable tbody tr').toArray();
                updateTableInfo();
                renderTable();
            }

            // Render table based on current page and search
            function renderTable() {
                const startIndex = (currentPage - 1) * entriesPerPage;
                const endIndex = startIndex + entriesPerPage;
                const filteredData = filterData();
                const pageData = filteredData.slice(startIndex, endIndex);

                $('#booksTable tbody').empty();
                $('#booksTable tbody').append(pageData);

                updateTableInfo();
                renderPagination(filteredData.length);
            }

            // Filter data based on search input
            function filterData() {
                const searchTerm = $('#searchInput').val().toLowerCase();
                if (!searchTerm) return currentData;

                return currentData.filter(row => {
                    const text = $(row).text().toLowerCase();
                    return text.includes(searchTerm);
                });
            }

            // Update table information
            function updateTableInfo() {
                const filteredData = filterData();
                const totalEntries = filteredData.length;
                const startIndex = (currentPage - 1) * entriesPerPage + 1;
                const endIndex = Math.min(startIndex + entriesPerPage - 1, totalEntries);

                if (totalEntries === 0) {
                    $('#tableInfo').text('Tidak ada data yang ditemukan');
                } else {
                    $('#tableInfo').text(`Menampilkan ${startIndex} sampai ${endIndex} dari ${totalEntries} entri`);
                }
            }

            // Render pagination
            function renderPagination(totalEntries) {
                const totalPages = Math.ceil(totalEntries / entriesPerPage);
                const pagination = $('#pagination');
                pagination.empty();

                // Previous button
                const prevDisabled = currentPage === 1 ? ' disabled' : '';
                pagination.append(`<li class="page-item${prevDisabled}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>`);

                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    const active = i === currentPage ? ' active' : '';
                    pagination.append(`<li class="page-item${active}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`);
                }

                // Next button
                const nextDisabled = currentPage === totalPages ? ' disabled' : '';
                pagination.append(`<li class="page-item${nextDisabled}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>`);
            }

            // Event listeners
            $('#searchInput').on('input', function() {
                currentPage = 1;
                renderTable();
            });

            $('#entriesSelect').on('change', function() {
                entriesPerPage = parseInt($(this).val());
                currentPage = 1;
                renderTable();
            });

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                if (!isNaN(page)) {
                    currentPage = page;
                    renderTable();
                }
            });

            // Initialize table
            initializeTable();
        });
    </script>
</body>
</html>