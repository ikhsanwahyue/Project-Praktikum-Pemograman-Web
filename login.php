<?php 
// Pastikan sesi dimulai dan $connection tersedia di file database.php
include 'config/database.php';

// Asumsi: isLoggedIn() didefinisikan di file lain (misalnya functions.php)
if (function_exists('isLoggedIn') && isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Menggunakan prepared statement untuk keamanan
    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        // Asumsi: session_start() sudah dipanggil di awal
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Azizi.io</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Palet Warna Kustom (WAJIB ADA) */
        .bg-primary-dark { background-color: #192038; } 
        .bg-soft-blue { background-color: #6f76fd; }
        .bg-dark-accent { background-color: #2d3250; }
        .bg-highlight { background-color: #f6b17a; }
        .text-soft-blue { color: #6f76fd; }
        .text-highlight { color: #f6b17a; }
    </style>
</head>
<body class="bg-primary-dark text-white">
    <?php include 'components/header.php'; ?>
    
    <!-- Konten Utama Login Card -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full bg-dark-accent rounded-xl shadow-2xl p-8 border border-gray-700">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-highlight mb-2">AZIZI.IO</h1>
                <h2 class="text-xl font-semibold text-white">Masuk ke Akun Anda</h2>
                <p class="text-gray-400 text-sm mt-1">Akses ribuan e-book berkualitas tinggi.</p>
            </div>
            
            <?php if (isset($error)): ?>
                <!-- Notifikasi Error dengan tema gelap -->
                <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-5">
                    <label class="block text-gray-300 mb-2 text-sm font-medium" for="username">
                        <i class="fas fa-user-circle mr-2 text-soft-blue"></i> Username
                    </label>
                    <input type="text" name="username" id="username" required
                           class="w-full px-4 py-3 border border-gray-600 bg-primary-dark rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-highlight focus:border-highlight transition"
                           placeholder="Masukkan username Anda">
                </div>
                
                <div class="mb-8">
                    <label class="block text-gray-300 mb-2 text-sm font-medium" for="password">
                        <i class="fas fa-lock mr-2 text-soft-blue"></i> Password
                    </label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-3 border border-gray-600 bg-primary-dark rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-highlight focus:border-highlight transition"
                           placeholder="Rahasia, min 6 karakter">
                </div>
                
                <!-- Tombol Login -->
                <button type="submit" name="login" 
                        class="w-full bg-soft-blue text-white py-3 rounded-lg hover:bg-soft-blue/90 transition font-semibold text-lg shadow-lg shadow-soft-blue/30">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk Sekarang
                </button>
            </form>
            
            <!-- Link Register -->
            <div class="text-center mt-8">
                <p class="text-gray-400 text-sm">Belum punya akun? 
                    <a href="register.php" class="text-highlight hover:text-soft-blue font-medium transition">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
</body>
</html>