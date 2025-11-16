<?php
require_once '../config/database.php';
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

$buku_id = $_GET['id'];
$book = null;

// Get book data
$stmt = $connection->prepare("
    SELECT b.*, p.name as penulis_name, k.kategori as kategori_name 
    FROM buku b 
    LEFT JOIN penulis p ON b.penulis_id = p.penulis_id 
    LEFT JOIN kategori k ON b.kategori_id = k.kategori_id 
    WHERE b.buku_id = ?
");
$stmt->bind_param("i", $buku_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$book) {
    header("Location: manage_books.php");
    exit();
}

// Get authors and categories for dropdown
$authors = $connection->query("SELECT * FROM penulis ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$categories = $connection->query("SELECT * FROM kategori ORDER BY kategori")->fetch_all(MYSQLI_ASSOC);

if(isset($_POST['update_book'])) {
    $judul = trim($_POST['judul']);
    $penulis_id = $_POST['penulis_id'];
    $kategori_id = $_POST['kategori_id'];
    $penerbit = trim($_POST['penerbit']);
    $terbit_pada = $_POST['terbit_pada'];
    $deskripsi = trim($_POST['deskripsi']);

    // Handle file uploads
    $cover_name = $book['cover'];
    $file_buku_name = $book['file_buku'];
    $upload_errors = [];

    // UPLOAD PATHS
    $base_dir = "../uploads/";
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
            if (move_uploaded_file($_FILES["cover"]["tmp_name"], $target_file)) {
                // Delete old cover if exists
                if(!empty($book['cover']) && file_exists($cover_dir . $book['cover'])) {
                    unlink($cover_dir . $book['cover']);
                }
            } else {
                $upload_errors[] = "Terjadi error saat upload cover.";
                $cover_name = $book['cover'];
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
            if (move_uploaded_file($_FILES["file_buku"]["tmp_name"], $target_file)) {
                // Delete old book file if exists
                if(!empty($book['file_buku']) && file_exists($book_dir . $book['file_buku'])) {
                    unlink($book_dir . $book['file_buku']);
                }
            } else {
                $upload_errors[] = "Terjadi error saat upload file buku.";
                $file_buku_name = $book['file_buku'];
            }
        }
    }

    // Jika ada error upload, tampilkan
    if(!empty($upload_errors)) {
        $error = implode("<br>", $upload_errors);
    } else {
        // Update database
        $stmt = $connection->prepare("UPDATE buku SET judul = ?, penulis_id = ?, kategori_id = ?, penerbit = ?, terbit_pada = ?, deskripsi = ?, cover = ?, file_buku = ? WHERE buku_id = ?");
        $stmt->bind_param("siisssssi", $judul, $penulis_id, $kategori_id, $penerbit, $terbit_pada, $deskripsi, $cover_name, $file_buku_name, $buku_id);

        if($stmt->execute()) {
            $_SESSION['message'] = "Buku berhasil diupdate!";
            header("Location: manage_books.php");
            exit();
        } else {
            $error = "Gagal mengupdate buku! Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Sistem Manajemen Perpustakaan</title>
    
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
            margin-top: 1rem;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        /* Book Preview */
        .book-preview {
            background: #f8fafc;
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
        }
        
        .book-cover-preview {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 0 auto 1rem;
        }
        
        .book-cover-placeholder {
            width: 120px;
            height: 180px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            margin: 0 auto 1rem;
        }
        
        /* Character Counter */
        .char-counter {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: right;
            margin-top: 0.25rem;
        }
        
        .char-counter.warning {
            color: #f59e0b;
        }
        
        .char-counter.danger {
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
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
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
                                        <i class="bi bi-journal-text text-primary me-2"></i>
                                        Edit Buku
                                    </h3>
                                    <p class="text-muted mb-0">Edit informasi buku <?php echo htmlspecialchars($book['judul']); ?></p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="manage_books.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Kembali ke Manajemen Buku
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
                                               value="<?php echo htmlspecialchars($book['judul']); ?>"
                                               placeholder="Masukkan judul buku"
                                               maxlength="200">
                                        <div class="char-counter">
                                            <span id="judulCounter"><?php echo strlen($book['judul']); ?></span>/200 karakter
                                        </div>
                                    </div>

                                    <!-- Penulis dan Kategori -->
                                    <div class="col-md-6">
                                        <label for="penulis_id" class="form-label required">Penulis</label>
                                        <select class="form-select select2-search" id="penulis_id" name="penulis_id" required>
                                            <option value="">Pilih Penulis</option>
                                            <?php foreach($authors as $author): ?>
                                                <option value="<?php echo $author['penulis_id']; ?>" 
                                                    <?php echo $author['penulis_id'] == $book['penulis_id'] ? 'selected' : ''; ?>>
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
                                                <option value="<?php echo $category['kategori_id']; ?>" 
                                                    <?php echo $category['kategori_id'] == $book['kategori_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['kategori']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Penerbit dan Tanggal Terbit -->
                                    <div class="col-md-6">
                                        <label for="penerbit" class="form-label">Penerbit</label>
                                        <input type="text" class="form-control" id="penerbit" name="penerbit" 
                                               value="<?php echo htmlspecialchars($book['penerbit']); ?>"
                                               placeholder="Nama penerbit"
                                               maxlength="100">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="terbit_pada" class="form-label">Tanggal Terbit</label>
                                        <input type="date" class="form-control" id="terbit_pada" name="terbit_pada" 
                                               value="<?php echo $book['terbit_pada']; ?>">
                                    </div>

                                    <!-- Deskripsi -->
                                    <div class="col-12">
                                        <label for="deskripsi" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" 
                                                  placeholder="Deskripsi singkat tentang buku..."
                                                  maxlength="1000"><?php echo htmlspecialchars($book['deskripsi']); ?></textarea>
                                        <div class="char-counter">
                                            <span id="deskripsiCounter"><?php echo strlen($book['deskripsi']); ?></span>/1000 karakter
                                        </div>
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
                                        <?php if(!empty($book['cover'])): ?>
                                        <div class="preview-container" id="coverPreview">
                                            <img src="../uploads/covers/<?php echo htmlspecialchars($book['cover']); ?>" alt="Preview Cover" class="preview-image">
                                            <div class="mt-2">
                                                <small class="text-muted">Cover saat ini</small>
                                            </div>
                                        </div>
                                        <?php endif; ?>
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
                                        <?php if(!empty($book['file_buku'])): ?>
                                        <div class="preview-container" id="bookPreview">
                                            <i class="bi bi-file-earmark-pdf fs-1 text-primary"></i>
                                            <div class="file-name mt-2 fw-semibold"><?php echo htmlspecialchars($book['file_buku']); ?></div>
                                            <div class="mt-2">
                                                <small class="text-muted">File saat ini</small>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="manage_books.php" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle me-2"></i>
                                                Batal
                                            </a>
                                            <button type="submit" name="update_book" class="btn btn-primary">
                                                <i class="bi bi-check-circle me-2"></i>
                                                Update Buku
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
                    <!-- Preview Card -->
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="form-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-eye me-2"></i>
                                Preview Buku
                            </h5>
                        </div>
                        <div class="form-body">
                            <div class="book-preview">
                                <?php if(!empty($book['cover'])): ?>
                                    <img src="../uploads/covers/<?php echo htmlspecialchars($book['cover']); ?>" alt="Cover Buku" class="book-cover-preview">
                                <?php else: ?>
                                    <div class="book-cover-placeholder">
                                        <i class="bi bi-book"></i>
                                    </div>
                                <?php endif; ?>
                                <h6 id="previewJudul" class="mb-1"><?php echo htmlspecialchars($book['judul']); ?></h6>
                                <small id="previewPenulis" class="text-muted d-block mb-2"><?php echo htmlspecialchars($book['penulis_name']); ?></small>
                                <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($book['kategori_name']); ?></span>
                                <p id="previewDeskripsi" class="small text-muted mb-0">
                                    <?php echo htmlspecialchars($book['deskripsi'] ?: 'Deskripsi buku akan muncul di sini...'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Book Info Card -->
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="form-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Informasi Buku
                            </h5>
                        </div>
                        <div class="form-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Buku ID</span>
                                <span class="badge bg-primary">#<?php echo $book['buku_id']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Tanggal Ditambah</span>
                                <small class="text-muted">
                                    <?php echo date('d M Y', strtotime($book['dibuat_pada'])); ?>
                                </small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status</span>
                                <span class="badge bg-success">Aktif</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tips Card -->
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.4s;">
                        <div class="form-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightbulb me-2"></i>
                                Tips Edit Buku
                            </h5>
                        </div>
                        <div class="form-body">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">Data Lengkap</small>
                                    <p class="mb-0 text-muted small">Isi semua informasi yang diperlukan</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">Cover Berkualitas</small>
                                    <p class="mb-0 text-muted small">Gunakan gambar dengan resolusi yang baik</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">File PDF Valid</small>
                                    <p class="mb-0 text-muted small">Pastikan file PDF dapat dibaca dengan baik</p>
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
        });

        // Character counters
        const judulInput = document.getElementById('judul');
        const deskripsiInput = document.getElementById('deskripsi');
        const judulCounter = document.getElementById('judulCounter');
        const deskripsiCounter = document.getElementById('deskripsiCounter');

        if (judulInput && judulCounter) {
            judulInput.addEventListener('input', function() {
                const count = this.value.length;
                judulCounter.textContent = count;
                updatePreview();
            });
        }

        if (deskripsiInput && deskripsiCounter) {
            deskripsiInput.addEventListener('input', function() {
                const count = this.value.length;
                deskripsiCounter.textContent = count;
                
                // Update character counter color
                if (count > 900) {
                    deskripsiCounter.classList.add('danger');
                } else if (count > 800) {
                    deskripsiCounter.classList.add('warning');
                } else {
                    deskripsiCounter.classList.remove('warning', 'danger');
                }
                
                updatePreview();
            });
        }

        // File Upload Preview Functions
        const coverInput = document.getElementById('cover');
        const bookInput = document.getElementById('file_buku');
        const coverUploadArea = document.getElementById('coverUploadArea');
        const bookUploadArea = document.getElementById('bookUploadArea');

        // Cover preview
        if (coverInput) {
            coverInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            let preview = document.getElementById('coverPreview');
                            if (!preview) {
                                preview = document.createElement('div');
                                preview.className = 'preview-container';
                                preview.id = 'coverPreview';
                                coverInput.parentNode.parentNode.appendChild(preview);
                            }
                            preview.innerHTML = `
                                <img src="${e.target.result}" alt="Preview Cover" class="preview-image">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearCoverPreview()">
                                    <i class="bi bi-x-circle me-1"></i>Hapus
                                </button>
                            `;
                            coverUploadArea.style.display = 'none';
                        }
                        reader.readAsDataURL(file);
                    }
                }
            });
        }

        // Book file preview
        if (bookInput) {
            bookInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.type === 'application/pdf') {
                        let preview = document.getElementById('bookPreview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.className = 'preview-container';
                            preview.id = 'bookPreview';
                            bookInput.parentNode.parentNode.appendChild(preview);
                        }
                        preview.innerHTML = `
                            <i class="bi bi-file-earmark-pdf fs-1 text-primary"></i>
                            <div class="file-name mt-2 fw-semibold">${file.name}</div>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearBookPreview()">
                                <i class="bi bi-x-circle me-1"></i>Hapus
                            </button>
                        `;
                        bookUploadArea.style.display = 'none';
                    }
                }
            });
        }

        // Clear preview functions
        function clearCoverPreview() {
            coverInput.value = '';
            const preview = document.getElementById('coverPreview');
            if (preview) preview.remove();
            coverUploadArea.style.display = 'block';
        }

        function clearBookPreview() {
            bookInput.value = '';
            const preview = document.getElementById('bookPreview');
            if (preview) preview.remove();
            bookUploadArea.style.display = 'block';
        }

        // Update preview function
        function updatePreview() {
            const judul = judulInput ? judulInput.value : '<?php echo htmlspecialchars($book['judul']); ?>';
            const deskripsi = deskripsiInput ? deskripsiInput.value : '<?php echo htmlspecialchars($book['deskripsi']); ?>';
            const penulisSelect = document.getElementById('penulis_id');
            const kategoriSelect = document.getElementById('kategori_id');
            
            const penulisText = penulisSelect ? penulisSelect.options[penulisSelect.selectedIndex]?.text : '<?php echo htmlspecialchars($book['penulis_name']); ?>';
            const kategoriText = kategoriSelect ? kategoriSelect.options[kategoriSelect.selectedIndex]?.text : '<?php echo htmlspecialchars($book['kategori_name']); ?>';
            
            // Update preview elements
            if (document.getElementById('previewJudul')) {
                document.getElementById('previewJudul').textContent = judul;
            }
            if (document.getElementById('previewPenulis')) {
                document.getElementById('previewPenulis').textContent = penulisText;
            }
            if (document.getElementById('previewDeskripsi')) {
                document.getElementById('previewDeskripsi').textContent = deskripsi || 'Deskripsi buku akan muncul di sini...';
            }
        }

        // Initialize preview
        updatePreview();
    </script>
</body>
</html>