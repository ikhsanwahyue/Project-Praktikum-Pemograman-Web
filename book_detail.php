<?php 
include 'config/database.php';

// Pastikan fungsi ini ada, contoh sederhana:
if (!function_exists('getCurrentUser')) {
    function getCurrentUser($connection) {
        // Logika sederhana untuk mendapatkan user yang login
        // Biasanya melibatkan $_SESSION['user_id'] dan query ke DB
        return isset($_SESSION['user_id']) ? ['user_id' => $_SESSION['user_id'], 'name' => 'John Doe', 'foto_profil' => 'default.png'] : false;
    }
}
// Ambil ID buku dari URL
$buku_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($buku_id === 0) {
    header("Location: books.php");
    exit();
}

// Query untuk mendapatkan detail buku (Gabungan Query Asli Anda)
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

// Proses form komentar (Disesuaikan agar user hanya bisa memberi 1 komentar)
if (isset($_POST['submit_comment']) && $user) {
    // ... (Logika komentar Anda yang sudah benar) ...
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
                // Redirect setelah sukses untuk mencegah resubmission form
                header("Location: book_detail.php?id=$buku_id&comment_success=true");
                exit();
            } else {
                $comment_error = "Terjadi kesalahan saat menambahkan ulasan";
            }
        }
    }
}

// Tangani redirect sukses komentar
if (isset($_GET['comment_success'])) {
    $comment_success = "Ulasan berhasil ditambahkan! Halaman diperbarui.";
}

// Proses favorit/simpan (Logika sudah benar, tinggal disisipkan)

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
    <title><?php echo htmlspecialchars($buku['judul']); ?> - Azizi.io</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Palet Warna Kustom (WAJIB ADA) */
        .bg-primary-dark { background-color: #192038; } 
        .bg-soft-blue { background-color: #6f76fd; }
        .bg-dark-accent { background-color: #2d3250; }
        .bg-highlight { background-color: #f6b17a; }
        .text-soft-blue { color: #6f76fd; }
        .text-highlight { color: #f6b17a; }
        
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
        
        /* Custom Input Styling */
        .form-textarea-dark {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #4b5563; /* border-gray-600 */
            background-color: #192038; /* bg-primary-dark */
            border-radius: 0.5rem; /* rounded-lg */
            color: white;
            transition: all 0.2s;
            outline: none;
        }
        .form-textarea-dark:focus {
            box-shadow: 0 0 0 2px rgba(111, 118, 253, 0.5); /* shadow ring soft-blue */
            border-color: #6f76fd; /* border soft-blue */
        }
    </style>
</head>
<body class="bg-primary-dark text-white">
    <?php include 'components/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-gray-400">
                <li class="inline-flex items-center">
                    <a href="index.php" class="inline-flex items-center font-medium hover:text-soft-blue">
                        <i class="fas fa-home mr-2"></i>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right mx-2 text-gray-500"></i>
                        <a href="books.php" class="ml-1 font-medium hover:text-soft-blue md:ml-2">Koleksi Buku</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right mx-2 text-gray-500"></i>
                        <span class="ml-1 font-medium text-gray-300 md:ml-2"><?php echo htmlspecialchars($buku['judul']); ?></span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-dark-accent rounded-xl shadow-2xl overflow-hidden border border-gray-700">
            <div class="md:flex">
                <div class="md:w-1/3 p-8 border-r border-gray-700">
                    <div class="relative">
                        <img src="uploads/covers/<?php echo $buku['cover'] ?: 'default-cover.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($buku['judul']); ?>" 
                             class="w-full rounded-lg shadow-xl object-cover max-w-xs mx-auto transform hover:scale-[1.02] transition duration-300">
                        <?php if ($rating >= 4.0): ?>
                            <span class="absolute top-2 right-2 bg-highlight text-dark-accent px-2 py-1 rounded-full text-xs font-bold shadow-md">
                                <i class="fas fa-star mr-1"></i>Top Rated
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 p-4 mt-6 bg-primary-dark rounded-lg md:hidden text-center text-gray-300">
                        <div>
                            <div class="text-xl font-bold text-soft-blue"><?php echo $buku['jumlah_ulasan']; ?></div>
                            <div class="text-xs">Ulasan</div>
                        </div>
                        <div>
                            <?php
                            $favorit_count_query = "SELECT COUNT(*) as total FROM buku_favorit WHERE buku_id = ?";
                            $stmt = $connection->prepare($favorit_count_query);
                            $stmt->bind_param("i", $buku_id);
                            $stmt->execute();
                            $favorit_count = $stmt->get_result()->fetch_assoc()['total'];
                            ?>
                            <div class="text-xl font-bold text-red-400"><?php echo $favorit_count; ?></div>
                            <div class="text-xs">Favorit</div>
                        </div>
                        <div>
                            <?php
                            $simpan_count_query = "SELECT COUNT(*) as total FROM simpan_buku WHERE buku_id = ?";
                            $stmt = $connection->prepare($simpan_count_query);
                            $stmt->bind_param("i", $buku_id);
                            $stmt->execute();
                            $simpan_count = $stmt->get_result()->fetch_assoc()['total'];
                            ?>
                            <div class="text-xl font-bold text-yellow-400"><?php echo $simpan_count; ?></div>
                            <div class="text-xs">Disimpan</div>
                        </div>
                    </div>

                    <div class="flex space-x-2 mt-4 md:hidden">
                        <?php if ($buku['file_buku']): ?>
                            <a href="uploads/books/<?php echo $buku['file_buku']; ?>" 
                               class="flex-1 bg-highlight text-primary-dark px-4 py-2 rounded-lg hover:bg-highlight/90 transition font-bold text-center text-sm"
                               download>
                                <i class="fas fa-download mr-1"></i>Download
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user): ?>
                            <form method="POST" class="flex-1">
                                <button type="submit" name="toggle_favorite" 
                                        class="w-full <?php echo $is_favorited ? 'bg-red-600 hover:bg-red-700' : 'bg-pink-600 hover:bg-pink-700'; ?> 
                                               text-white px-4 py-2 rounded-lg transition font-semibold text-sm">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </form>
                            <form method="POST" class="flex-1">
                                <button type="submit" name="toggle_save" 
                                        class="w-full <?php echo $is_saved ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-soft-blue hover:bg-soft-blue/90'; ?> 
                                               text-white px-4 py-2 rounded-lg transition font-semibold text-sm">
                                    <i class="fas fa-bookmark"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                </div>
                
                <div class="md:w-2/3 p-8">
                    <h1 class="text-4xl font-extrabold mb-4 text-white"><?php echo htmlspecialchars($buku['judul']); ?></h1>
                    
                    <div class="flex items-center mb-6">
                        <div class="flex text-highlight mr-3 text-2xl">
                            <?php
                            $full_stars = floor($rating);
                            $has_half_star = ($rating - $full_stars) >= 0.5;
                            
                            for ($i = 0; $i < 5; $i++) {
                                if ($i < $full_stars) {
                                    echo '<i class="fas fa-star"></i>';
                                } else if ($i == $full_stars && $has_half_star) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star text-gray-600"></i>';
                                }
                            }
                            ?>
                        </div>
                        <span class="text-highlight text-2xl font-bold mr-2"><?php echo $rating; ?></span>
                        <span class="text-gray-400">/5</span>
                        <span class="mx-3 text-gray-600">•</span>
                        <span class="text-gray-400 text-sm">(<?php echo $buku['jumlah_ulasan']; ?> ulasan)</span>
                    </div>
                    
                    <div class="space-y-4 mb-8 text-gray-300">
                        <div class="flex items-center">
                            <i class="fas fa-user-edit text-soft-blue w-6"></i>
                            <span class="font-semibold w-24">Penulis:</span>
                            <span class="text-white hover:text-highlight transition"><?php echo htmlspecialchars($buku['penulis_name']); ?></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-tag text-soft-blue w-6"></i>
                            <span class="font-semibold w-24">Kategori:</span>
                            <span class="bg-primary-dark text-highlight px-3 py-1 rounded-full text-sm font-medium border border-soft-blue/50">
                                <?php echo htmlspecialchars($buku['kategori']); ?>
                            </span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-building text-soft-blue w-6"></i>
                            <span class="font-semibold w-24">Penerbit:</span>
                            <span class="text-white"><?php echo htmlspecialchars($buku['penerbit']); ?></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-soft-blue w-6"></i>
                            <span class="font-semibold w-24">Terbit:</span>
                            <span class="text-white"><?php echo date('d F Y', strtotime($buku['terbit_pada'])); ?></span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-soft-blue w-6"></i>
                            <span class="font-semibold w-24">Format:</span>
                            <span class="text-white">
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
                    
                    <div class="hidden md:flex space-x-4 mb-6">
                        <?php if ($buku['file_buku']): ?>
                            <a href="uploads/books/<?php echo $buku['file_buku']; ?>" 
                               class="flex items-center bg-highlight text-primary-dark px-6 py-3 rounded-lg hover:bg-highlight/90 transition font-bold shadow-md shadow-highlight/20"
                               download>
                                <i class="fas fa-download mr-2"></i>Download Buku
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user): ?>
                            <form method="POST" class="inline">
                                <button type="submit" name="toggle_favorite" 
                                        class="flex items-center <?php echo $is_favorited ? 'bg-red-600 hover:bg-red-700' : 'bg-pink-600 hover:bg-pink-700'; ?> 
                                               text-white px-6 py-3 rounded-lg transition font-semibold shadow-md shadow-pink-600/20">
                                    <i class="fas fa-heart mr-2"></i>
                                    <?php echo $is_favorited ? 'Hapus Favorit' : 'Tambah Favorit'; ?>
                                </button>
                            </form>
                            
                            <form method="POST" class="inline">
                                <button type="submit" name="toggle_save" 
                                        class="flex items-center <?php echo $is_saved ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-soft-blue hover:bg-soft-blue/90'; ?> 
                                               text-white px-6 py-3 rounded-lg transition font-semibold shadow-md shadow-soft-blue/20">
                                    <i class="fas fa-bookmark mr-2"></i>
                                    <?php echo $is_saved ? 'Hapus Simpan' : 'Simpan Buku'; ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="login.php?redirect=book_detail.php?id=<?php echo $buku_id; ?>" 
                               class="flex items-center bg-soft-blue text-white px-6 py-3 rounded-lg hover:bg-soft-blue/90 transition font-semibold">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Aksi
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 p-8">
                <h2 class="text-2xl font-bold mb-4 text-highlight">Deskripsi Buku</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-300 leading-relaxed text-lg">
                        <?php echo nl2br(htmlspecialchars($buku['deskripsi'])); ?>
                    </p>
                </div>
            </div>
            
            <div class="border-t border-gray-700 p-8">
                <h2 class="text-2xl font-bold mb-6 text-highlight">
                    <i class="fas fa-comments mr-2 text-soft-blue"></i>Ulasan Pembaca
                    <span class="text-lg font-normal text-gray-400">(<?php echo $buku['jumlah_ulasan']; ?> ulasan)</span>
                </h2>
                
                <?php if ($user): ?>
                <div class="bg-primary-dark border border-gray-700 rounded-xl p-6 mb-10 shadow-lg">
                    <h3 class="text-xl font-semibold mb-4 text-soft-blue">
                        <i class="fas fa-edit mr-2"></i>Tambah Ulasan Anda
                    </h3>
                    
                    <?php if (isset($comment_error)): ?>
                        <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-4">
                            <i class="fas fa-exclamation-triangle mr-2"></i><?php echo $comment_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($comment_success)): ?>
                        <div class="bg-green-800 border border-green-600 text-green-300 px-4 py-3 rounded-lg mb-4">
                            <i class="fas fa-check-circle mr-2"></i><?php echo $comment_success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block text-gray-300 mb-2 font-semibold">Rating</label>
                            <div class="flex space-x-1" id="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="cursor-pointer transform hover:scale-110 transition">
                                        <input type="radio" name="rating" value="<?php echo $i; ?>" required 
                                                class="sr-only rating-input">
                                        <span class="text-4xl text-gray-600 hover:text-highlight rating-star">★</span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Pilih rating 1-5 bintang</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-300 mb-2 font-semibold">Komentar</label>
                            <textarea name="comment" rows="4" required 
                                            placeholder="Bagikan pengalaman Anda membaca buku ini..."
                                            class="form-textarea-dark"></textarea>
                        </div>
                        
                        <button type="submit" name="submit_comment" 
                                class="bg-soft-blue text-white px-8 py-3 rounded-lg hover:bg-soft-blue/90 transition font-semibold shadow-lg shadow-soft-blue/20">
                            <i class="fas fa-paper-plane mr-2"></i>Kirim Ulasan
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <div class="bg-primary-dark border border-highlight rounded-xl p-6 mb-8 text-center shadow-lg">
                    <i class="fas fa-exclamation-circle text-highlight text-3xl mb-3"></i>
                    <p class="text-gray-300 text-lg">
                        <a href="login.php?redirect=book_detail.php?id=<?php echo $buku_id; ?>" 
                           class="text-soft-blue hover:text-highlight font-semibold hover:underline">Login</a> 
                        untuk menambahkan ulasan dan rating.
                    </p>
                </div>
                <?php endif; ?>
                
                <div class="space-y-6">
                    <?php
                    if ($comments_result->num_rows > 0) {
                        while ($comment = $comments_result->fetch_assoc()) {
                            $comment_date = date('d M Y', strtotime($comment['disimpan_pada']));
                            $comment_time = date('H:i', strtotime($comment['disimpan_pada']));
                            ?>
                            <div class="bg-primary-dark border border-gray-700 rounded-xl p-6 hover:shadow-xl transition duration-300">
                                <div class="flex items-start mb-4">
                                    <img src="uploads/profiles/<?php echo $comment['foto_profil'] ?: 'default.png'; ?>" 
                                         alt="<?php echo htmlspecialchars($comment['user_name']); ?>" 
                                         class="w-12 h-12 rounded-full mr-4 object-cover border-2 border-soft-blue">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold text-white"><?php echo htmlspecialchars($comment['user_name']); ?></h4>
                                                <div class="flex items-center mt-1 text-lg">
                                                    <div class="flex text-highlight mr-2">
                                                        <?php
                                                        for ($i = 0; $i < 5; $i++) {
                                                            if ($i < $comment['rating']) {
                                                                echo '<i class="fas fa-star text-sm"></i>';
                                                            } else {
                                                                echo '<i class="far fa-star text-gray-600 text-sm"></i>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                    <span class="text-sm text-gray-400"><?php echo $comment['rating']; ?>/5</span>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500 pt-1">
                                                <?php echo $comment_date; ?> • <?php echo $comment_time; ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-300 leading-relaxed text-base"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                            </div>
                            <?php
                        }
                    } else {
                        echo '
                        <div class="text-center py-12 bg-primary-dark border border-gray-700 rounded-xl">
                            <i class="fas fa-comment-slash text-gray-500 text-5xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-300 mb-2">Belum ada ulasan</h3>
                            <p class="text-gray-400">Jadilah yang pertama memberikan ulasan untuk buku ini!</p>
                        </div>
                        ';
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php if ($rekomendasi_result->num_rows > 0): ?>
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6 text-highlight">
                <i class="fas fa-rocket mr-2 text-soft-blue"></i>Rekomendasi Serupa
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                <?php
                while ($rekomendasi = $rekomendasi_result->fetch_assoc()) {
                    $rec_rating = $rekomendasi['avg_rating'] ? round($rekomendasi['avg_rating'], 1) : 0;
                    ?>
                    <div class="bg-dark-accent rounded-xl shadow-xl overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1 duration-300 border border-gray-700">
                        <img src="uploads/covers/<?php echo $rekomendasi['cover'] ?: 'default-cover.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($rekomendasi['judul']); ?>" 
                             class="w-full h-56 object-cover object-top">
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2 text-white line-clamp-2 hover:text-soft-blue transition">
                                <a href="book_detail.php?id=<?php echo $rekomendasi['buku_id']; ?>"><?php echo htmlspecialchars($rekomendasi['judul']); ?></a>
                            </h3>
                            <p class="text-gray-400 text-sm mb-2">Oleh: <?php echo htmlspecialchars($rekomendasi['penulis_name']); ?></p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="bg-primary-dark text-highlight px-2 py-1 rounded text-xs font-semibold"><?php echo htmlspecialchars($rekomendasi['kategori']); ?></span>
                                <div class="flex items-center">
                                    <span class="text-highlight text-sm mr-1">★</span>
                                    <span class="text-gray-300 text-sm font-semibold"><?php echo $rec_rating; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'components/footer.php'; ?>

    <script>
        // Rating stars interaction (Disesuaikan untuk tema gelap)
        document.addEventListener('DOMContentLoaded', function() {
            const ratingStars = document.querySelectorAll('.rating-star');
            const ratingInputs = document.querySelectorAll('.rating-input');
            
            ratingInputs.forEach((input, index) => {
                const starElement = ratingStars[index];

                // Function to set star colors based on a rating value
                function setStars(value) {
                    ratingStars.forEach((star, i) => {
                        if (i < value) {
                            star.classList.remove('text-gray-600', 'text-yellow-300');
                            star.classList.add('text-highlight');
                        } else {
                            star.classList.remove('text-highlight', 'text-yellow-300');
                            star.classList.add('text-gray-600');
                        }
                    });
                }

                input.addEventListener('change', function() {
                    setStars(parseInt(this.value));
                });
                
                // Hover effect
                starElement.addEventListener('mouseenter', function() {
                    for (let i = 0; i <= index; i++) {
                        ratingStars[i].classList.remove('text-gray-600');
                        ratingStars[i].classList.add('text-yellow-300'); // Hover effect color
                    }
                });
                
                starElement.addEventListener('mouseleave', function() {
                    // Check if an input is checked
                    const checkedValue = document.querySelector('input[name="rating"]:checked')?.value;
                    if (checkedValue) {
                        setStars(parseInt(checkedValue));
                    } else {
                        // Reset to default gray if nothing is checked
                        ratingStars.forEach(star => {
                            star.classList.remove('text-highlight', 'text-yellow-300');
                            star.classList.add('text-gray-600');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>