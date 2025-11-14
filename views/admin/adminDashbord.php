<?php
require_once '../config/database.php';
session_start();

// Cek apakah admin sudah login
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.php");
    exit();
}

// Ambil statistik
$stats = [];
try {
    // Total Users
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM user");
    $stmt->execute();
    $stats['total_users'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total Books
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM buku");
    $stmt->execute();
    $stats['total_books'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total Authors
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM penulis");
    $stmt->execute();
    $stats['total_authors'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total Categories
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM kategori");
    $stmt->execute();
    $stats['total_categories'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total Comments
    $stmt = $connection->prepare("SELECT COUNT(*) as total FROM comments");
    $stmt->execute();
    $stats['total_comments'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Recent Books
    $stmt = $connection->prepare("
        SELECT b.*, p.name as penulis_name, k.kategori 
        FROM buku b 
        LEFT JOIN penulis p ON b.penulis_id = p.penulis_id 
        LEFT JOIN kategori k ON b.kategori_id = k.kategori_id 
        ORDER BY b.dibuat_pada DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Recent Users
    $stmt = $connection->prepare("SELECT * FROM user ORDER BY dibuat_pada DESC LIMIT 5");
    $stmt->execute();
    $recent_users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

} catch (Exception $e) {
    $error = $e->getMessage();
}

// Logout
if(isset($_POST['logout'])) {
    session_destroy();
    header("Location: adminLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Manajemen Buku</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: calc(100vh - 80px);
        }

        .sidebar {
            background: white;
            padding: 2rem 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar nav ul {
            list-style: none;
        }

        .sidebar nav a {
            display: block;
            padding: 1rem 2rem;
            color: #333;
            text-decoration: none;
            transition: background 0.3s;
            border-left: 4px solid transparent;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: #f8f9fa;
            border-left-color: #667eea;
            color: #667eea;
        }

        .main-content {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card h3 {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        .section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section h2 {
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #5a6fd8;
        }

        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Dashboard Admin - Sistem Manajemen Buku</h1>
        <div class="user-info">
            <span>Halo, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="#" class="active">Dashboard</a></li>
                    <li><a href="manage_users.php">Manajemen User</a></li>
                    <li><a href="manage_books.php">Manajemen Buku</a></li>
                    <li><a href="manage_authors.php">Manajemen Penulis</a></li>
                    <li><a href="manage_categories.php">Manajemen Kategori</a></li>
                    <li><a href="manage_comments.php">Manajemen Komentar</a></li>
                    <li><a href="manage_favorites.php">Favorit & Simpan</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <!-- Statistik -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <div class="number"><?php echo $stats['total_users']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Buku</h3>
                    <div class="number"><?php echo $stats['total_books']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Penulis</h3>
                    <div class="number"><?php echo $stats['total_authors']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Kategori</h3>
                    <div class="number"><?php echo $stats['total_categories']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Komentar</h3>
                    <div class="number"><?php echo $stats['total_comments']; ?></div>
                </div>
            </div>

            <!-- Buku Terbaru -->
            <div class="section">
                <h2>Buku Terbaru</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Tanggal Terbit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_books as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['judul']); ?></td>
                            <td><?php echo htmlspecialchars($book['penulis_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($book['kategori'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($book['terbit_pada'] ?? '-'); ?></td>
                            <td class="action-buttons">
                                <a href="edit_book.php?id=<?php echo $book['buku_id']; ?>" class="btn btn-small">Edit</a>
                                <a href="delete_book.php?id=<?php echo $book['buku_id']; ?>" class="btn btn-small" onclick="return confirm('Hapus buku ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- User Terbaru -->
            <div class="section">
                <h2>User Terbaru</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['dibuat_pada']); ?></td>
                            <td class="action-buttons">
                                <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-small">Edit</a>
                                <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-small" onclick="return confirm('Hapus user ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Quick Actions -->
            <div class="section">
                <h2>Quick Actions</h2>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="add_book.php" class="btn">Tambah Buku Baru</a>
                    <a href="add_user.php" class="btn">Tambah User Baru</a>
                    <a href="add_author.php" class="btn">Tambah Penulis Baru</a>
                    <a href="add_category.php" class="btn">Tambah Kategori Baru</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>