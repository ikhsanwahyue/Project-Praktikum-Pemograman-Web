<?php include 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - E-Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header/Navbar -->
    <?php include 'components/header.php'; ?>

    <!-- Hero Section -->
    <section class="bg-blue-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">Selamat Datang di Perpustakaan Digital</h1>
            <p class="text-xl mb-8">Temukan ribuan buku digital berkualitas untuk dibaca kapan saja</p>
            <a href="books.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">Jelajahi Koleksi</a>
        </div>
    </section>

    <!-- Buku Favorit -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Buku Favorit Pengguna</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                // Query untuk mendapatkan buku favorit (buku dengan jumlah favorit terbanyak)
                $query = "
                    SELECT b.*, p.name as penulis_name, k.kategori, 
                           COUNT(bf.favorit_id) as jumlah_favorit,
                           AVG(c.rating) as avg_rating
                    FROM buku b
                    JOIN penulis p ON b.penulis_id = p.penulis_id
                    JOIN kategori k ON b.kategori_id = k.kategori_id
                    LEFT JOIN buku_favorit bf ON b.buku_id = bf.buku_id
                    LEFT JOIN comments c ON b.buku_id = c.buku_id
                    GROUP BY b.buku_id
                    ORDER BY jumlah_favorit DESC
                    LIMIT 4
                ";
                $result = $connection->query($query);
                
                while ($buku = $result->fetch_assoc()) {
                    $rating = $buku['avg_rating'] ? round($buku['avg_rating'], 1) : 0;
                    echo "
                    <div class='bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition'>
                        <img src='uploads/covers/{$buku['cover']}' alt='{$buku['judul']}' class='w-full h-48 object-cover'>
                        <div class='p-4'>
                            <h3 class='font-bold text-lg mb-2'>{$buku['judul']}</h3>
                            <p class='text-gray-600 mb-2'>Oleh: {$buku['penulis_name']}</p>
                            <div class='flex justify-between items-center'>
                                <span class='bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm'>{$buku['kategori']}</span>
                                <div class='flex items-center'>
                                    <span class='text-yellow-500'>★</span>
                                    <span class='ml-1 text-gray-700'>{$rating}</span>
                                </div>
                            </div>
                            <a href='book_detail.php?id={$buku['buku_id']}' class='mt-4 block text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition'>Lihat Detail</a>
                        </div>
                    </div>
                    ";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Buku Baru Rilis -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8 text-center">Buku Baru Rilis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                // Query untuk mendapatkan buku baru (urut berdasarkan terbit_pada terbaru)
                $query = "
                    SELECT b.*, p.name as penulis_name, k.kategori,
                           AVG(c.rating) as avg_rating
                    FROM buku b
                    JOIN penulis p ON b.penulis_id = p.penulis_id
                    JOIN kategori k ON b.kategori_id = k.kategori_id
                    LEFT JOIN comments c ON b.buku_id = c.buku_id
                    GROUP BY b.buku_id
                    ORDER BY b.terbit_pada DESC, b.dibuat_pada DESC
                    LIMIT 8
                ";
                $result = $connection->query($query);
                
                while ($buku = $result->fetch_assoc()) {
                    $rating = $buku['avg_rating'] ? round($buku['avg_rating'], 1) : 0;
                    echo "
                    <div class='bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition'>
                        <img src='uploads/covers/{$buku['cover']}' alt='{$buku['judul']}' class='w-full h-48 object-cover'>
                        <div class='p-4'>
                            <h3 class='font-bold text-lg mb-2'>{$buku['judul']}</h3>
                            <p class='text-gray-600 mb-2'>Oleh: {$buku['penulis_name']}</p>
                            <div class='flex justify-between items-center'>
                                <span class='bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm'>{$buku['kategori']}</span>
                                <div class='flex items-center'>
                                    <span class='text-yellow-500'>★</span>
                                    <span class='ml-1 text-gray-700'>{$rating}</span>
                                </div>
                            </div>
                            <a href='book_detail.php?id={$buku['buku_id']}' class='mt-4 block text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition'>Lihat Detail</a>
                        </div>
                    </div>
                    ";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
</body>
</html>