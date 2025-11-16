<?php
require_once '../config/database.php';
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// Get authors and categories for dropdown
$authors = $connection->query("SELECT * FROM penulis ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$categories = $connection->query("SELECT * FROM kategori ORDER BY kategori")->fetch_all(MYSQLI_ASSOC);

if(isset($_POST['add_book'])) {
    $judul = $_POST['judul'];
    $penulis_id = $_POST['penulis_id'];
    $kategori_id = $_POST['kategori_id'];
    $penerbit = $_POST['penerbit'];
    $terbit_pada = $_POST['terbit_pada'];
    $deskripsi = $_POST['deskripsi'];

    // Validasi penulis dan kategori
    $author_exists = $connection->query("SELECT penulis_id FROM penulis WHERE penulis_id = '$penulis_id'")->num_rows > 0;
    $category_exists = $connection->query("SELECT kategori_id FROM kategori WHERE kategori_id = '$kategori_id'")->num_rows > 0;
    
    if(!$author_exists) {
        $error = "Penulis yang dipilih tidak ditemukan!";
    } elseif(!$category_exists) {
        $error = "Kategori yang dipilih tidak ditemukan!";
    } else {
        // Handle file uploads
        $cover_name = null;
        $file_buku_name = null;
        $upload_errors = [];

        // UPLOAD PATHS - RELATIVE TO PROJECT ROOT
        $base_dir = "../uploads/"; // Dari folder admin ke uploads
        $cover_dir = $base_dir . "covers/";
        $book_dir = $base_dir . "books/";

        // UPLOAD COVER
        if(isset($_FILES['cover']) && $_FILES['cover']['error'] === 0) {
            $cover_ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            $cover_name = 'cover_' . uniqid() . '.' . $cover_ext;
            $target_file = $cover_dir . $cover_name;
            $uploadOk = 1;

            // Check if image file is a actual image
            $check = @getimagesize($_FILES["cover"]["tmp_name"]);
            if($check === false) {
                $upload_errors[] = "File cover bukan gambar yang valid.";
                $uploadOk = 0;
            }

            // Check file size (2MB)
            if ($_FILES["cover"]["size"] > 2000000) {
                $upload_errors[] = "Ukuran file cover terlalu besar. Maksimal 2MB.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if(!in_array($cover_ext, $allowed_types)) {
                $upload_errors[] = "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                $uploadOk = 0;
            }

            // Try to upload
            if ($uploadOk == 1) {
                if (!move_uploaded_file($_FILES["cover"]["tmp_name"], $target_file)) {
                    $upload_errors[] = "Terjadi error saat upload cover.";
                    $cover_name = null;
                }
            }
        }

        // UPLOAD BOOK FILE
        if(isset($_FILES['file_buku']) && $_FILES['file_buku']['error'] === 0) {
            $file_buku_ext = strtolower(pathinfo($_FILES['file_buku']['name'], PATHINFO_EXTENSION));
            $file_buku_name = 'book_' . uniqid() . '.' . $file_buku_ext;
            $target_file = $book_dir . $file_buku_name;
            $uploadOk = 1;

            // Check file size (10MB)
            if ($_FILES["file_buku"]["size"] > 10000000) {
                $upload_errors[] = "Ukuran file buku terlalu besar. Maksimal 10MB.";
                $uploadOk = 0;
            }

            // Allow only PDF
            if($file_buku_ext != "pdf") {
                $upload_errors[] = "Hanya file PDF yang diperbolehkan untuk buku.";
                $uploadOk = 0;
            }

            // Try to upload
            if ($uploadOk == 1) {
                if (!move_uploaded_file($_FILES["file_buku"]["tmp_name"], $target_file)) {
                    $upload_errors[] = "Terjadi error saat upload file buku.";
                    $file_buku_name = null;
                }
            }
        }

        // Jika ada error upload, tampilkan
        if(!empty($upload_errors)) {
            $error = implode("<br>", $upload_errors);
        } else {
            // Insert into database
            $stmt = $connection->prepare("INSERT INTO buku (judul, penulis_id, kategori_id, penerbit, terbit_pada, deskripsi, cover, file_buku) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siisssss", $judul, $penulis_id, $kategori_id, $penerbit, $terbit_pada, $deskripsi, $cover_name, $file_buku_name);

            if($stmt->execute()) {
                $_SESSION['message'] = "Buku berhasil ditambahkan!";
                header("Location: manage_books.php");
                exit();
            } else {
                $error = "Gagal menambahkan buku! Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Check folder status
$base_dir = "../uploads/";
$cover_dir = $base_dir . "covers/";
$book_dir = $base_dir . "books/";

$cover_exists = file_exists($cover_dir);
$cover_writable = is_writable($cover_dir);
$book_exists = file_exists($book_dir);
$book_writable = is_writable($book_dir);
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Sistem Manajemen Perpustakaan</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
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
        
        /* Form Controls */
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .required::after {
            content: " *";
            color: #dc2626;
        }
        
        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            height: auto;
            padding: 0.75rem 1rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 10px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0;
            line-height: 1.5;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .select2-results__option--highlighted[aria-selected] {
            background-color: #667eea !important;
        }
        
        .select2-search--dropdown .select2-search__field {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem;
        }
        
        .select2-dropdown {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        /* File Upload */
        .file-upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: #f9fafb;
        }
        
        .file-upload-area:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .file-upload-area.dragover {
            border-color: #667eea;
            background: #e0e7ff;
        }
        
        .file-info {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
        
        /* Status Card */
        .status-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.5rem;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .status-item:last-child {
            border-bottom: none;
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
        
        /* Preview Image */
        .preview-container {
            display: none;
            margin-top: 1rem;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Validation Styles */
        .is-invalid {
            border-color: #dc2626 !important;
        }
        
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc2626;
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
                    <div class="card form-card animate-fade-in-up">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="mb-2">
                                        <i class="bi bi-plus-circle text-primary me-2"></i>
                                        Tambah Buku Baru
                                    </h3>
                                    <p class="text-muted mb-0">Tambahkan buku baru ke koleksi perpustakaan digital</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="manage_books.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Kembali ke Daftar Buku
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show animate-fade-in-up" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Main Form -->
                <div class="col-lg-8">
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.1s;">
                        <div class="form-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-journal-text me-2"></i>
                                Informasi Buku
                            </h5>
                        </div>
                        <div class="form-body">
                            <form method="POST" enctype="multipart/form-data" id="bookForm">
                                <div class="row g-3">
                                    <!-- Judul Buku -->
                                    <div class="col-12">
                                        <label for="judul" class="form-label required">Judul Buku</label>
                                        <input type="text" class="form-control" id="judul" name="judul" required 
                                               placeholder="Masukkan judul buku">
                                    </div>

                                    <!-- Penulis dan Kategori -->
                                    <div class="col-md-6">
                                        <label for="penulis_id" class="form-label required">Penulis</label>
                                        <select class="form-select select2-search" id="penulis_id" name="penulis_id" required>
                                            <option value="">Pilih Penulis</option>
                                            <?php foreach($authors as $author): ?>
                                                <option value="<?php echo $author['penulis_id']; ?>">
                                                    <?php echo htmlspecialchars($author['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="kategori_id" class="form-label required">Kategori</label>
                                        <select class="form-select select2-search" id="kategori_id" name="kategori_id" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php foreach($categories as $category): ?>
                                                <option value="<?php echo $category['kategori_id']; ?>">
                                                    <?php echo htmlspecialchars($category['kategori']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Penerbit dan Tanggal Terbit -->
                                    <div class="col-md-6">
                                        <label for="penerbit" class="form-label">Penerbit</label>
                                        <input type="text" class="form-control" id="penerbit" name="penerbit" 
                                               placeholder="Nama penerbit">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="terbit_pada" class="form-label">Tanggal Terbit</label>
                                        <input type="date" class="form-control" id="terbit_pada" name="terbit_pada">
                                    </div>

                                    <!-- Deskripsi -->
                                    <div class="col-12">
                                        <label for="deskripsi" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" 
                                                  placeholder="Deskripsi singkat tentang buku..."></textarea>
                                    </div>

                                    <!-- File Uploads -->
                                    <div class="col-12">
                                        <h6 class="mb-3">
                                            <i class="bi bi-paperclip me-2"></i>
                                            File Upload
                                        </h6>
                                    </div>

                                    <!-- Cover Upload -->
                                    <div class="col-md-6">
                                        <label for="cover" class="form-label">Cover Buku</label>
                                        <div class="file-upload-area" id="coverUploadArea">
                                            <i class="bi bi-image fs-1 text-muted mb-2"></i>
                                            <p class="mb-1">Drag & drop cover buku di sini</p>
                                            <p class="mb-2">atau</p>
                                            <input type="file" class="form-control d-none" id="cover" name="cover" accept="image/*">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('cover').click()">
                                                Pilih File
                                            </button>
                                            <div class="file-info">
                                                Format: JPG, PNG, GIF | Maks: 2MB
                                            </div>
                                        </div>
                                        <div class="preview-container" id="coverPreview">
                                            <img src="" alt="Preview Cover" class="preview-image">
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearCoverPreview()">
                                                <i class="bi bi-x-circle me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Book File Upload -->
                                    <div class="col-md-6">
                                        <label for="file_buku" class="form-label">File Buku (PDF)</label>
                                        <div class="file-upload-area" id="bookUploadArea">
                                            <i class="bi bi-file-earmark-pdf fs-1 text-muted mb-2"></i>
                                            <p class="mb-1">Drag & drop file PDF di sini</p>
                                            <p class="mb-2">atau</p>
                                            <input type="file" class="form-control d-none" id="file_buku" name="file_buku" accept=".pdf">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('file_buku').click()">
                                                Pilih File
                                            </button>
                                            <div class="file-info">
                                                Format: PDF | Maks: 10MB
                                            </div>
                                        </div>
                                        <div class="preview-container" id="bookPreview">
                                            <i class="bi bi-file-earmark-pdf fs-1 text-primary"></i>
                                            <div class="file-name mt-2 fw-semibold"></div>
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearBookPreview()">
                                                <i class="bi bi-x-circle me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="manage_books.php" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle me-2"></i>
                                                Batal
                                            </a>
                                            <button type="submit" name="add_book" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-2"></i>
                                                Tambah Buku
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="col-lg-4">
                    <!-- Status Card -->
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="form-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Status Sistem
                            </h5>
                        </div>
                        <div class="form-body">
                            <div class="status-card">
                                <div class="status-item">
                                    <span class="fw-semibold">Folder Covers:</span>
                                    <span class="badge <?php echo $cover_exists && $cover_writable ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $cover_exists && $cover_writable ? 'READY' : 'ERROR'; ?>
                                    </span>
                                </div>
                                <div class="status-item">
                                    <span class="fw-semibold">Folder Books:</span>
                                    <span class="badge <?php echo $book_exists && $book_writable ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $book_exists && $book_writable ? 'READY' : 'ERROR'; ?>
                                    </span>
                                </div>
                                <div class="status-item">
                                    <span class="fw-semibold">Total Penulis:</span>
                                    <span class="badge bg-primary"><?php echo count($authors); ?></span>
                                </div>
                                <div class="status-item">
                                    <span class="fw-semibold">Total Kategori:</span>
                                    <span class="badge bg-primary"><?php echo count($categories); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tips Card -->
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="form-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightbulb me-2"></i>
                                Tips Upload
                            </h5>
                        </div>
                        <div class="form-body">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">Cover yang Baik</small>
                                    <p class="mb-0 text-muted small">Gunakan gambar dengan resolusi minimal 500x700 pixel</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">File PDF</small>
                                    <p class="mb-0 text-muted small">Pastikan file PDF tidak terproteksi dan dapat dibaca</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">Informasi Lengkap</small>
                                    <p class="mb-0 text-muted small">Isi semua field yang wajib untuk pengalaman pengguna yang lebih baik</p>
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/id.js"></script>
    
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

        // Initialize Select2
        $(document).ready(function() {
            $('.select2-search').select2({
                placeholder: function() {
                    $(this).data('placeholder');
                },
                allowClear: true,
                language: "id",
                width: '100%',
                theme: 'bootstrap-5',
                minimumResultsForSearch: 1
            });

            // Custom validation for Select2
            $('#penulis_id').on('change', function() {
                validateSelect2(this, 'penulis-feedback');
            });

            $('#kategori_id').on('change', function() {
                validateSelect2(this, 'kategori-feedback');
            });
        });

        function validateSelect2(selectElement, feedbackId) {
            const value = $(selectElement).val();
            const feedback = document.getElementById(feedbackId);
            
            if (!value) {
                $(selectElement).addClass('is-invalid');
                feedback.style.display = 'block';
            } else {
                $(selectElement).removeClass('is-invalid');
                feedback.style.display = 'none';
            }
        }

        // File Upload Preview Functions
        const coverInput = document.getElementById('cover');
        const bookInput = document.getElementById('file_buku');
        const coverPreview = document.getElementById('coverPreview');
        const bookPreview = document.getElementById('bookPreview');
        const coverUploadArea = document.getElementById('coverUploadArea');
        const bookUploadArea = document.getElementById('bookUploadArea');

        // Cover preview
        coverInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        coverPreview.querySelector('img').src = e.target.result;
                        coverPreview.style.display = 'block';
                        coverUploadArea.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                }
            }
        });

        // Book file preview
        bookInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.type === 'application/pdf') {
                    bookPreview.querySelector('.file-name').textContent = file.name;
                    bookPreview.style.display = 'block';
                    bookUploadArea.style.display = 'none';
                }
            }
        });

        // Clear preview functions
        function clearCoverPreview() {
            coverInput.value = '';
            coverPreview.style.display = 'none';
            coverUploadArea.style.display = 'block';
        }

        function clearBookPreview() {
            bookInput.value = '';
            bookPreview.style.display = 'none';
            bookUploadArea.style.display = 'block';
        }

        // Drag and drop functionality
        function setupDragDrop(uploadArea, input, preview) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadArea.classList.add('dragover');
            }

            function unhighlight() {
                uploadArea.classList.remove('dragover');
            }

            uploadArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                input.files = files;
                
                // Trigger change event
                const event = new Event('change');
                input.dispatchEvent(event);
            }
        }

        setupDragDrop(coverUploadArea, coverInput, coverPreview);
        setupDragDrop(bookUploadArea, bookInput, bookPreview);

        // Form validation
        document.getElementById('bookForm').addEventListener('submit', function(e) {
            let valid = true;

            // Validate required fields
            const requiredFields = this.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                    
                    // Special handling for Select2
                    if ($(field).hasClass('select2-search')) {
                        validateSelect2(field, field.id + '-feedback');
                    }
                } else {
                    field.classList.remove('is-invalid');
                    
                    // Special handling for Select2
                    if ($(field).hasClass('select2-search')) {
                        $(field).removeClass('is-invalid');
                        document.getElementById(field.id + '-feedback').style.display = 'none';
                    }
                }
            });

            if (!valid) {
                e.preventDefault();
                // Scroll to first invalid field
                const firstInvalid = this.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
                
                // Show alert for Select2 validation
                if (!$('#penulis_id').val()) {
                    $('#penulis_id').addClass('is-invalid');
                    document.getElementById('penulis-feedback').style.display = 'block';
                }
                if (!$('#kategori_id').val()) {
                    $('#kategori_id').addClass('is-invalid');
                    document.getElementById('kategori-feedback').style.display = 'block';
                }
            }
        });

        // Real-time validation for Select2
        $('#penulis_id').on('select2:select', function() {
            validateSelect2(this, 'penulis-feedback');
        });

        $('#kategori_id').on('select2:select', function() {
            validateSelect2(this, 'kategori-feedback');
        });

        // Clear validation on input
        document.getElementById('judul').addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    </script>
</body>
</html>