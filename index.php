<?php
session_start();

$base_url = '/Project-Praktikum-Pemograman-Web';

// Handle routing
$page = $_GET['page'] ?? 'beranda';
$kategori = $_GET['kategori'] ?? '';

// Handle logout SEBELUM output apapun
if ($page === 'logout') {
    // Hapus semua session data
    $_SESSION = array();
    
    // Hapus session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Hancurkan session
    session_destroy();
    
    // Redirect ke beranda
    header('Location: index.php');
    exit();
}

// Simulasi login (untuk testing)
// $_SESSION['is_logged_in'] = true;
// $_SESSION['user_id'] = 1;
// $_SESSION['username'] = 'john_doe';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azizi.io - Digital Book Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <!-- Header -->
    <?php
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
        include 'includes/headerUser.php';
    } else {
        include 'includes/headerGuest.php';
    }
    ?>

    <!-- Main Content -->
    <main>
        <?php
        // Routing berdasarkan parameter page
        switch($page) {
            case 'about':
                include 'views/about.php';
                break;
            case 'buku': 
                include 'views/buku.php';
                break;
            case 'contact':
                include 'views/contact.php';
                break;
            case 'login':
                include 'views/auth/login.php';
                break;
            case 'daftar':
                include 'views/auth/daftar.php';
                break;
            case 'dashboard':
                // Hanya bisa diakses jika sudah login
                if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
                    include 'views/dashboard.php';
                } else {
                    include 'views/auth/login.php';
                }
                break;
            case 'profil':
                // Hanya bisa diakses jika sudah login
                if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
                    include 'views/profil.php';
                } else {
                    include 'views/auth/login.php';
                }
                break;
            default:
                include 'views/beranda.php';
        }

        // Handle kategori buku
        if ($kategori) {
            include 'views/kategori.php';
        }
        ?>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>