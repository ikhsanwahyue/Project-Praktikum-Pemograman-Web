<?php
// Inisialisasi variabel
$message = '';
$message_class = '';

// Logika Pembuatan Captcha
if (!isset($_SESSION['captcha_code'])) {
    $captcha_code = substr(md5(microtime()), rand(0, 26), 5);
    $_SESSION['captcha_code'] = $captcha_code;
}

// --- Logika POST Request ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifikasi Captcha (Case-insensitive)
    if (!isset($_POST['captcha']) || strtolower($_POST['captcha']) !== strtolower($_SESSION['captcha_code'])) {
        $message = "Kode Captcha salah.";
        $message_class = "alert-danger";
    } else {
        // Ambil data dan sanitasi
        $nama = trim($_POST['nama_lengkap'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $topik = trim($_POST['topik'] ?? '');
        $pesan = trim($_POST['pesan'] ?? '');

        try {
            // Simulasi pengiriman pesan
            $message = "Pesan Anda berhasil terkirim. Terima kasih!";
            $message_class = "alert-success";
            
            // Reset form
            $_POST = array();
            
        } catch (Exception $e) {
            $message = "Terjadi kesalahan saat mengirim pesan: " . $e->getMessage();
            $message_class = "alert-danger";
        }
    }
    // Setelah submit, regenerasi captcha
    $captcha_code = substr(md5(microtime()), rand(0, 26), 5);
    $_SESSION['captcha_code'] = $captcha_code;
}
?>

<div class="main-content">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-card">
                    <h2 class="fw-bold mb-4" style="color: #f9b17a;">HUBUNGI KAMI</h2>
                    <p class="mb-4">Kami siap membantu Anda! Jangan ragu untuk menghubungi kami dengan pertanyaan atau kebutuhan Anda.</p>

                    <?php if (!empty($message)): ?>
                        <div class="alert <?= $message_class ?> text-center" role="alert">
                            <?= $message ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                       value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="topik" class="form-label">Topik</label>
                            <input type="text" class="form-control" id="topik" name="topik" 
                                   value="<?= htmlspecialchars($_POST['topik'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="pesan" class="form-label">Pesan</label>
                            <textarea class="form-control" id="pesan" name="pesan" rows="4" required><?= htmlspecialchars($_POST['pesan'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="captcha" class="form-label">Captcha: <span class="captcha-box"><?= $_SESSION['captcha_code'] ?></span></label>
                            <input type="text" class="form-control w-50" id="captcha" name="captcha" required placeholder="Masukkan kode captcha">
                        </div>

                        <button type="submit" class="btn btn-primary d-flex  mt-2">KIRIM PESAN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>