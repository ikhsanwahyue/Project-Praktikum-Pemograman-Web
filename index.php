<?php 
// Sertakan konfigurasi database
include 'config/database.php'; 

// 1. Ambil data Kategori/Tema untuk form pencarian (Asumsi koneksi database berfungsi)
$kategori_result = null;
if (isset($connection) && $connection instanceof mysqli) {
    $kategori_result = $connection->query("SELECT kategori, kategori_id FROM kategori ORDER BY kategori ASC");
}

// Definisikan lebar card kustom untuk Horizontal Scroll
$card_width_class = 'w-[140px] md:w-[160px] lg:w-[180px] flex-none';

// Query-query yang akan digunakan di halaman
// Query Favorit
$query_fav = "SELECT b.*, p.name as penulis_name, k.kategori, COUNT(bf.favorit_id) as jumlah_favorit, AVG(c.rating) as avg_rating FROM buku b JOIN penulis p ON b.penulis_id = p.penulis_id JOIN kategori k ON b.kategori_id = k.kategori_id LEFT JOIN buku_favorit bf ON b.buku_id = bf.buku_id LEFT JOIN comments c ON b.buku_id = c.buku_id GROUP BY b.buku_id ORDER BY jumlah_favorit DESC LIMIT 10";

// Query Rilisan Terbaru
$query_new = "SELECT b.*, p.name as penulis_name, k.kategori, AVG(c.rating) as avg_rating FROM buku b JOIN penulis p ON b.penulis_id = p.penulis_id JOIN kategori k ON b.kategori_id = k.kategori_id LEFT JOIN comments c ON b.buku_id = c.buku_id GROUP BY b.buku_id ORDER BY b.terbit_pada DESC, b.dibuat_pada DESC LIMIT 8";

// Query Rekomendasi (Random)
$query_recom = "SELECT b.*, p.name as penulis_name, k.kategori, AVG(c.rating) as avg_rating FROM buku b JOIN penulis p ON b.penulis_id = p.penulis_id JOIN kategori k ON b.kategori_id = k.kategori_id LEFT JOIN comments c ON b.buku_id = c.buku_id GROUP BY b.buku_id ORDER BY RAND() LIMIT 8";

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - E-Book Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Palet Warna Kustom */
        .bg-primary-dark { background-color: #192038; } 
        .bg-soft-blue { background-color: #6f76fd; }
        .bg-dark-accent { background-color: #2d3250; }
        .bg-highlight { background-color: #f6b17a; }
        .text-soft-blue { color: #6f76fd; }
        .text-highlight { color: #f6b17a; }
        .border-soft-blue { border-color: #6f76fd; }

        /* Styling Card Buku yang lebih halus */
        .book-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .book-card:hover {
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .book-cover {
            height: 12rem; /* Diperkecil agar lebih cocok untuk horizontal scroll */
            width: 100%;
            object-fit: cover;
            object-position: top;
        }

        /* --- Custom Scrollbar Hide for Horizontal Scrolling --- */
        /* Menyembunyikan scrollbar di browser WebKit (Chrome, Safari) */
        .horizontal-scroll-container::-webkit-scrollbar {
            display: none;
        }
        /* Menyembunyikan scrollbar di browser IE, Edge, dan Firefox */
        .horizontal-scroll-container {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="bg-primary-dark text-white">
    <?php include 'components/header.php'; ?>

    <section class="bg-primary-dark py-28 border-b border-gray-700">
        <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center justify-between">
            
            <div class="lg:w-1/2 text-left mb-12 lg:mb-0">
                <p class="text-xl font-medium text-highlight mb-4">SELAMAT DATANG DI AZIZI.IO!</p>
                <h1 class="text-5xl md:text-6xl font-extrabold leading-tight">
                    Jelajahi Dunia Literasi Digital.
                </h1>
                <p class="text-xl mt-4 text-gray-300">
                    Temukan dan baca ribuan koleksi buku digital berkualitas, dari fiksi hingga non-fiksi.
                </p>
            </div>
            
            <div class="lg:w-5/12 bg-dark-accent p-8 rounded-2xl shadow-2xl">
                <h2 class="text-3xl font-bold mb-6 text-white">Pencarian Cepat</h2>
                <form action="books.php" method="GET">
                    <div class="space-y-4">
                        
                        <div>
                            <input type="text" name="keyword" id="keyword" placeholder="Cari judul atau penulis..." class="w-full px-5 py-3 border border-dark-accent bg-primary-dark rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-soft-blue focus:border-soft-blue transition">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <select id="jenjang" name="jenjang" class="w-full px-5 py-3 border border-dark-accent bg-primary-dark rounded-xl text-white focus:outline-none focus:ring-soft-blue focus:border-soft-blue transition">
                                    <option value="">Semua Jenjang</option>
                                    <option>PAUD</option>
                                    <option>SD</option>
                                    <option>SMP</option>
                                    <option>SMA</option>
                                    <option>Umum</option>
                                </select>
                            </div>
                            <div>
                                <select id="tema" name="tema" class="w-full px-5 py-3 border border-dark-accent bg-primary-dark rounded-xl text-white focus:outline-none focus:ring-soft-blue focus:border-soft-blue transition">
                                    <option value="">Semua Tema</option>
                                    <?php
                                    // Mengisi opsi tema/kategori secara dinamis
                                    if ($kategori_result && $kategori_result->num_rows > 0) {
                                        while($kategori = $kategori_result->fetch_assoc()) {
                                            echo "<option value='{$kategori['kategori_id']}'>{$kategori['kategori']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-soft-blue text-white py-3 rounded-xl font-semibold text-lg shadow-lg hover:bg-opacity-90 transition duration-300">
                            CARI BUKU
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    

    <section class="py-24"> 
        <div class="px-6 mb-8">
            <h2 class="text-4xl font-bold text-center mt-2">Buku Favorit Pilihan</h2>
            <p class="text-lg text-gray-400 text-center">Koleksi terpopuler berdasarkan rating dan jumlah favorit pengguna.</p>
        </div>
        
        <div class="horizontal-scroll-container flex space-x-6 overflow-x-scroll pb-4 px-6">
            <?php
            $result_fav = $connection->query($query_fav);
            
            if ($result_fav && $result_fav->num_rows > 0) {
                while ($buku = $result_fav->fetch_assoc()) {
                    $rating = $buku['avg_rating'] ? round($buku['avg_rating'], 1) : 0;
                    echo "
                    <a href='book_detail.php?id={$buku['buku_id']}' class='book-card bg-dark-accent text-white {$card_width_class}'>
                        <img src='uploads/covers/{$buku['cover']}' alt='{$buku['judul']}' class='book-cover'>
                        <div class='p-4'>
                            <h3 class='font-bold text-base mb-1 line-clamp-2'>{$buku['judul']}</h3>
                            <p class='text-xs text-gray-400 mb-2'>Oleh: {$buku['penulis_name']}</p>
                            
                            <div class='flex justify-between items-center text-xs'>
                                <span class='bg-soft-blue text-white px-2 py-0.5 rounded-full text-[10px] font-medium'>{$buku['kategori']}</span>
                                <div class='flex items-center'>
                                    <span class='text-highlight text-sm'>★</span>
                                    <span class='ml-1 text-white font-semibold'>{$rating}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    ";
                }
            } else {
                echo "<p class='text-gray-400 px-6'>Belum ada buku favorit yang tersedia.</p>";
            }
            ?>
        </div>
    </section>

    <section class="py-10 bg-dark-accent">
        <div class="px-6 mb-8">
            <h2 class="text-4xl font-bold text-center">Rilisan Terbaru</h2>
            <p class="text-lg text-gray-400 text-center">Jangan sampai ketinggalan buku-buku paling baru.</p>
        </div>

        <div class="horizontal-scroll-container flex space-x-6 overflow-x-scroll pb-4 px-6">
            <?php
            $result_new = $connection->query($query_new);
            
            if ($result_new && $result_new->num_rows > 0) {
                while ($buku = $result_new->fetch_assoc()) {
                    $rating = $buku['avg_rating'] ? round($buku['avg_rating'], 1) : 0;
                    echo "
                    <a href='book_detail.php?id={$buku['buku_id']}' class='book-card bg-primary-dark text-white {$card_width_class}'>
                        <img src='uploads/covers/{$buku['cover']}' alt='{$buku['judul']}' class='book-cover'>
                        <div class='p-4'>
                            <h3 class='font-bold text-base mb-1 line-clamp-2'>{$buku['judul']}</h3>
                            <p class='text-xs text-gray-400 mb-2'>Oleh: {$buku['penulis_name']}</p>
                            
                            <div class='flex justify-between items-center text-xs'>
                                <span class='bg-highlight text-primary-dark px-2 py-0.5 rounded-full text-[10px] font-medium'>{$buku['kategori']}</span>
                                <div class='flex items-center'>
                                    <span class='text-highlight text-sm'>★</span>
                                    <span class='ml-1 text-white font-semibold'>{$rating}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    ";
                }
            } else {
                echo "<p class='text-gray-400 px-6'>Belum ada rilisan terbaru yang tersedia.</p>";
            }
            ?>
        </div>
    </section>

    <section class="py-16 bg-primary-dark border-t border-gray-700">
        <div class="px-6 mb-8">
            <h2 class="text-4xl font-bold text-highlight text-center">Coba Baca Ini, Kayaknya Cocok Untukmu!</h2>
            <p class="text-lg text-gray-400 text-center">Rekomendasi pilihan berdasarkan popularitas dan kecocokan.</p>
        </div>

        <div class="horizontal-scroll-container flex space-x-6 overflow-x-scroll pb-4 px-6">
            <?php
            $result_recom = $connection->query($query_recom);
            
            if ($result_recom && $result_recom->num_rows > 0) {
                while ($buku = $result_recom->fetch_assoc()) {
                    $rating = $buku['avg_rating'] ? round($buku['avg_rating'], 1) : 0;
                    echo "
                    <a href='book_detail.php?id={$buku['buku_id']}' class='book-card bg-dark-accent text-white {$card_width_class}'>
                        <img src='uploads/covers/{$buku['cover']}' alt='{$buku['judul']}' class='book-cover'>
                        <div class='p-4'>
                            <h3 class='font-bold text-base mb-1 line-clamp-2'>{$buku['judul']}</h3>
                            <p class='text-xs text-gray-400 mb-2'>Oleh: {$buku['penulis_name']}</p>
                            
                            <div class='flex justify-between items-center text-xs'>
                                <span class='bg-soft-blue text-white px-2 py-0.5 rounded-full text-[10px] font-medium'>REKOMENDASI</span>
                                <div class='flex items-center'>
                                    <span class='text-highlight text-sm'>★</span>
                                    <span class='ml-1 text-white font-semibold'>{$rating}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    ";
                }
            } else {
                echo "<p class='text-gray-400 px-6'>Rekomendasi belum tersedia saat ini.</p>";
            }
            ?>
        </div>

        <div class="text-center mt-12">
             <a href="books.php" class="bg-highlight text-primary-dark px-10 py-4 rounded-full font-semibold text-lg shadow-xl hover:bg-opacity-90 transition duration-300">
                LIHAT SEMUA KOLEKSI
             </a>
        </div>

    </section>

    <?php include 'components/footer.php'; ?>
</body>
</html>