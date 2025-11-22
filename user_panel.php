<?php 
include 'config/database.php';

// Redirect jika belum login
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user = getCurrentUser($connection);

// Proses update profil
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    // Handle foto profil upload
    $foto_profil = $user['foto_profil'];
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
    
    $update_query = "UPDATE user SET name = ?, email = ?, foto_profil = ? WHERE user_id = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("sssi", $name, $email, $foto_profil, $user['user_id']);
    $stmt->execute();
    
    header("Location: user_panel.php");
    exit();
}

// Proses update password
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

// Ambil buku favorit user
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

// Ambil buku yang disimpan user
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
</head>
<body class="bg-gray-100">
    <!-- Header/Navbar -->
    <?php include 'components/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Panel Pengguna</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center mb-6">
                        <img src="uploads/profiles/<?php echo $user['foto_profil'] ?: 'default.png'; ?>" 
                             alt="<?php echo $user['name']; ?>" 
                             class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                        <h2 class="text-xl font-semibold"><?php echo $user['name']; ?></h2>
                        <p class="text-gray-600">@<?php echo $user['username']; ?></p>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="#profile" class="block px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-semibold">
                            Profil Saya
                        </a>
                        <a href="#password" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                            Ubah Password
                        </a>
                        <a href="#favorites" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                            Buku Favorit
                        </a>
                        <a href="#saved" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                            Buku Disimpan
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Section -->
                <div id="profile" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-6">Profil Saya</h2>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" value="<?php echo $user['name']; ?>" required
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Username</label>
                                <input type="text" value="<?php echo $user['username']; ?>" disabled
                                       class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                                <p class="text-sm text-gray-500 mt-1">Username tidak dapat diubah</p>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="<?php echo $user['email']; ?>" required
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Foto Profil</label>
                                <input type="file" name="foto_profil" 
                                       accept="image/jpeg,image/png,image/gif"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <button type="submit" name="update_profile" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
                
                <!-- Password Section -->
                <div id="password" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-6">Ubah Password</h2>
                    
                    <?php if (isset($password_success)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <?php echo $password_success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($password_error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <?php echo $password_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-gray-700 mb-2">Password Saat Ini</label>
                                <input type="password" name="current_password" required
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Password Baru</label>
                                <input type="password" name="new_password" required
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" required
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <button type="submit" name="update_password" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Ubah Password
                        </button>
                    </form>
                </div>
                
                <!-- Favorites Section -->
                <div id="favorites" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-6">Buku Favorit Saya</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php
                        if ($favorit_result->num_rows > 0) {
                            while ($buku = $favorit_result->fetch_assoc()) {
                                echo "
                                <div class='flex border rounded-lg p-4 hover:shadow-md transition'>
                                    <img src='uploads/covers/{$buku['cover']}' 
                                         alt='{$buku['judul']}' 
                                         class='w-16 h-20 object-cover rounded mr-4'>
                                    <div class='flex-1'>
                                        <h3 class='font-semibold mb-1'>{$buku['judul']}</h3>
                                        <p class='text-sm text-gray-600 mb-1'>Oleh: {$buku['penulis_name']}</p>
                                        <span class='bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs'>{$buku['kategori']}</span>
                                        <a href='book_detail.php?id={$buku['buku_id']}' 
                                           class='block mt-2 text-blue-600 hover:underline text-sm'>Lihat Detail</a>
                                    </div>
                                </div>
                                ";
                            }
                        } else {
                            echo "<p class='text-gray-500'>Belum ada buku favorit.</p>";
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Saved Books Section -->
                <div id="saved" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-6">Buku yang Disimpan</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php
                        if ($simpan_result->num_rows > 0) {
                            while ($buku = $simpan_result->fetch_assoc()) {
                                echo "
                                <div class='flex border rounded-lg p-4 hover:shadow-md transition'>
                                    <img src='uploads/covers/{$buku['cover']}' 
                                         alt='{$buku['judul']}' 
                                         class='w-16 h-20 object-cover rounded mr-4'>
                                    <div class='flex-1'>
                                        <h3 class='font-semibold mb-1'>{$buku['judul']}</h3>
                                        <p class='text-sm text-gray-600 mb-1'>Oleh: {$buku['penulis_name']}</p>
                                        <span class='bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs'>{$buku['kategori']}</span>
                                        <a href='book_detail.php?id={$buku['buku_id']}' 
                                           class='block mt-2 text-blue-600 hover:underline text-sm'>Lihat Detail</a>
                                    </div>
                                </div>
                                ";
                            }
                        } else {
                            echo "<p class='text-gray-500'>Belum ada buku yang disimpan.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
</body>
</html>