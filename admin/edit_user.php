<?php
require_once '../config/database.php';
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

$user_id = $_GET['id'];
$user = null;

// Get user data
$stmt = $connection->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$user) {
    header("Location: manage_users.php");
    exit();
}

if(isset($_POST['update_user'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    // Check if username already exists (excluding current user)
    $check_stmt = $connection->prepare("SELECT user_id FROM user WHERE username = ? AND user_id != ?");
    $check_stmt->bind_param("si", $username, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if($check_stmt->num_rows > 0) {
        $error = "Username sudah digunakan oleh user lain!";
    } else {
        // Check if email already exists (excluding current user)
        $check_stmt2 = $connection->prepare("SELECT user_id FROM user WHERE email = ? AND user_id != ?");
        $check_stmt2->bind_param("si", $email, $user_id);
        $check_stmt2->execute();
        $check_stmt2->store_result();
        
        if($check_stmt2->num_rows > 0) {
            $error = "Email sudah digunakan oleh user lain!";
        } else {
            // Jika password diisi, update password juga
            if(!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $connection->prepare("UPDATE user SET name = ?, username = ?, email = ?, password = ? WHERE user_id = ?");
                $stmt->bind_param("ssssi", $name, $username, $email, $password, $user_id);
            } else {
                $stmt = $connection->prepare("UPDATE user SET name = ?, username = ?, email = ? WHERE user_id = ?");
                $stmt->bind_param("sssi", $name, $username, $email, $user_id);
            }

            if($stmt->execute()) {
                $_SESSION['message'] = "User berhasil diupdate!";
                header("Location: manage_users.php");
                exit();
            } else {
                $error = "Gagal mengupdate user! Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt2->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Sistem Manajemen Perpustakaan</title>
    
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
        
        /* Password Toggle */
        .password-toggle {
            position: relative;
        }
        
        .password-toggle .form-control {
            padding-right: 3rem;
        }
        
        .password-toggle-btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #6b7280;
            cursor: pointer;
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
        
        /* User Avatar */
        .user-avatar-preview {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
        
        /* Password Strength */
        .password-strength {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak {
            background: #dc2626;
            width: 25%;
        }
        
        .strength-fair {
            background: #f59e0b;
            width: 50%;
        }
        
        .strength-good {
            background: #10b981;
            width: 75%;
        }
        
        .strength-strong {
            background: #059669;
            width: 100%;
        }
        
        /* Character Counter */
        .char-counter {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: right;
            margin-top: 0.25rem;
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
                <a href="manage_users.php" class="nav-link active">
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
                                        <i class="bi bi-person-gear text-primary me-2"></i>
                                        Edit User
                                    </h3>
                                    <p class="text-muted mb-0">Edit informasi user <?php echo htmlspecialchars($user['name']); ?></p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="manage_users.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Kembali ke Manajemen User
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
                                <i class="bi bi-person-badge me-2"></i>
                                Informasi User
                            </h5>
                        </div>
                        <div class="form-body">
                            <form method="POST" id="userForm">
                                <div class="row g-3">
                                    <!-- Nama Lengkap -->
                                    <div class="col-12">
                                        <label for="name" class="form-label required">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($user['name']); ?>" required 
                                               placeholder="Masukkan nama lengkap user"
                                               maxlength="100">
                                        <div class="char-counter">
                                            <span id="nameCounter"><?php echo strlen($user['name']); ?></span>/100 karakter
                                        </div>
                                    </div>

                                    <!-- Username -->
                                    <div class="col-md-6">
                                        <label for="username" class="form-label required">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($user['username']); ?>" required 
                                               placeholder="Masukkan username"
                                               maxlength="50">
                                        <div class="form-text">Username harus unik dan tidak mengandung spasi</div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label required">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>" required 
                                               placeholder="user@example.com"
                                               maxlength="100">
                                    </div>

                                    <!-- Password -->
                                    <div class="col-12">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="password-toggle">
                                            <input type="password" class="form-control" id="password" name="password" 
                                                   placeholder="Kosongkan jika tidak ingin mengubah password" 
                                                   minlength="6">
                                            <button type="button" class="password-toggle-btn" id="togglePassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <div class="password-strength">
                                            <div class="password-strength-bar" id="passwordStrength"></div>
                                        </div>
                                        <div class="form-text">Password minimal 6 karakter. Kosongkan jika tidak ingin mengubah.</div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="manage_users.php" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle me-2"></i>
                                                Batal
                                            </a>
                                            <button type="submit" name="update_user" class="btn btn-primary">
                                                <i class="bi bi-check-circle me-2"></i>
                                                Update User
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
                    <!-- User Preview Card -->
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.2s;">
                        <div class="form-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-eye me-2"></i>
                                Preview User
                            </h5>
                        </div>
                        <div class="form-body text-center">
                            <div class="user-avatar-preview" id="avatarPreview">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                            <h6 id="previewName" class="mb-1"><?php echo htmlspecialchars($user['name']); ?></h6>
                            <small id="previewUsername" class="text-muted d-block mb-2">@<?php echo htmlspecialchars($user['username']); ?></small>
                            <p id="previewEmail" class="small text-muted mb-0">
                                <?php echo htmlspecialchars($user['email']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- User Info Card -->
                    <div class="card form-card animate-fade-in-up" style="animation-delay: 0.3s;">
                        <div class="form-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Informasi User
                            </h5>
                        </div>
                        <div class="form-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">User ID</span>
                                <span class="badge bg-primary">#<?php echo $user['user_id']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Tanggal Daftar</span>
                                <small class="text-muted">
                                    <?php echo date('d M Y', strtotime($user['dibuat_pada'])); ?>
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
                                Tips Edit User
                            </h5>
                        </div>
                        <div class="form-body">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">Data Valid</small>
                                    <p class="mb-0 text-muted small">Pastikan data yang diinput valid dan akurat</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">Username Unik</small>
                                    <p class="mb-0 text-muted small">Username harus berbeda dengan user lain</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <i class="bi bi-check-circle text-success me-2 mt-1"></i>
                                <div>
                                    <small class="fw-semibold">Password Aman</small>
                                    <p class="mb-0 text-muted small">Gunakan password yang kuat jika diubah</p>
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

        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });

        // Password strength indicator
        password.addEventListener('input', function() {
            const strengthBar = document.getElementById('passwordStrength');
            const value = this.value;
            let strength = 0;

            // Reset classes
            strengthBar.className = 'password-strength-bar';

            if (value.length >= 6) strength++;
            if (value.length >= 8) strength++;
            if (/[A-Z]/.test(value)) strength++;
            if (/[0-9]/.test(value)) strength++;
            if (/[^A-Za-z0-9]/.test(value)) strength++;

            // Update strength bar
            if (value.length > 0) {
                if (strength <= 2) {
                    strengthBar.classList.add('strength-weak');
                } else if (strength === 3) {
                    strengthBar.classList.add('strength-fair');
                } else if (strength === 4) {
                    strengthBar.classList.add('strength-good');
                } else {
                    strengthBar.classList.add('strength-strong');
                }
            }
        });

        // Character counter for name
        const nameInput = document.getElementById('name');
        const nameCounter = document.getElementById('nameCounter');

        nameInput.addEventListener('input', function() {
            const count = this.value.length;
            nameCounter.textContent = count;
            
            // Update preview
            updatePreview();
        });

        // Update preview function
        function updatePreview() {
            const name = nameInput.value;
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            
            // Update avatar with first letter
            const firstLetter = name.charAt(0).toUpperCase();
            document.getElementById('avatarPreview').textContent = firstLetter;
            
            // Update other preview elements
            document.getElementById('previewName').textContent = name;
            document.getElementById('previewUsername').textContent = '@' + username;
            document.getElementById('previewEmail').textContent = email;
        }

        // Real-time validation
        document.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });

        // Initialize preview
        updatePreview();
    </script>
</body>
</html>