<?php 
// user_panel.php

// Sertakan file konfigurasi database dan fungsi helper
include 'config/database.php';

// Redirect jika belum login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Ambil data user yang sedang login
$user = getCurrentUser($connection);
$password_success = $password_error = null;

// --- 1. Proses Update Profil ---
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    // Handle upload foto profil
    $foto_profil = $user['foto_profil']; // Default: gunakan foto lama
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['foto_profil']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $file_extension = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
            $new_filename = "profile_" . $user['user_id'] . "_" . time() . "." . $file_extension;
            $upload_path = "uploads/profiles/" . $new_filename;
            
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_path)) {
                // Hapus foto lama jika ada
                if ($foto_profil && file_exists("uploads/profiles/" . $foto_profil)) {
                    unlink("uploads/profiles/" . $foto_profil);
                }
                $foto_profil = $new_filename;
            }
        }
    }
    
    // Update data di database
    $update_query = "UPDATE user SET name = ?, email = ?, foto_profil = ? WHERE user_id = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("sssi", $name, $email, $foto_profil, $user['user_id']);
    $stmt->execute();
    
    // Redirect untuk menghindari pengiriman form berulang
    header("Location: user_panel.php?update_status=success#profile");
    exit();
}

// --- 2. Proses Update Password ---
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE user SET password = ? WHERE user_id = ?";
            $stmt = $connection->prepare($update_query);
            $stmt->bind_param("si", $hashed_password, $user['user_id']);
            $stmt->execute();
            
            $password_success = "Password berhasil diubah!";
        } else {
            $password_error = "Password baru tidak cocok!";
        }
    } else {
        $password_error = "Password saat ini salah!";
    }
}

// --- 3. Ambil Buku Favorit User ---
$favorit_query = "
    SELECT b.*, p.name as penulis_name, k.kategori
    FROM buku_favorit bf
    JOIN buku b ON bf.buku_id = b.buku_id
    JOIN penulis p ON b.penulis_id = p.penulis_id
    JOIN kategori k ON b.kategori_id = k.kategori_id
    WHERE bf.user_id = ?
    ORDER BY bf.ditambahkan_pada DESC
";
$stmt = $connection->prepare($favorit_query);
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$favorit_result = $stmt->get_result();

// --- 4. Ambil Buku yang Disimpan User ---
$simpan_query = "
    SELECT b.*, p.name as penulis_name, k.kategori
    FROM simpan_buku sb
    JOIN buku b ON sb.buku_id = b.buku_id
    JOIN penulis p ON b.penulis_id = p.penulis_id
    JOIN kategori k ON b.kategori_id = k.kategori_id
    WHERE sb.user_id = ?
    ORDER BY sb.disimpan_pada DESC
";
$stmt = $connection->prepare($simpan_query);
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$simpan_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Pengguna - E-Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Skema Warna Gelap yang Konsisten
                        primary: {
                            dark: '#1e2030', // Latar belakang utama (lebih gelap dari #2a2d44)
                            medium: '#2a2d44', // Background card/sidebar
                            light: '#4a4f73' // Aksen ringan
                        },
                        accent: {
                            DEFAULT: '#5d69b6', // Biru-Ungu (Aksen utama)
                            secondary: '#f9b17a' // Orange (Aksen kontras)
                        },
                        text: {
                            light: '#ffffff', // Teks di atas warna gelap
                            dark: '#2d3250', // Teks di atas warna terang (sekarang jarang digunakan)
                            muted: '#a0a3b8' // Teks sekunder/muted (lebih terang di atas primary-medium)
                        },
                        bg: {
                            white: '#ffffff',
                            gray: '#f0f2f5'
                        }
                    },
                    fontFamily: {
                        'raleway': ['Raleway', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Raleway', sans-serif;
        }
        
        /* Mengubah warna active agar lebih kontras dengan skema gelap */
        .nav-item.active {
            background: rgba(93, 105, 182, 0.2); /* Sedikit sentuhan accent */
            color: #f9b17a; /* Menggunakan secondary accent agar lebih menonjol */
            border-left-color: #f9b17a !important; /* Aksen kiri */
        }
        
        .nav-item {
             color: #a0a3b8; /* text-muted */
             border-left-color: transparent !important;
        }

        .nav-item:hover {
            color: #ffffff; /* Mengubah hover text menjadi putih */
            background-color: rgba(93, 105, 182, 0.1);
        }
        
        .book-card {
            transition: all 0.3s ease;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
        }
        
        /* Pastikan input text memiliki background yang kontras */
        input[type="text"], input[type="email"], input[type="password"] {
            color: #2a2d44; /* Warna teks gelap */
        }
    </style>
</head>
<body class="bg-primary-dark text-text-light font-raleway">
    <?php include 'components/header.php'; ?>

    <div class="min-h-screen py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10">
                <h1 class="text-4xl font-bold text-text-light mb-4 relative pb-4">
                    Panel Pengguna 📚
                    <div class="absolute bottom-0 left-0 w-16 h-1 bg-accent-secondary rounded-full"></div>
                </h1>
                <p class="text-text-muted text-lg">Kelola profil dan preferensi membaca Anda</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
                <div class="lg:col-span-1">
                    <div class="bg-primary-medium rounded-2xl shadow-xl p-6 sticky top-8 border border-primary-light">
                        <div class="text-center mb-8 pb-6 border-b border-primary-light">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-accent-DEFAULT to-primary-light mx-auto mb-4 flex items-center justify-center text-text-light text-2xl font-bold overflow-hidden border-4 border-accent-secondary">
                                <?php if ($user['foto_profil']): ?>
                                    <img src="uploads/profiles/<?php echo $user['foto_profil']; ?>" 
                                        alt="<?php echo $user['name']; ?>" 
                                        class="w-full h-full object-cover">
                                <?php else: ?>
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                <?php endif; ?>
                            </div>
                            <h2 class="text-xl font-semibold text-text-light mb-1"><?php echo htmlspecialchars($user['name']); ?></h2>
                            <p class="text-text-muted text-sm">@<?php echo htmlspecialchars($user['username']); ?></p>
                        </div>
                        
                        <nav class="space-y-2">
                            <a href="#profile" class="nav-item flex items-center px-4 py-3 hover:text-text-light rounded-xl transition-all duration-300 border-l-4 border-transparent active">
                                <i class="fas fa-user-circle w-5 mr-3"></i>
                                <span class="font-semibold">Profil Saya</span>
                            </a>
                            <a href="#password" class="nav-item flex items-center px-4 py-3 hover:text-text-light rounded-xl transition-all duration-300 border-l-4 border-transparent">
                                <i class="fas fa-lock w-5 mr-3"></i>
                                <span class="font-semibold">Ubah Password</span>
                            </a>
                            <a href="#favorites" class="nav-item flex items-center px-4 py-3 hover:text-text-light rounded-xl transition-all duration-300 border-l-4 border-transparent">
                                <i class="fas fa-heart w-5 mr-3"></i>
                                <span class="font-semibold">Buku Favorit</span>
                            </a>
                            <a href="#saved" class="nav-item flex items-center px-4 py-3 hover:text-text-light rounded-xl transition-all duration-300 border-l-4 border-transparent">
                                <i class="fas fa-bookmark w-5 mr-3"></i>
                                <span class="font-semibold">Buku Disimpan</span>
                            </a>
                        </nav>
                    </div>
                </div>
                
                <div class="lg:col-span-3 space-y-10">
                    
                    <div id="profile" class="content-section bg-primary-medium rounded-2xl shadow-xl p-8 border border-primary-light">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-text-light">📝 Profil Saya</h2>
                            <div class="w-12 h-1 bg-accent-secondary rounded-full"></div>
                        </div>
                        
                        <?php if (isset($_GET['update_status']) && $_GET['update_status'] == 'success'): ?>
                            <div class="bg-green-600/20 border border-green-500 text-green-300 px-6 py-4 rounded-xl mb-6 flex items-center">
                                <i class="fas fa-check-circle mr-3"></i>
                                Profil berhasil diperbarui!
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-text-light font-semibold mb-3">Nama Lengkap</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-DEFAULT focus:border-transparent transition-all duration-300 text-text-dark">
                                </div>
                                
                                <div>
                                    <label class="block text-text-light font-semibold mb-3">Username</label>
                                    <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-600/30 text-text-muted cursor-not-allowed">
                                    <p class="text-sm text-text-muted mt-2">Username tidak dapat diubah</p>
                                </div>
                                
                                <div>
                                    <label class="block text-text-light font-semibold mb-3">Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-DEFAULT focus:border-transparent transition-all duration-300 text-text-dark">
                                </div>
                                
                                <div>
                                    <label class="block text-text-light font-semibold mb-3">Foto Profil</label>
                                    <input type="file" name="foto_profil" 
                                            accept="image/jpeg,image/png,image/gif"
                                            class="w-full text-text-light file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent-DEFAULT file:text-text-light hover:file:bg-accent-secondary/80 file:cursor-pointer">
                                </div>
                            </div>
                            
                            <button type="submit" name="update_profile" 
                                    class="bg-accent-DEFAULT text-text-light px-8 py-3 rounded-xl hover:bg-accent-secondary transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                    
                    <div id="password" class="content-section bg-primary-medium rounded-2xl shadow-xl p-8 border border-primary-light">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-text-light">🔑 Ubah Password</h2>
                            <div class="w-12 h-1 bg-accent-secondary rounded-full"></div>
                        </div>
                        
                        <?php if (isset($password_success)): ?>
                            <div class="bg-green-600/20 border border-green-500 text-green-300 px-6 py-4 rounded-xl mb-6 flex items-center">
                                <i class="fas fa-check-circle mr-3"></i>
                                <?php echo $password_success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($password_error)): ?>
                            <div class="bg-red-600/20 border border-red-500 text-red-300 px-6 py-4 rounded-xl mb-6 flex items-center">
                                <i class="fas fa-exclamation-circle mr-3"></i>
                                <?php echo $password_error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="space-y-6 mb-8">
                                <div>
                                    <label class="block text-text-light font-semibold mb-3">Password Saat Ini</label>
                                    <input type="password" name="current_password" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-DEFAULT focus:border-transparent transition-all duration-300 text-text-dark">
                                </div>
                                
                                <div>
                                    <label class="block text-text-light font-semibold mb-3">Password Baru</label>
                                    <input type="password" name="new_password" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-DEFAULT focus:border-transparent transition-all duration-300 text-text-dark">
                                </div>
                                
                                <div>
                                    <label class="block text-text-light font-semibold mb-3">Konfirmasi Password Baru</label>
                                    <input type="password" name="confirm_password" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-accent-DEFAULT focus:border-transparent transition-all duration-300 text-text-dark">
                                </div>
                            </div>
                            
                            <button type="submit" name="update_password" 
                                    class="bg-accent-DEFAULT text-text-light px-8 py-3 rounded-xl hover:bg-accent-secondary transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-key mr-2"></i>Ubah Password
                            </button>
                        </form>
                    </div>
                    
                    <div id="favorites" class="content-section bg-primary-medium rounded-2xl shadow-xl p-8 border border-primary-light">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-text-light">💖 Buku Favorit Saya</h2>
                            <div class="w-12 h-1 bg-accent-secondary rounded-full"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            <?php
                            if ($favorit_result->num_rows > 0) {
                                while ($buku = $favorit_result->fetch_assoc()) {
                                    // Menggunakan htmlspecialchars untuk mencegah XSS
                                    $judul = htmlspecialchars($buku['judul']);
                                    $penulis = htmlspecialchars($buku['penulis_name']);
                                    $kategori = htmlspecialchars($buku['kategori']);
                                    $buku_id = htmlspecialchars($buku['buku_id']);
                                    $cover_url = $buku['cover'] ? "uploads/covers/" . htmlspecialchars($buku['cover']) : '';
                                    $cover_text = strtoupper(substr($buku['judul'], 0, 3));

                                    echo "
                                    <div class='book-card bg-primary-medium/80 border border-primary-light rounded-2xl p-5 hover:shadow-2xl cursor-pointer transition-all duration-300'>
                                        <div class='flex space-x-4'>
                                            <div class='flex-shrink-0'>
                                                <a href='book_detail.php?id={$buku_id}' class='block w-20 h-24 rounded-xl bg-gradient-to-br from-primary-light to-accent-DEFAULT flex items-center justify-center text-text-light font-bold text-sm overflow-hidden shadow-md'>
                                                    " . ($cover_url ? 
                                                        "<img src='{$cover_url}' alt='{$judul}' class='w-full h-full object-cover'>" : 
                                                        "{$cover_text}") . "
                                                </a>
                                            </div>
                                            <div class='flex-1 min-w-0'>
                                                <h3 class='font-bold text-text-light mb-1 line-clamp-2 leading-tight'>{$judul}</h3>
                                                <p class='text-text-muted text-sm mb-2'>Oleh: {$penulis}</p>
                                                <span class='inline-block bg-accent-DEFAULT/20 text-accent-secondary px-3 py-1 rounded-full text-xs font-semibold'>{$kategori}</span>
                                                <a href='book_detail.php?id={$buku_id}' 
                                                   class='block mt-3 text-accent-secondary hover:text-accent-DEFAULT font-semibold text-sm transition-colors duration-300'>
                                                    Lihat Detail <i class='fas fa-arrow-right ml-1'></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    ";
                                }
                            } else {
                                echo "
                                <div class='col-span-full text-center py-12'>
                                    <i class='fas fa-heart text-5xl text-primary-light mb-4'></i>
                                    <p class='text-text-muted text-lg mb-4'>Anda belum menambahkan buku ke favorit.</p>
                                    <a href='library.php' class='inline-block bg-accent-DEFAULT text-text-light px-6 py-3 rounded-xl hover:bg-accent-secondary transition-colors duration-300 font-semibold'>
                                        <i class='fas fa-book-open mr-2'></i> Jelajahi Buku
                                    </a>
                                </div>
                                ";
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div id="saved" class="content-section bg-primary-medium rounded-2xl shadow-xl p-8 border border-primary-light">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-2xl font-bold text-text-light">🔖 Buku yang Disimpan</h2>
                            <div class="w-12 h-1 bg-accent-secondary rounded-full"></div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            <?php
                            // Reset pointer result set untuk query simpan
                            $simpan_result->data_seek(0);
                            
                            if ($simpan_result->num_rows > 0) {
                                while ($buku = $simpan_result->fetch_assoc()) {
                                    // Menggunakan htmlspecialchars untuk mencegah XSS
                                    $judul = htmlspecialchars($buku['judul']);
                                    $penulis = htmlspecialchars($buku['penulis_name']);
                                    $kategori = htmlspecialchars($buku['kategori']);
                                    $buku_id = htmlspecialchars($buku['buku_id']);
                                    $cover_url = $buku['cover'] ? "uploads/covers/" . htmlspecialchars($buku['cover']) : '';
                                    $cover_text = strtoupper(substr($buku['judul'], 0, 3));
                                    
                                    echo "
                                    <div class='book-card bg-primary-medium/80 border border-primary-light rounded-2xl p-5 hover:shadow-2xl cursor-pointer transition-all duration-300'>
                                        <div class='flex space-x-4'>
                                            <div class='flex-shrink-0'>
                                                <a href='book_detail.php?id={$buku_id}' class='block w-20 h-24 rounded-xl bg-gradient-to-br from-primary-light to-accent-DEFAULT flex items-center justify-center text-text-light font-bold text-sm overflow-hidden shadow-md'>
                                                    " . ($cover_url ? 
                                                        "<img src='{$cover_url}' alt='{$judul}' class='w-full h-full object-cover'>" : 
                                                        "{$cover_text}") . "
                                                </a>
                                            </div>
                                            <div class='flex-1 min-w-0'>
                                                <h3 class='font-bold text-text-light mb-1 line-clamp-2 leading-tight'>{$judul}</h3>
                                                <p class='text-text-muted text-sm mb-2'>Oleh: {$penulis}</p>
                                                <span class='inline-block bg-accent-DEFAULT/20 text-accent-secondary px-3 py-1 rounded-full text-xs font-semibold'>{$kategori}</span>
                                                <a href='book_detail.php?id={$buku_id}' 
                                                   class='block mt-3 text-accent-secondary hover:text-accent-DEFAULT font-semibold text-sm transition-colors duration-300'>
                                                    Lihat Detail <i class='fas fa-arrow-right ml-1'></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    ";
                                }
                            } else {
                                echo "
                                <div class='col-span-full text-center py-12'>
                                    <i class='fas fa-bookmark text-5xl text-primary-light mb-4'></i>
                                    <p class='text-text-muted text-lg mb-4'>Anda belum menyimpan buku apapun.</p>
                                    <a href='library.php' class='inline-block bg-accent-DEFAULT text-text-light px-6 py-3 rounded-xl hover:bg-accent-secondary transition-colors duration-300 font-semibold'>
                                        <i class='fas fa-book-open mr-2'></i> Jelajahi Buku
                                    </a>
                                </div>
                                ";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script>
        // Tambahkan fungsi untuk inisialisasi awal active nav item berdasarkan hash URL
        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash || '#profile';
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            const activeItem = document.querySelector(`.nav-item[href='${hash}']`);
            if (activeItem) {
                activeItem.classList.add('active');
            }
        });

        // Smooth scroll untuk navigation dan update active nav item
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    // Update active class
                    document.querySelectorAll('.nav-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Scroll
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Perbaikan: Update active nav item saat scroll
        const sections = document.querySelectorAll('.content-section');
        const navItems = document.querySelectorAll('.nav-item');

        const updateActiveNav = () => {
            let current = 'profile'; // Default
            const offset = 150; // Offset agar link aktif sedikit lebih awal

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (window.scrollY >= sectionTop - offset) {
                    current = section.getAttribute('id');
                }
            });

            navItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href').substring(1) === current) {
                    item.classList.add('active');
                }
            });
        };

        window.addEventListener('scroll', updateActiveNav);
        // Panggil saat load untuk menentukan posisi awal
        window.addEventListener('load', updateActiveNav);
    </script>
</body>
</html>