<?php
// Asumsi: Variabel $current_user dan $connection sudah didefinisikan/ditarik dari sesi
// dan fungsi getCurrentUser($connection) sudah tersedia.
$current_user = getCurrentUser($connection);
?>
<header class="sticky top-0 z-50 shadow-md bg-dark-accent/95 backdrop-blur-sm border-b border-gray-700">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center flex-shrink-0">
                <a href="index.php" class="text-2xl font-bold text-highlight hover:text-soft-blue transition">E-Book</a>
            </div>
            
            <nav class="hidden md:flex space-x-8 lg:mx-auto">
                <a href="index.php" class="nav-link-custom">Beranda</a>
                <a href="books.php" class="nav-link-custom">Koleksi Buku</a>
                <a href="contact.php" class="nav-link-custom">Kontak</a>
            </nav>
            
            <div class="flex items-center space-x-4">
                <?php if ($current_user): ?>
                    <div class="flex items-center space-x-3">
                        <img src="uploads/profiles/<?php echo $current_user['foto_profil'] ?: 'default.png'; ?>" 
                             alt="<?php echo $current_user['name']; ?>" 
                             class="w-8 h-8 rounded-full object-cover border-2 border-highlight">
                        
                        <a href="user_panel.php" class="text-gray-200 hover:text-soft-blue font-medium hidden sm:block">
                            <?php echo $current_user['name']; ?>
                        </a>
                        
                        <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-full hover:bg-red-700 transition text-sm font-semibold shadow-md">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex space-x-3">
                        <a href="login.php" class="px-4 py-2 font-semibold text-soft-blue border border-soft-blue rounded-full hover:bg-soft-blue hover:text-white transition duration-300 hidden sm:block">
                            Login
                        </a>
                        <a href="register.php" class="bg-highlight text-primary-dark px-4 py-2 rounded-full hover:bg-highlight/90 transition font-semibold shadow-md">
                            Daftar
                        </a>
                    </div>
                <?php endif; ?>

                <div class="md:hidden">
                    <button id="mobile-menu-button-single" type="button" class="text-gray-200 hover:text-soft-blue focus:outline-none p-2 rounded-md">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    /* Styling Nav Link Kustom (Sama seperti yang disarankan sebelumnya) */
    .nav-link-custom {
        color: #e5e7eb; /* text-gray-200 */
        font-weight: 500;
        padding: 0.5rem 0.75rem;
        transition: color 0.3s ease, border-bottom 0.3s ease;
        border-bottom: 2px solid transparent;
        display: flex;
        align-items: center;
    }
    .nav-link-custom:hover {
        color: #6f76fd; /* text-soft-blue */
        border-bottom-color: #6f76fd;
    }
    /* Tambahkan definisi warna kustom untuk memastikan CDN Tailwind mengenali warna: */
    .bg-primary-dark { background-color: #192038; } 
    .bg-soft-blue { background-color: #6f76fd; }
    .bg-dark-accent { background-color: #2d3250; }
    .bg-highlight { background-color: #f6b17a; }
    .text-primary-dark { color: #192038; }
</style>