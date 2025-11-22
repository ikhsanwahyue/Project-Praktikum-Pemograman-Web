

<nav class="navbar navbar-expand-lg navbar-azizi shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img src="public/asset/LogoAziziTeks.png" alt="Logo Azizi.io" class="navbar-logo">
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAzizi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarAzizi">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="?page=beranda">
                        <i class="fas fa-home me-1"></i>Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="?page=about">
                        <i class="fas fa-info-circle me-1"></i>Tentang
                    </a>
                </li>

                <!-- Book Dropdown -->
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link nav-link-custom dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-book me-1"></i>Kategori Buku
                    </a>
                    <ul class="dropdown-menu dropdown-menu-azizi">
                        <li><a class="dropdown-item" href="?kategori=fiksi">📚 Fiksi</a></li>
                        <li><a class="dropdown-item" href="?kategori=nonfiksi">📖 Non-Fiksi</a></li>
                        <li><a class="dropdown-item" href="?kategori=puisi">✍️ Puisi</a></li>
                        <li><a class="dropdown-item" href="?kategori=esai">📝 Esai</a></li>
                        <li><a class="dropdown-item" href="?kategori=ilmiah">🔬 Ilmiah</a></li>
                    </ul>
                </li> -->

                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="?page=buku">
                        <i class="fas fa-envelope me-1"></i>Buku
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-link-custom" href="?page=contact">
                        <i class="fas fa-envelope me-1"></i>Kontak
                    </a>
                </li>
            </ul>

            <!-- User Dropdown -->
            <div class="dropdown user-dropdown">
                <a class="btn btn-user dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span class="user-name"><?php echo $_SESSION['user_name'] ?? 'User'; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-azizi dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="?page=dashboard">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="?page=profil">
                            <i class="fas fa-user-edit me-2"></i>Profil Saya
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="?page=favorit">
                            <i class="fas fa-heart me-2"></i>Buku Favorit
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-logout" href="index.php?page=logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>