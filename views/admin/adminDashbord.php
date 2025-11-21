<?php
// Mencegah akses langsung ke file include
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Akses tidak diizinkan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard Azizi.io</title>
    <!-- Memuat Bootstrap 5.3 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat Ikon Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- CSS Kustom untuk Gaya Dashboard -->
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #4338ca; /* Biru/Ungu utama */
            --card-purple: #6d28d9; /* Ungu gelap */
            --card-blue: #2563eb; /* Biru terang */
            --light-bg: #f8fafc;
            --active-bg: #eef2ff; /* Latar belakang item aktif Sidebar */
        }
        
        body {
            font-family: 'Inter', sans-serif; /* Menggunakan font yang modern */
            background-color: var(--light-bg);
            padding-top: 56px; /* Memberi ruang untuk Topbar */
        }
        
        /* Gaya Sidebar Desktop (Layout Permanen) */
        .sidebar-desktop {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 56px; /* Sesuaikan dengan tinggi Topbar */
            z-index: 100;
            background-color: #fff;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }
        
        /* Konten Utama Desktop */
        .main-content-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s;
        }

        /* Gaya Topbar (Fixed) */
        .topbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 101;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        /* Responsive: Untuk layar kecil (mobile) */
        @media (max-width: 991.98px) {
            .sidebar-desktop {
                display: none; /* Sembunyikan sidebar permanen di mobile */
            }
            .main-content-wrapper {
                margin-left: 0; /* Konten full-width */
            }
        }

        /* Custom Scrollbar (untuk sidebar) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #ced4da;
            border-radius: 10px;
        }

        /* Gaya Kustom untuk Navigasi */
        .nav-link.active-link {
            background-color: var(--active-bg);
            color: var(--primary-color) !important;
            font-weight: 600;
        }
        .nav-link.active-link .fa-solid {
             color: var(--primary-color) !important;
        }
        .nav-link {
            color: #6c757d;
        }
        .nav-link:hover {
            background-color: #f8f9fa;
            color: #495057;
        }
    </style>
</head>
<body>

<!-- TOPBAR (Header Navigasi Atas) -->
<nav class="navbar navbar-expand-lg navbar-light bg-white topbar">
    <div class="container-fluid px-4">
        
        <!-- Toggle Menu (Hanya terlihat di Mobile) -->
        <button class="btn btn-link text-dark d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        
        <!-- Nama Dashboard (Hanya terlihat di Mobile) -->
        <a class="navbar-brand d-lg-none text-primary fw-bold" href="#">Admin Panel</a>

        <!-- Search Bar Desktop -->
        <div class="d-none d-lg-flex flex-grow-1 me-auto ms-5" style="max-width: 500px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Cari data, menu, atau laporan...">
            </div>
        </div>

        <!-- Ikon dan Profil Kanan -->
        <div class="d-flex align-items-center">
            <!-- Notifikasi -->
            <button class="btn btn-link text-secondary me-2">
                <i class="fas fa-bell fa-lg"></i>
            </button>
            
            <!-- Profil User -->
            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-none d-sm-inline me-2 text-dark fw-medium">Admin Azizi</span>
                    <div class="rounded-circle bg-secondary-subtle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <span class="text-primary fw-bold">A</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Pengaturan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="adminLogin.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- SIDEBAR DESKTOP (Permanen di Layar Besar) -->
<div class="sidebar-desktop d-none d-lg-block shadow-sm custom-scrollbar">
    <div class="p-4 border-bottom">
        <h1 class="h5 fw-bold text-primary">Admin Dashboard</h1>
    </div>
    
    <nav class="nav flex-column p-3">
        <!-- Dashboard -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase small">Dashboard</h6>
        <a class="nav-link rounded py-2 px-3 mb-1 active-link" href="adminDashboard.php">
            <i class="fas fa-tachometer-alt fa-fw me-3"></i>
            Default
        </a>
        
        <!-- Pages -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase small">Pages</h6>
        <a class="nav-link rounded py-2 px-3 mb-1" href="manage_users.php">
            <i class="fas fa-users fa-fw me-3"></i>
            Manajemen User
        </a>
        <a class="nav-link rounded py-2 px-3 mb-1" href="manage_books.php">
            <i class="fas fa-book fa-fw me-3"></i>
            Manajemen Buku
        </a>
        <a class="nav-link rounded py-2 px-3 mb-1" href="manage_categories.php">
            <i class="fas fa-folder-open fa-fw me-3"></i>
            Manajemen Kategori
        </a>
        
        <!-- Utilities -->
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase small">Utilities</h6>
        <a class="nav-link rounded py-2 px-3 mb-1" href="#">
            <i class="fas fa-cogs fa-fw me-3"></i>
            Pengaturan
        </a>
        <a class="nav-link rounded py-2 px-3 mb-1" href="adminLogin.php">
            <i class="fas fa-lock fa-fw me-3"></i>
            Authentication (Login)
        </a>
    </nav>
</div>

<!-- SIDEBAR MOBILE (Offcanvas Bootstrap) -->
<div class="offcanvas offcanvas-start bg-white" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel" style="width: var(--sidebar-width);">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title text-primary fw-bold" id="sidebarOffcanvasLabel">Admin Dashboard</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <nav class="nav flex-column p-3">
            <!-- Konten Navigasi Sama dengan Sidebar Desktop -->
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase small">Dashboard</h6>
            <a class="nav-link rounded py-2 px-3 mb-1 active-link" href="adminDashboard.php">
                <i class="fas fa-tachometer-alt fa-fw me-3"></i>
                Default
            </a>
            
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase small">Pages</h6>
            <a class="nav-link rounded py-2 px-3 mb-1" href="manage_users.php">
                <i class="fas fa-users fa-fw me-3"></i>
                Manajemen User
            </a>
            <a class="nav-link rounded py-2 px-3 mb-1" href="manage_books.php">
                <i class="fas fa-book fa-fw me-3"></i>
                Manajemen Buku
            </a>
            <a class="nav-link rounded py-2 px-3 mb-1" href="manage_categories.php">
                <i class="fas fa-folder-open fa-fw me-3"></i>
                Manajemen Kategori
            </a>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase small">Utilities</h6>
            <a class="nav-link rounded py-2 px-3 mb-1" href="#">
                <i class="fas fa-cogs fa-fw me-3"></i>
                Pengaturan
            </a>
            <a class="nav-link rounded py-2 px-3 mb-1" href="adminLogin.php">
                <i class="fas fa-lock fa-fw me-3"></i>
                Authentication (Login)
            </a>
        </nav>
    </div>
</div>

<!-- CONTAINER UTAMA -->
<div class="main-content-wrapper">
    <!-- Konten halaman akan dimasukkan di sini -->
    <main class="container-fluid py-4">