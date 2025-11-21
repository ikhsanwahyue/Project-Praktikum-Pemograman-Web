<?php

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: beranda.php");
    exit();
}

$message = '';
$class = 'alert-danger';

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status === 'email_exists') {
        $message = 'Email sudah terdaftar.';
    } elseif ($status === 'error') {
        $message = 'Pendaftaran gagal. Silakan coba lagi.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun | Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/style.css?v=2.0">
</head>
<body class="login-page-body">

    <div class="login-card">
        <img src="../public/asset/LogoAziziz.png"
             onerror="this.onerror=null;this.src='https://placehold.co/80x80/2a2d44/648599?text=AZ'"
             alt="Logo Azizi.io"
             class="logo-img d-block mx-auto mb-3"
             style="width: 80px;">
        <h4 class="fw-bold text-center mb-4 text-accent">Buat Akun Baru di Azizi.io</h4>

        <?php if (!empty($message)): ?>
            <div class="alert <?= $class ?> text-center" role="alert">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="processRegister.php" method="POST">
            <div class="mb-3">
                <label for="nama_lengkap" class="form-label text-muted-custom">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Contoh: Budi Santoso" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label text-muted-custom">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukan Email Anda" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label text-muted-custom">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password (min. 6 karakter)" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary-login w-100">Daftar Sekarang</button>
        </form>

        <p class="link-text">
            Sudah punya akun? <a href="login.php" class="text-light-link">Masuk di sini</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>