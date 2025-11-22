<?php 
// Pastikan sesi dimulai dan $connection tersedia di file database.php
include 'config/database.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Buku - Azizi.io E-Book</title>
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
        .border-soft-blue { border-color: #6f76fd; }
        .text-primary-dark { color: #192038; }

        /* Styling Card Buku */
        .book-card-item {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #2d3250; /* Border halus */
        }
        .book-card-item:hover {
            transform: translateY(-4px); 
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            border-color: #6f76fd; /* Border berwarna saat hover */
        }
    </style>
</head>
<body class="bg-primary-dark text-white">
    <?php include 'components/header.php'; ?>

    <div class="container mx-auto px-4 py-12">
        <h1 class="text-4xl font-extrabold mb-4 text-highlight flex items-center">
            <i class="fas fa-book-reader mr-3"></i> Jelajahi Koleksi Buku
        </h1>
        <p class="text-lg text-gray-400 mb-8">Temukan buku yang Anda cari menggunakan filter di bawah ini.</p>
        
        <div class="bg-dark-accent p-6 rounded-xl shadow-lg mb-10 border border-gray-700">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">Cari Buku</label>
                    <input type="text" name="search" placeholder="Judul atau penulis..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                           class="w-full px-4 py-3 border border-gray-600 bg-primary-dark rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-highlight focus:border-highlight transition">
                </div>
                
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">Kategori</label>
                    <select name="kategori" class="w-full px-4 py-3 border border-gray-600 bg-primary-dark rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-highlight focus:border-highlight transition">
                        <option value="">Semua Kategori</option>
                        <?php
                        $kategori_query = "SELECT * FROM kategori ORDER BY kategori";
                        $kategori_result = $connection->query($kategori_query);
                        while ($kategori = $kategori_result->fetch_assoc()) {
                            $selected = (isset($_GET['kategori']) && $_GET['kategori'] == $kategori['kategori_id']) ? 'selected' : '';
                            echo "<option value='{$kategori['kategori_id']}' $selected>{$kategori['kategori']}</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-gray-300 mb-2 font-medium">Urutkan</label>
                    <select name="sort" class="w-full px-4 py-3 border border-gray-600 bg-primary-dark rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-highlight focus:border-highlight transition">
                        <option value="terbaru" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'terbaru') ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="terlama" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'terlama') ? 'selected' : ''; ?>>Terlama</option>
                        <option value="rating" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating') ? 'selected' : ''; ?>>Rating Tertinggi</option>
                        <option value="judul" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'judul') ? 'selected' : ''; ?>>Judul A-Z</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-soft-blue text-white py-3 px-4 rounded-lg font-semibold hover:bg-soft-blue/90 transition shadow-md">
                        <i class="fas fa-filter mr-2"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-6">
            <?php
            // LOGIKA PHP DIBIARKAN SAMA PERSIS (termasuk pembuatan query dan execution)
            $where_conditions = [];
            $params = [];
            $types = "";
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $where_conditions[] = "(b.judul LIKE ? OR p.name LIKE ?)";
                $search_term = "%{$_GET['search']}%";
                $params[] = $search_term;
                $params[] = $search_term;
                $types .= "ss";
            }
            
            if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
                $where_conditions[] = "b.kategori_id = ?";
                $params[] = $_GET['kategori'];
                $types .= "i";
            }
            
            $where_clause = "";
            if (!empty($where_conditions)) {
                $where_clause = "WHERE " . implode(" AND ", $where_conditions);
            }
            
            $order_by = "b.dibuat_pada DESC";
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'terlama':
                        $order_by = "b.dibuat_pada ASC";
                        break;
                    case 'rating':
                        $order_by = "avg_rating DESC";
                        break;
                    case 'judul':
                        $order_by = "b.judul ASC";
                        break;
                }
            }
            
            $query = "
                SELECT b.*, p.name as penulis_name, k.kategori,
                        AVG(c.rating) as avg_rating,
                        COUNT(DISTINCT bf.favorit_id) as jumlah_favorit
                FROM buku b
                JOIN penulis p ON b.penulis_id = p.penulis_id
                JOIN kategori k ON b.kategori_id = k.kategori_id
                LEFT JOIN comments c ON b.buku_id = c.buku_id
                LEFT JOIN buku_favorit bf ON b.buku_id = bf.buku_id
                $where_clause
                GROUP BY b.buku_id
                ORDER BY $order_by
            ";
            
            if (!empty($params)) {
                $stmt = $connection->prepare($query);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $connection->query($query);
            }
            // END LOGIKA PHP
            
            if ($result->num_rows > 0) {
                while ($buku = $result->fetch_assoc()) {
                    $rating = $buku['avg_rating'] ? round($buku['avg_rating'], 1) : 0;
                    echo "
                    <div class='book-card-item bg-dark-accent text-white'>
                        <img src='uploads/covers/{$buku['cover']}' alt='{$buku['judul']}' class='w-full h-56 object-cover rounded-t-xl'>
                        
                        <div class='p-4'>
                            <h3 class='font-bold text-base mb-1 line-clamp-2'>{$buku['judul']}</h3>
                            <p class='text-gray-400 text-xs mb-3'>Oleh: {$buku['penulis_name']}</p>
                            
                            <div class='flex justify-between items-center mb-4'>
                                <span class='bg-highlight text-primary-dark px-2 py-0.5 rounded-full text-[10px] font-medium'>{$buku['kategori']}</span>
                                
                                <div class='flex items-center text-sm'>
                                    <i class='fas fa-star text-highlight'></i>
                                    <span class='ml-1 text-white font-semibold'>{$rating}</span>
                                </div>
                            </div>
                            <a href='book_detail.php?id={$buku['buku_id']}' class='block text-center bg-soft-blue text-white py-2 rounded-full font-semibold hover:bg-soft-blue/90 transition text-sm'>
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                    ";
                }
            } else {
                echo "<p class='col-span-full text-center text-gray-500 py-12'>
                        <i class='fas fa-exclamation-circle text-soft-blue mr-2'></i> Tidak ada buku yang ditemukan berdasarkan kriteria Anda.
                      </p>";
            }
            ?>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html>