<?php
$current_user = getCurrentUser($connection);
?>
<header class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="index.php" class="text-2xl font-bold text-blue-600">E-Book</a>
            </div>
            
            <!-- Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium">Beranda</a>
                <a href="books.php" class="text-gray-700 hover:text-blue-600 font-medium">Koleksi Buku</a>
                <a href="contact.php" class="text-gray-700 hover:text-blue-600 font-medium">Kontak</a>
            </nav>
            
            <!-- User Actions -->
            <div class="flex items-center space-x-4">
                <?php if ($current_user): ?>
                    <div class="flex items-center space-x-3">
                        <img src="uploads/profiles/<?php echo $current_user['foto_profil'] ?: 'default.png'; ?>" 
                             alt="<?php echo $current_user['name']; ?>" 
                             class="w-8 h-8 rounded-full object-cover">
                        <a href="user_panel.php" class="text-gray-700 hover:text-blue-600 font-medium">
                            <?php echo $current_user['name']; ?>
                        </a>
                        <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-sm">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex space-x-2">
                        <a href="login.php" class="text-gray-700 hover:text-blue-600 font-medium px-4 py-2">Login</a>
                        <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Daftar</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>