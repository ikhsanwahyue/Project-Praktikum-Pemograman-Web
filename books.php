<?php include 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Buku - E-Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header/Navbar -->
    <?php include 'components/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Koleksi Buku</h1>
        
        <!-- Filter Section -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-gray-700 mb-2">Cari Buku</label>
                    <input type="text" name="search" placeholder="Judul atau penulis..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <!-- Kategori -->
                <div>
                    <label class="block text-gray-700 mb-2">Kategori</label>
                    <select name="kategori" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                
                <!-- Sort By -->
                <div>
                    <label class="block text-gray-700 mb-2">Urutkan</label>
                    <select name="sort" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="terbaru" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'terbaru') ? 'selected' : ''; ?>>Terbaru</option>
                        <option value="terlama" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'terlama') ? 'selected' : ''; ?>>Terlama</option>
                        <option value="rating" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating') ? 'selected' : ''; ?>>Rating Tertinggi</option>
                        <option value="judul" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'judul') ? 'selected' : ''; ?>>Judul A-Z</option>
                    </select>
                </div>
                
                <!-- Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">Terapkan Filter</button>
                </div>
            </form>
        </div>
        
        <!-- Books Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            // Build query berdasarkan filter
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
            
            // Order by
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
            
            if ($result->num_rows > 0) {
                while ($buku = $result->fetch_assoc()) {
                    $rating = $buku['avg_rating'] ? round($buku['avg_rating'], 1) : 0;
                    echo "
                    <div class='bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition'>
                        <img src='uploads/covers/{$buku['cover']}' alt='{$buku['judul']}' class='w-full h-48 object-cover'>
                        <div class='p-4'>
                            <h3 class='font-bold text-lg mb-2'>{$buku['judul']}</h3>
                            <p class='text-gray-600 mb-2'>Oleh: {$buku['penulis_name']}</p>
                            <div class='flex justify-between items-center mb-4'>
                                <span class='bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm'>{$buku['kategori']}</span>
                                <div class='flex items-center'>
                                    <span class='text-yellow-500'>★</span>
                                    <span class='ml-1 text-gray-700'>{$rating}</span>
                                </div>
                            </div>
                            <a href='book_detail.php?id={$buku['buku_id']}' class='block text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition'>Lihat Detail</a>
                        </div>
                    </div>
                    ";
                }
            } else {
                echo "<p class='col-span-full text-center text-gray-500 py-8'>Tidak ada buku yang ditemukan.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
</body>
</html>