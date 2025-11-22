<?php 
// Pastikan sesi dimulai dan $connection tersedia di file database.php
include 'config/database.php';

// Asumsi: isLoggedIn() didefinisikan di file lain
if (function_exists('isLoggedIn') && isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } else {
        // Cek apakah username atau email sudah ada
        $check_query = "SELECT * FROM user WHERE username = ? OR email = ?";
        $stmt = $connection->prepare($check_query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username atau email sudah digunakan!";
        } else {
            // Hash password dan simpan user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO user (name, username, email, password) VALUES (?, ?, ?, ?)";
            $stmt = $connection->prepare($insert_query);
            $stmt->bind_param("ssss", $name, $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan. Silakan coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Azizi.io</title>
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
    
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-xl w-full bg-dark-accent rounded-xl shadow-2xl p-10 border border-gray-700">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-highlight mb-2">Daftar Sekarang</h1>
                <h2 class="text-xl font-semibold text-white">Buat Akun Azizi.io Baru</h2>
                <p class="text-gray-400 text-sm mt-1">Bergabunglah dengan komunitas pembaca kami dan akses koleksi buku digital.</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="bg-green-800 border border-green-600 text-green-300 px-4 py-3 rounded-lg mb-6">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success; ?>
                    <a href="login.php" class="block mt-2 text-highlight hover:underline font-semibold">
                        <i class="fas fa-sign-in-alt mr-1"></i> Klik di sini untuk Login
                    </a>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                    <div class="mb-5">
                        <label class="block text-gray-300 mb-2 text-sm font-medium" for="name">
                            <i class="fas fa-id-badge mr-2 text-soft-blue"></i> Nama Lengkap
                        </label>
                        <input type="text" name="name" id="name" required
                               class="form-input"
                               placeholder="Nama sesuai KTP">
                    </div>
                    
                    <div class="mb-5">
                        <label class="block text-gray-300 mb-2 text-sm font-medium" for="username">
                            <i class="fas fa-user mr-2 text-soft-blue"></i> Username
                        </label>
                        <input type="text" name="username" id="username" required
                               class="form-input"
                               placeholder="Buat username unik">
                    </div>
                    
                    <div class="mb-5 md:col-span-2">
                        <label class="block text-gray-300 mb-2 text-sm font-medium" for="email">
                            <i class="fas fa-envelope mr-2 text-soft-blue"></i> Email
                        </label>
                        <input type="email" name="email" id="email" required
                               class="form-input"
                               placeholder="email@domain.com">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-300 mb-2 text-sm font-medium" for="password">
                            <i class="fas fa-lock mr-2 text-soft-blue"></i> Password
                        </label>
                        <input type="password" name="password" id="password" required
                               class="form-input"
                               placeholder="Minimal 6 karakter">
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-300 mb-2 text-sm font-medium" for="confirm_password">
                            <i class="fas fa-check-lock mr-2 text-soft-blue"></i> Konfirmasi Password
                        </label>
                        <input type="password" name="confirm_password" id="confirm_password" required
                               class="form-input"
                               placeholder="Ulangi password di atas">
                    </div>
                </div>
                
                <button type="submit" name="register" 
                        class="w-full bg-highlight text-primary-dark py-3 rounded-lg hover:bg-highlight/90 transition font-extrabold text-lg shadow-lg shadow-highlight/30 mt-4">
                    <i class="fas fa-user-plus mr-2"></i> Selesaikan Pendaftaran
                </button>
            </form>
            
            <div class="text-center mt-8">
                <p class="text-gray-400 text-sm">Sudah punya akun? 
                    <a href="login.php" class="text-soft-blue hover:text-highlight font-medium transition">
                        Login di sini
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <style>
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #4b5563; /* border-gray-600 */
            background-color: #192038; /* bg-primary-dark */
            border-radius: 0.5rem; /* rounded-lg */
            color: white;
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus {
            box-shadow: 0 0 0 2px rgba(246, 177, 122, 0.5); /* shadow ring highlight */
            border-color: #f6b17a; /* border highlight */
        }
    </style>
</body>
</html>