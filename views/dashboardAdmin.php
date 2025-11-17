<?php
// views/admin/dashboardAdmin.php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: adminLogin.php');
    exit();
}

require_once '../../models/bukuModels.php';
require_once '../../models/userModels.php';
require_once '../../models/kontakModels.php';
require_once '../../models/kategoriModels.php';

$bukuModel = new BukuModel();
$userModel = new UserModel();
$kontakModel = new KontakModel();
$kategoriModel = new KategoriModel();

// Get statistics
$totalBooks = $bukuModel->getBookCount();
$totalUsers = $userModel->getUserCount();
$totalMessages = $kontakModel->getMessageCount();
$unreadMessages = $kontakModel->getUnreadCount();
$recentBooks = $bukuModel->getRecentBooks(5);
$recentUsers = $userModel->getRecentUsers(5);
$recentMessages = $kontakModel->getRecentMessages(5);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Azizi.io</title>
    <link rel="stylesheet" href="../../public/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../../public/assets/LogoAziziTeks.png" alt="Azizi.io" class="logo">
                <h2>Admin Panel</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboardAdmin.php" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="manage_books.php" class="nav-item">
                    <i class="fas fa-book"></i>
                    <span>Kelola Buku</span>
                </a>
                <a href="manage_users.php" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Kelola User</span>
                </a>
                <a href="manage_categories.php" class="nav-item">
                    <i class="fas fa-tags"></i>
                    <span>Kelola Kategori</span>
                </a>
                <a href="manage_authors.php" class="nav-item">
                    <i class="fas fa-user-edit"></i>
                    <span>Kelola Penulis</span>
                </a>
                <a href="manage_comments.php" class="nav-item">
                    <i class="fas fa-comments"></i>
                    <span>Kelola Komentar</span>
                </a>
                <a href="manage_favorites.php" class="nav-item">
                    <i class="fas fa-heart"></i>
                    <span>Favorit User</span>
                </a>
                <a href="manage_contacts.php" class="nav-item">
                    <i class="fas fa-envelope"></i>
                    <span>Pesan Masuk</span>
                    <?php if ($unreadMessages > 0): ?>
                    <span class="nav-badge"><?= $unreadMessages ?></span>
                    <?php endif; ?>
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="../../views/beranda.php" class="nav-item">
                    <i class="fas fa-home"></i>
                    <span>Kembali ke Site</span>
                </a>
                <a href="logout.php" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <h1 class="page-title">Dashboard</h1>
                    <p class="welcome-text">Selamat datang, <?= htmlspecialchars($_SESSION['user']['nama_lengkap']) ?>!</p>
                </div>
                <div class="header-right">
                    <div class="user-menu">
                        <img src="../../uploads/users/<?= $_SESSION['user']['foto'] ?? 'default.png' ?>" 
                             alt="Profile" class="user-avatar">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['user']['nama_lengkap']) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </header>

            <!-- Statistics Cards -->
            <section class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon book">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-number"><?= $totalBooks ?></h3>
                        <p class="stat-label">Total Buku</p>
                    </div>
                    <a href="manage_books.php" class="stat-link">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon user">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-number"><?= $totalUsers ?></h3>
                        <p class="stat-label">Total User</p>
                    </div>
                    <a href="manage_users.php" class="stat-link">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon message">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-number"><?= $totalMessages ?></h3>
                        <p class="stat-label">Pesan Masuk</p>
                    </div>
                    <a href="manage_contacts.php" class="stat-link">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="stat-card">
                    <div class="stat-icon category">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-number"><?= $kategoriModel->getCategoryCount() ?></h3>
                        <p class="stat-label">Kategori</p>
                    </div>
                    <a href="manage_categories.php" class="stat-link">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </section>

            <!-- Charts and Recent Activity -->
            <div class="dashboard-content">
                <!-- Recent Books -->
                <div class="content-column">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Buku Terbaru</h3>
                            <a href="manage_books.php" class="card-link">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="recent-list">
                                <?php foreach ($recentBooks as $book): ?>
                                <div class="recent-item">
                                    <img src="../../uploads/covers/<?= $book->cover_path ?>" 
                                         alt="<?= htmlspecialchars($book->judul) ?>" 
                                         class="item-avatar">
                                    <div class="item-info">
                                        <h4 class="item-title"><?= htmlspecialchars($book->judul) ?></h4>
                                        <p class="item-subtitle"><?= htmlspecialchars($book->nama_penulis) ?></p>
                                        <span class="item-meta"><?= date('d M Y', strtotime($book->created_at)) ?></span>
                                    </div>
                                    <div class="item-actions">
                                        <a href="edit_book.php?id=<?= $book->id ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Messages -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Pesan Terbaru</h3>
                            <a href="manage_contacts.php" class="card-link">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="recent-list">
                                <?php foreach ($recentMessages as $message): ?>
                                <div class="recent-item">
                                    <div class="item-avatar text-avatar <?= $message->status == 'unread' ? 'unread' : '' ?>">
                                        <?= strtoupper(substr($message->nama, 0, 1)) ?>
                                    </div>
                                    <div class="item-info">
                                        <h4 class="item-title"><?= htmlspecialchars($message->subject) ?></h4>
                                        <p class="item-subtitle"><?= htmlspecialchars($message->nama) ?></p>
                                        <span class="item-meta"><?= date('d M Y', strtotime($message->created_at)) ?></span>
                                    </div>
                                    <div class="item-actions">
                                        <a href="view_message.php?id=<?= $message->id ?>" class="btn-view">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users and Quick Actions -->
                <div class="content-column">
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">User Terbaru</h3>
                            <a href="manage_users.php" class="card-link">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="recent-list">
                                <?php foreach ($recentUsers as $user): ?>
                                <div class="recent-item">
                                    <img src="../../uploads/users/<?= $user->foto ?? 'default.png' ?>" 
                                         alt="<?= htmlspecialchars($user->nama_lengkap) ?>" 
                                         class="item-avatar">
                                    <div class="item-info">
                                        <h4 class="item-title"><?= htmlspecialchars($user->nama_lengkap) ?></h4>
                                        <p class="item-subtitle"><?= htmlspecialchars($user->email) ?></p>
                                        <span class="item-meta role-<?= $user->role ?>"><?= ucfirst($user->role) ?></span>
                                    </div>
                                    <div class="item-actions">
                                        <a href="edit_user.php?id=<?= $user->id ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="add_book.php" class="quick-action">
                                    <div class="action-icon">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <span>Tambah Buku</span>
                                </a>
                                <a href="add_category.php" class="quick-action">
                                    <div class="action-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <span>Tambah Kategori</span>
                                </a>
                                <a href="add_author.php" class="quick-action">
                                    <div class="action-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <span>Tambah Penulis</span>
                                </a>
                                <a href="manage_contacts.php" class="quick-action">
                                    <div class="action-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <span>Lihat Pesan</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">System Info</h3>
                        </div>
                        <div class="card-body">
                            <div class="system-info">
                                <div class="info-item">
                                    <span class="info-label">PHP Version:</span>
                                    <span class="info-value"><?= phpversion() ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Server Time:</span>
                                    <span class="info-value"><?= date('Y-m-d H:i:s') ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Memory Usage:</span>
                                    <span class="info-value"><?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../public/js/admin.js"></script>
</body>
</html>