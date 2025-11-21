<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: home.php");
    exit();
}

$message = '';
// Default class untuk alert (digunakan jika tidak ada status yang cocok)
$class = 'alert-info'; 

if (isset($_GET['status'])) {
    // Pastikan nilai status adalah string dan aman
    $status = htmlspecialchars($_GET['status'], ENT_QUOTES, 'UTF-8'); 
    
    switch ($status) {
        case 'failed':
            $message = 'Gagal masuk. Email atau **Password** salah.';
            $class = 'alert-danger';
            break;
        case 'not_logged_in':
            $message = 'Anda harus masuk untuk mengakses halaman tersebut.';
            $class = 'alert-warning';
            break;
        case 'registered':
            $message = 'Pendaftaran **berhasil**! Silakan masuk dengan akun Anda.';
            $class = 'alert-success';
            break;
        case 'logged_out':
            $message = 'Anda telah berhasil keluar (logout).';
            $class = 'alert-info';
            break;
        default:
            // Tambahkan default case untuk status yang tidak dikenal
            $message = '';
            $class = '';
            break;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../public/css/style.css"> 
</head>
<body class="login-page-body">

    <div class="login-card">
        <img src="../public/asset/LogoAziziz.png"
             onerror="this.onerror=null;this.src='https://placehold.co/80x80/2d3150/f6b17a?text=Logo'"
             alt="Logo Azizi.io"
             class="logo-img d-block mx-auto mb-3"
             style="width: 80px;">
        
        <h4 class="fw-bold text-center mb-4 text-accent">Selamat Datang Kembali</h4>

        <?php if (!empty($message)): ?>
            <div class="alert <?= $class ?> alert-dismissible fade show border-0 text-center" role="alert" style="--bs-alert-bg: #4c527f; --bs-alert-color: #fff;">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="filter: invert(1);"></button>
            </div>
        <?php endif; ?>

        <form action="../../controllers/authController.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label text-muted-custom">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukan Email Anda" required autocomplete="off" aria-label="Email">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label text-muted-custom">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password" required autocomplete="off" aria-label="Password">
            </div>
            <button type="submit" name="login" class="btn btn-primary-login w-100">Masuk</button>
        </form>

        <p class="link-text">
            Belum punya akun? <a href="daftar.php" class="text-light-link">Daftar di sini</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>