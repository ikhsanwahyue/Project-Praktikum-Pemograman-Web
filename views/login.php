<?php
// file: views/login.php
session_start();
?>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      /* Warna: #2d3250 (Background), #424769 (Card), #f9b17a (Accent) */
      body {
        background-color: #2d3250;
        /* Menggunakan Flexbox untuk centering sempurna */
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
      }
      .login-card {
        /* Hapus margin-left yang tidak responsif */
        background-color: #424769;
        padding: 2.5rem;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        width: 100%;
        max-width: 450px; 
      }
      h4 {
        color: white;
        margin-bottom: 2.5rem !important; /* Tambah margin bawah untuk judul */
      }
      .form-label {
        font-weight: 500;
        color: #f0f0f5; 
      }
      .form-control {
        background-color: #53587a; /* Input background lebih gelap */
        border: 1px solid #6b7095;
        color: #fff;
      }
      .form-control::placeholder {
        color: #c0c0d0;
      }
      .form-control:focus {
        background-color: #53587a;
        border-color: #f9b17a; 
        box-shadow: 0 0 0 0.25rem rgba(249, 177, 122, 0.5);
        color: #fff;
      }
      .btn-primary {
        background-color: #f9b17a; /* Oranye */
        border: none;
        font-weight: 600;
        padding: 0.75rem;
        color: #3d395e; /* Teks tombol lebih gelap */
        transition: background-color 0.2s;
      }
      .btn-primary:hover {
        background-color: #e89e6a; /* Oranye gelap saat hover */
        border: none;
      }
      .text-light-link {
        color: #f9b17a !important;
        text-decoration: none;
        font-weight: 500;
      }
      .text-light-link:hover {
        color: #fff !important;
      }
      .logo-img {
          margin-bottom: 1.5rem !important;
      }
    </style>
  </head>
  <body>

    <div class="login-card">
      <img src="../public/asset/LogoAziziz.png" alt="Logo Azizi.io" class="logo-img d-block mx-auto" style="width: 80px;">
      <h4 class="fw-bold text-center">Masuk ke Buku Digital Azizi.io</h4>
      
      <?php if (isset($_GET['status'])): ?>
        <?php 
          $status = $_GET['status'];
          $message = '';
          if ($status === 'failed') {
              $message = 'Email atau Password salah. Silakan coba lagi.';
          } elseif ($status === 'registered') {
              $message = 'Pendaftaran berhasil! Silakan masuk.';
              $class = 'alert-success';
          }
        ?>
        <div class="alert <?= $class ?? 'alert-danger' ?> text-center" role="alert">
          <?= $message ?>
        </div>
      <?php endif; ?>
      <form action="proses_login.php" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Masukan Email Anda" required>
        </div>

        <div class="mb-4"> <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password Anda" required>
        </div>

        <div class="text-end mb-4"> <a href="#" class="text-light-link small">Lupa Kata Sandi?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100">Lanjutkan</button>
      </form>
      
      <p class="mt-4 text-center" style="font-size: 0.9rem; color: #ccc;">
        Belum punya akun? <a href="daftar.php" class="text-light-link">Daftar di sini</a>
      </p>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>