<?php 
include 'config/database.php';

// Ambil ID buku dari URL
$buku_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($buku_id === 0) {
    header("Location: books.php");
    exit();
}

// Query untuk mendapatkan detail buku
$query = "
    SELECT b.*, p.name as penulis_name, p.email as penulis_email, 
           k.kategori, k.deskripsi as kategori_deskripsi,
           AVG(c.rating) as avg_rating,
           COUNT(c.comment_id) as jumlah_ulasan
    FROM buku b
    JOIN penulis p ON b.penulis_id = p.penulis_id
    JOIN kategori k ON b.kategori_id = k.kategori_id
    LEFT JOIN comments c ON b.buku_id = c.buku_id
    WHERE b.buku_id = ?
    GROUP BY b.buku_id
";

$stmt = $connection->prepare($query);
$stmt->bind_param("i", $buku_id);
$stmt->execute();
$result = $stmt->get_result();
$buku = $result->fetch_assoc();

if (!$buku) {
    header("Location: books.php");
    exit();
}

// Cek apakah user sudah login
$user = getCurrentUser($connection);

// Cek apakah buku sudah difavoritkan oleh user
$is_favorited = false;
$is_saved = false;

if ($user) {
    // Cek favorit
    $favorit_query = "SELECT * FROM buku_favorit WHERE buku_id = ? AND user_id = ?";
    $stmt = $connection->prepare($favorit_query);
    $stmt->bind_param("ii", $buku_id, $user['user_id']);
    $stmt->execute();
    $is_favorited = $stmt->get_result()->num_rows > 0;
    
    // Cek simpan
    $simpan_query = "SELECT * FROM simpan_buku WHERE buku_id = ? AND user_id = ?";
    $stmt = $connection->prepare($simpan_query);
    $stmt->bind_param("ii", $buku_id, $user['user_id']);
    $stmt->execute();
    $is_saved = $stmt->get_result()->num_rows > 0;
}

// Proses form komentar
if (isset($_POST['submit_comment']) && $user) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    
    // Validasi rating
    if ($rating < 1 || $rating > 5) {
        $comment_error = "Rating harus antara 1-5 bintang";
    } elseif (empty($comment)) {
        $comment_error = "Komentar tidak boleh kosong";
    } else {
        // Cek apakah user sudah memberikan komentar untuk buku ini
        $check_comment_query = "SELECT * FROM comments WHERE user_id = ? AND buku_id = ?";
        $stmt = $connection->prepare($check_comment_query);
        $stmt->bind_param("ii", $user['user_id'], $buku_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $comment_error = "Anda sudah memberikan ulasan untuk buku ini";
        } else {
            $insert_query = "INSERT INTO comments (user_id, buku_id, rating, comment) VALUES (?, ?, ?, ?)";
            $stmt = $connection->prepare($insert_query);
            $stmt->bind_param("iiis", $user['user_id'], $buku_id, $rating, $comment);
            
            if ($stmt->execute()) {
                $comment_success = "Ulasan berhasil ditambahkan!";
                // Refresh halaman untuk menampilkan komentar baru
                header("Location: book_detail.php?id=$buku_id");
                exit();
            } else {
                $comment_error = "Terjadi kesalahan saat menambahkan ulasan";
            }
        }
    }
}

// Proses favorit
if (isset($_POST['toggle_favorite']) && $user) {
    if ($is_favorited) {
        $delete_query = "DELETE FROM buku_favorit WHERE buku_id = ? AND user_id = ?";
        $stmt = $connection->prepare($delete_query);
        $stmt->bind_param("ii", $buku_id, $user['user_id']);
        $stmt->execute();
    } else {
        $insert_query = "INSERT INTO buku_favorit (buku_id, user_id) VALUES (?, ?)";
        $stmt = $connection->prepare($insert_query);
        $stmt->bind_param("ii", $buku_id, $user['user_id']);
        $stmt->execute();
    }
    
    header("Location: book_detail.php?id=$buku_id");
    exit();
}

// Proses simpan
if (isset($_POST['toggle_save']) && $user) {
    if ($is_saved) {
        $delete_query = "DELETE FROM simpan_buku WHERE buku_id = ? AND user_id = ?";
        $stmt = $connection->prepare($delete_query);
        $stmt->bind_param("ii", $buku_id, $user['user_id']);
        $stmt->execute();
    } else {
        $insert_query = "INSERT INTO simpan_buku (buku_id, user_id) VALUES (?, ?)";
        $stmt = $connection->prepare($insert_query);
        $stmt->bind_param("ii", $buku_id, $user['user_id']);
        $stmt->execute();
    }
    
    header("Location: book_detail.php?id=$buku_id");
    exit();
}

// Ambil komentar untuk buku ini
$comments_query = "
    SELECT c.*, u.name as user_name, u.foto_profil
    FROM comments c
    JOIN user u ON c.user_id = u.user_id
    WHERE c.buku_id = ?
    ORDER BY c.disimpan_pada DESC
";
$stmt = $connection->prepare($comments_query);
$stmt->bind_param("i", $buku_id);
$stmt->execute();
$comments_result = $stmt->get_result();

// Ambil buku rekomendasi (buku dengan kategori yang sama)
$rekomendasi_query = "
    SELECT b.*, p.name as penulis_name, k.kategori,
           AVG(c.rating) as avg_rating
    FROM buku b
    JOIN penulis p ON b.penulis_id = p.penulis_id
    JOIN kategori k ON b.kategori_id = k.kategori_id
    LEFT JOIN comments c ON b.buku_id = c.buku_id
    WHERE b.kategori_id = ? AND b.buku_id != ?
    GROUP BY b.buku_id
    ORDER BY avg_rating DESC, b.dibuat_pada DESC
    LIMIT 4
";
$stmt = $connection->prepare($rekomendasi_query);
$stmt->bind_param("ii", $buku['kategori_id'], $buku_id);
$stmt->execute();
$rekomendasi_result = $stmt->get_result();

$rating = $buku['avg_rating'] ? round($buku['avg_rating'], 1) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($buku['judul']); ?> - E-Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header/Navbar -->
    <?php include 'components/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="books.php" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Koleksi Buku</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?php echo htmlspecialchars($buku['judul']); ?></span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Book Header -->
            <div class="md:flex">
                <!-- Book Cover -->
                <div class="md:w-1/3 p-8">
                    <div class="relative">
                        <img src="uploads/covers/<?php echo $buku['cover'] ?: 'default-cover.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($buku['judul']); ?>" 
                             class="w-full rounded-lg shadow-lg object-cover max-w-xs mx-auto">
                        <?php if ($rating >= 4.0): ?>
                            <span class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-star mr-1"></i>Top Rated
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Action Buttons Mobile -->
                    <div class="flex space-x-2 mt-4 md:hidden">
                        <?php if ($buku['file_buku']): ?>
                            <a href="uploads/books/<?php echo $buku['file_buku']; ?>" 
                               class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-semibold text-center"
                               download>
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user): ?>
                            <form method="POST" class="flex-1">
                                <button type="submit" name="toggle_favorite" 
                                        class="w-full <?php echo $is_favorited ? 'bg-red-600 hover:bg-red-700' : 'bg-pink-600 hover:bg-pink-700'; ?> 
                                               text-white px-4 py-2 rounded-lg transition font-semibold">
                                    <i class="fas fa-heart mr-2"></i><?php echo $is_favorited ? 'Favorit' : 'Favorit'; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Book Info -->
                <div class="md:w-2/3 p-8">
                    <h1 class="text-3xl font-bold mb-4 text-gray-800"><?php echo htmlspecialchars($buku['judul']); ?></h1>
                    
                    <!-- Rating -->
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-500 mr-2 text-lg">
                            <?php
                            $full_stars = floor($rating);
                            $has_half_star = ($rating - $full_stars) >= 0.5;
                            
                            for ($i = 0; $i < 5; $i++) {
                                if ($i < $full_stars) {
                                    echo '<i class="fas fa-star"></i>';
                                } else if ($i == $full_stars && $has_half_star) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <span class="text-gray-700 font-semibold"><?php echo $rating; ?>/5</span>
                        <span class="mx-2 text-gray-400">•</span>
                        <span class="text-gray-600"><?php echo $buku['jumlah_ulasan']; ?> ulasan</span>
                    </div>
                    
                    <!-- Book Metadata -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-user-edit text-blue-600 w-6"></i>
                            <span class="font-semibold text-gray-700 w-24">Penulis:</span>
                            <span class="text-gray-800"><?php echo htmlspecialchars($buku['penulis_name']); ?></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-tag text-blue-600 w-6"></i>
                            <span class="font-semibold text-gray-700 w-24">Kategori:</span>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                <?php echo htmlspecialchars($buku['kategori']); ?>
                            </span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-building text-blue-600 w-6"></i>
                            <span class="font-semibold text-gray-700 w-24">Penerbit:</span>
                            <span class="text-gray-800"><?php echo htmlspecialchars($buku['penerbit']); ?></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-blue-600 w-6"></i>
                            <span class="font-semibold text-gray-700 w-24">Terbit:</span>
                            <span class="text-gray-800"><?php echo date('d F Y', strtotime($buku['terbit_pada'])); ?></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-blue-600 w-6"></i>
                            <span class="font-semibold text-gray-700 w-24">Format:</span>
                            <span class="text-gray-800">
                                <?php 
                                if ($buku['file_buku']) {
                                    $ext = pathinfo($buku['file_buku'], PATHINFO_EXTENSION);
                                    echo strtoupper($ext) . ' File';
                                } else {
                                    echo 'Tidak tersedia';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons Desktop -->
                    <div class="flex space-x-4 mb-6">
                        <?php if ($buku['file_buku']): ?>
                            <a href="uploads/books/<?php echo $buku['file_buku']; ?>" 
                               class="flex items-center bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold"
                               download>
                                <i class="fas fa-download mr-2"></i>Download Buku
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user): ?>
                            <form method="POST" class="inline">
                                <button type="submit" name="toggle_favorite" 
                                        class="flex items-center <?php echo $is_favorited ? 'bg-red-600 hover:bg-red-700' : 'bg-pink-600 hover:bg-pink-700'; ?> 
                                               text-white px-6 py-3 rounded-lg transition font-semibold">
                                    <i class="fas fa-heart mr-2"></i>
                                    <?php echo $is_favorited ? 'Hapus Favorit' : 'Tambah Favorit'; ?>
                                </button>
                            </form>
                            
                            <form method="POST" class="inline">
                                <button type="submit" name="toggle_save" 
                                        class="flex items-center <?php echo $is_saved ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700'; ?> 
                                               text-white px-6 py-3 rounded-lg transition font-semibold">
                                    <i class="fas fa-bookmark mr-2"></i>
                                    <?php echo $is_saved ? 'Hapus Simpan' : 'Simpan Buku'; ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="login.php?redirect=book_detail.php?id=<?php echo $buku_id; ?>" 
                               class="flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Favorit/Simpan
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600"><?php echo $buku['jumlah_ulasan']; ?></div>
                            <div class="text-sm text-gray-600">Ulasan</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                <?php
                                $favorit_count_query = "SELECT COUNT(*) as total FROM buku_favorit WHERE buku_id = ?";
                                $stmt = $connection->prepare($favorit_count_query);
                                $stmt->bind_param("i", $buku_id);
                                $stmt->execute();
                                $favorit_count = $stmt->get_result()->fetch_assoc()['total'];
                                echo $favorit_count;
                                ?>
                            </div>
                            <div class="text-sm text-gray-600">Favorit</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                <?php
                                $simpan_count_query = "SELECT COUNT(*) as total FROM simpan_buku WHERE buku_id = ?";
                                $stmt = $connection->prepare($simpan_count_query);
                                $stmt->bind_param("i", $buku_id);
                                $stmt->execute();
                                $simpan_count = $stmt->get_result()->fetch_assoc()['total'];
                                echo $simpan_count;
                                ?>
                            </div>
                            <div class="text-sm text-gray-600">Disimpan</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Book Description -->
            <div class="border-t border-gray-200 p-8">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Deskripsi Buku</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-700 leading-relaxed text-lg">
                        <?php echo nl2br(htmlspecialchars($buku['deskripsi'])); ?>
                    </p>
                </div>
            </div>
            
            <!-- Comments Section -->
            <div class="border-t border-gray-200 p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">
                    <i class="fas fa-comments mr-2"></i>Ulasan Pembaca
                    <span class="text-lg font-normal text-gray-600">(<?php echo $buku['jumlah_ulasan']; ?> ulasan)</span>
                </h2>
                
                <?php if ($user): ?>
                <!-- Add Comment Form -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-semibold mb-4 text-blue-800">
                        <i class="fas fa-edit mr-2"></i>Tambah Ulasan Anda
                    </h3>
                    
                    <?php if (isset($comment_error)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <i class="fas fa-exclamation-triangle mr-2"></i><?php echo $comment_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($comment_success)): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            <i class="fas fa-check-circle mr-2"></i><?php echo $comment_success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2 font-semibold">Rating</label>
                            <div class="flex space-x-1" id="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="cursor-pointer transform hover:scale-110 transition">
                                        <input type="radio" name="rating" value="<?php echo $i; ?>" required 
                                               class="sr-only rating-input">
                                        <span class="text-3xl text-gray-300 hover:text-yellow-400 rating-star">★</span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Pilih rating 1-5 bintang</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2 font-semibold">Komentar</label>
                            <textarea name="comment" rows="4" required 
                                      placeholder="Bagikan pengalaman Anda membaca buku ini..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        
                        <button type="submit" name="submit_comment" 
                                class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Ulasan
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8 text-center">
                    <i class="fas fa-exclamation-circle text-yellow-500 text-3xl mb-3"></i>
                    <p class="text-yellow-800 text-lg">
                        <a href="login.php?redirect=book_detail.php?id=<?php echo $buku_id; ?>" 
                           class="text-blue-600 hover:underline font-semibold">Login</a> 
                        untuk menambahkan ulasan dan rating.
                    </p>
                </div>
                <?php endif; ?>
                
                <!-- Comments List -->
                <div class="space-y-6">
                    <?php
                    if ($comments_result->num_rows > 0) {
                        while ($comment = $comments_result->fetch_assoc()) {
                            $comment_date = date('d M Y', strtotime($comment['disimpan_pada']));
                            $comment_time = date('H:i', strtotime($comment['disimpan_pada']));
                            ?>
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                                <div class="flex items-start mb-4">
                                    <img src="uploads/profiles/<?php echo $comment['foto_profil'] ?: 'default.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($comment['user_name']); ?>" 
                                         class="w-12 h-12 rounded-full mr-4 object-cover border-2 border-blue-200">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($comment['user_name']); ?></h4>
                                                <div class="flex items-center mt-1">
                                                    <div class="flex text-yellow-500 mr-2">
                                                        <?php
                                                        for ($i = 0; $i < 5; $i++) {
                                                            if ($i < $comment['rating']) {
                                                                echo '<i class="fas fa-star text-sm"></i>';
                                                            } else {
                                                                echo '<i class="far fa-star text-sm"></i>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <span class="text-sm text-gray-500"><?php echo $comment['rating']; ?>/5</span>
                                                </div>
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                <?php echo $comment_date; ?> • <?php echo $comment_time; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                            </div>
                            <?php
                        }
                    } else {
                        echo '
                        <div class="text-center py-12">
                            <i class="fas fa-comment-slash text-gray-400 text-5xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">Belum ada ulasan</h3>
                            <p class="text-gray-500">Jadilah yang pertama memberikan ulasan untuk buku ini!</p>
                        </div>
                        ';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Recommended Books -->
        <?php if ($rekomendasi_result->num_rows > 0): ?>
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">
                <i class="fas fa-bookmark mr-2"></i>Buku Serupa
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                while ($rekomendasi = $rekomendasi_result->fetch_assoc()) {
                    $rec_rating = $rekomendasi['avg_rating'] ? round($rekomendasi['avg_rating'], 1) : 0;
                    ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition transform hover:-translate-y-1">
                        <img src="uploads/covers/<?php echo $rekomendasi['cover'] ?: 'default-cover.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($rekomendasi['judul']); ?>" 
                             class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2 text-gray-800 line-clamp-2"><?php echo htmlspecialchars($rekomendasi['judul']); ?></h3>
                            <p class="text-gray-600 text-sm mb-2">Oleh: <?php echo htmlspecialchars($rekomendasi['penulis_name']); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs"><?php echo htmlspecialchars($rekomendasi['kategori']); ?></span>
                                <div class="flex items-center">
                                    <span class="text-yellow-500 text-sm">★</span>
                                    <span class="ml-1 text-gray-700 text-sm"><?php echo $rec_rating; ?></span>
                                </div>
                            </div>
                            <a href="book_detail.php?id=<?php echo $rekomendasi['buku_id']; ?>" 
                               class="mt-4 block text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition text-sm font-semibold">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <script>
        // Rating stars interaction
        document.addEventListener('DOMContentLoaded', function() {
            const ratingStars = document.querySelectorAll('.rating-star');
            const ratingInputs = document.querySelectorAll('.rating-input');
            
            ratingInputs.forEach((input, index) => {
                input.addEventListener('change', function() {
                    // Reset all stars
                    ratingStars.forEach(star => {
                        star.classList.remove('text-yellow-500');
                        star.classList.add('text-gray-300');
                    });
                    
                    // Color stars up to selected rating
                    for (let i = 0; i <= index; i++) {
                        ratingStars[i].classList.remove('text-gray-300');
                        ratingStars[i].classList.add('text-yellow-500');
                    }
                });
                
                // Hover effect
                input.addEventListener('mouseenter', function() {
                    for (let i = 0; i <= index; i++) {
                        ratingStars[i].classList.add('text-yellow-300');
                    }
                });
                
                input.addEventListener('mouseleave', function() {
                    for (let i = 0; i <= index; i++) {
                        ratingStars[i].classList.remove('text-yellow-300');
                        if (i <= document.querySelector('input[name="rating"]:checked')?.value - 1) {
                            ratingStars[i].classList.add('text-yellow-500');
                        } else {
                            ratingStars[i].classList.add('text-gray-300');
                        }
                    }
                });
            });
        });
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .prose {
            max-width: none;
        }
        
        .prose p {
            margin-bottom: 1em;
            line-height: 1.7;
        }
    </style>
</body>
</html>