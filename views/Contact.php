<?php
// views/Contact.php

// WAJIB: Memulai Sesi
session_start();

// PASTIKAN PATH MODEL SUDAH BENAR
// Path: Project-Praktikum-Pemrograman-Web/models/kontakModels.php
require_once __DIR__ . '/../models/kontakModels.php'; 

$message = '';
$message_class = '';

// Logika Pembuatan Captcha
$captcha_code = substr(md5(microtime()), rand(0, 26), 5);
$_SESSION['captcha_code'] = $captcha_code;

// --- Logika POST Request ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifikasi Captcha (Case-insensitive)
    if (!isset($_POST['captcha']) || strtolower($_POST['captcha']) !== strtolower($_SESSION['captcha_code'])) {
        $message = "Kode Captcha salah.";
        $message_class = "alert-danger";
    } else {
        // Ambil data dan sanitasi
        $nama = trim($_POST['nama_lengkap']);
        $email = trim($_POST['email']);
        $topik = trim($_POST['topik']);
        $pesan = trim($_POST['pesan']);

        try {
            $kontakModel = new KontakModels(); 
            // $kontakModel->insertPesan($nama, $email, $topik, $pesan); // Uncomment jika sudah siap
            $message = "Pesan Anda berhasil terkirim. Terima kasih!";
            $message_class = "alert-success";
        } catch (Exception $e) {
            $message = "Terjadi kesalahan saat mengirim pesan: " . $e->getMessage();
            $message_class = "alert-danger";
        }
    }
    // Setelah submit, regenerasi captcha
    $captcha_code = substr(md5(microtime()), rand(0, 26), 5);
    $_SESSION['captcha_code'] = $captcha_code;
}

// --- INCLUDE HEADER ---
include __DIR__ . '/../includes/headerUser.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hubungi Kami | Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap');
        
        /* Variabel Warna */
        :root {
            --bg-dark: #2d3250; /* Ungu gelap (Background utama) */
            --card-dark: #424769; /* Ungu medium (Card kontak) */
            --input-dark: #53587a; /* Ungu input */
            --accent-orange: #f9b17a; /* Oranye aksen */
            --text-light: #f0f0f5; /* Putih terang */
        }
        
        body {
            font-family: 'Raleway', sans-serif;
            background-color: var(--bg-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--text-light); /* Default text color */
        }
        
        .main-content {
            flex-grow: 1;
            padding-top: 50px; 
            padding-bottom: 50px;
        }
        
        /* === KARTU KONTAK (HERO/ISI) === */
        .contact-card {
            background-color: var(--card-dark);
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }
        
        /* BARIS BARU: Paksa Paragraf di dalam Contact Card menjadi putih */
        .contact-card p {
            color: var(--text-light) !important;
        }
        
        /* === Styling Input Form === */
        .form-label { color: var(--text-light) !important; }
        .form-control {
            background-color: var(--input-dark) !important; 
            border: 1px solid #6b7095 !important;
            color: var(--text-light) !important; 
        }
        .form-control::placeholder {
            color: #c0c0d0 !important;
        }
        .form-control:focus {
            background-color: var(--input-dark) !important;
            border-color: var(--accent-orange) !important;
            box-shadow: 0 0 0 0.25rem rgba(249, 177, 122, 0.5) !important;
        }

        /* Styling Captcha CODE BOX */
        .captcha-box {
            background-color: var(--text-light); 
            color: var(--bg-dark);
            font-size: 1.5rem;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            letter-spacing: 5px;
            user-select: none;
            height: 38px; 
            line-height: 28px; 
        }
        
        /* === PERBAIKAN STYLING TOMBOL KIRIM === */
        .btn-kirim {
            background-color: var(--accent-orange) !important;
            border-color: var(--accent-orange) !important;
            font-weight: 700 !important; 
            color: var(--bg-dark) !important; 
            padding: 10px 20px !important; 
            border-radius: 8px !important; 
            transition: background-color 0.2s, transform 0.1s, box-shadow 0.2s;
            text-transform: uppercase !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3) !important; 
        }
        .btn-kirim:hover { 
            background-color: #e89e6a !important; 
            border-color: #e89e6a !important;
            transform: translateY(-2px); 
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4) !important; 
            color: var(--bg-dark) !important; 
        }
        .btn-kirim:active {
            transform: translateY(0); 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        }

    </style>
    
</head>
<body>
<div class="main-content">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-card">
                    <h2 class="fw-bold mb-4" style="color: var(--accent-orange);">HUBUNGI KAMI</h2>
                    <p class="mb-4">Kami siap membantu Anda! Jangan ragu untuk menghubungi kami dengan pertanyaan atau kebutuhan Anda.</p>

                    <?php if (!empty($message)): ?>
                        <div class="alert <?= $message_class ?> text-center" role="alert">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>

                    <form action="Contact.php" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="topik" class="form-label">Topik</label>
                            <input type="text" class="form-control" id="topik" name="topik" required>
                        </div>

                        <div class="mb-3">
                            <label for="pesan" class="form-label">Pesan</label>
                            <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="captcha" class="form-label">Captcha</label>
                            <div class="d-flex align-items-end gap-3">
                                <input type="text" class="form-control w-50" id="captcha" name="captcha" required>
                                <span class="captcha-box"><?= $_SESSION['captcha_code'] ?></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-kirim w-100 mt-2">KIRIM PESAN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>