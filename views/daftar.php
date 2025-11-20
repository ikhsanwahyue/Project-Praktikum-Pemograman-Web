<?php
// file: register.php atau daftar.php
session_start();
?>

<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        /* Warna Latar Belakang */
        background-color: #2d3250;
        /* Menggunakan Flexbox untuk centering sempurna */
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem; /* Padding agar responsif di layar kecil */
      }
      .register-card {
        /* Hapus margin-left: 500px; yang menyebabkan centering rusak */
        background-color: #424769;
        padding: 2.5rem;
        border-radius: 12px; /* Sedikit lebih membulat */
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4); /* Bayangan lebih gelap */
        width: 100%;
        max-width: 450px; /* Sedikit dikecilkan agar terlihat fokus */
      }
      .form-label {
        font-weight: 500;
        color: #f0f0f5; /* Warna label sedikit lebih lembut dari putih murni */
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
        border-color: #f9b17a; /* Border fokus oranye */
        box-shadow: 0 0 0 0.25rem rgba(249, 177, 122, 0.5);
        color: #fff;
      }
      h4, p {
        color: #fff;
      }
      .btn {
        background-color: #f9b17a; /* Oranye */
        border: none;
        font-weight: 600;
        padding: 0.75rem;
      }
      .btn:hover {
        background-color: #e89e6a; /* Oranye gelap */
      }
      .text-light-link {
        color: #f9b17a;
        text-decoration: none;
        font-weight: 500;
      }
      .text-light-link:hover {
        color: #fff;
      }
      .text-white-muted {
        color: #ccc;
        opacity: 1; /* Hapus opacity, gunakan warna spesifik */
        font-size: 0.95rem;
      }
      /* Mengatasi bug margin-bottom pada Bootstrap 5 untuk logo */
      .logo-img {
          margin-bottom: 1.5rem !important;
      }
    </style>
  </head>
  <body>

    <div class="register-card">
      <img src="../public/asset/LogoAziziz.png" alt="Logo Azizi.io" class="logo-img d-block mx-auto" style="width: 80px;">
      <h4 class="fw-bold mb-3 text-center">Buat Akun Baru</h4>
      <p class="mb-4 text-center text-white-muted">Daftar ke Azizi.io dan mulai akses buku digital</p>

      <?php if (isset($_GET['status'])): ?>
        <?php 
          $status = $_GET['status'];
          $message = '';
          $class = 'alert-danger';

          if ($status === 'success') {
              $message = 'Pendaftaran berhasil! Silakan masuk.';
              $class = 'alert-success';
          } elseif ($status === 'password_mismatch') {
              $message = 'Konfirmasi password tidak cocok.';
          } elseif ($status === 'email_exist') {
              $message = 'Email sudah terdaftar.';
          } elseif ($status === 'error_db') {
              $message = 'Gagal menyimpan data ke database.';
          } else {
              $message = 'Terjadi kesalahan, silakan coba lagi.';
          }
        ?>
        <div class="alert <?= $class ?> text-center" role="alert">
          <?= $message ?>
        </div>
      <?php endif; ?>
      <form action="proses_daftar.php" method="POST">
        <div class="mb-3">
          <label for="nama" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukan Nama Lengkap" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Masukan Email Anda" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password" required>
        </div>

        <div class="mb-4"> <label for="konfirmasi" class="form-label">Konfirmasi Password</label>
          <input type="password" class="form-control" id="konfirmasi" name="konfirmasi" placeholder="Ulangi Password" required>
        </div>

        <button type="submit" class="btn w-100 text-white">Daftar Sekarang</button>
      </form>
      
      <p class="mt-4 text-center text-white-muted" style="font-size: 0.9rem;">
        Sudah punya akun? <a href="login.php" class="text-light-link">Masuk di sini</a>
      </p>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>