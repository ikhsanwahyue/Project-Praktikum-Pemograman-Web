<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #0d6efd;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .register-card {
        background-color: #fff;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 500px;
        margin-left: 500px;
      }
      .form-label {
        font-weight: 500;
      }
    </style>
  </head>
  <body>

    <div class="register-card">
      <img src="asset/logo-ikhsan.png" alt="Logo Buku Ikhsan" class="mb-3 d-block mx-auto" style="width: 80px;">
      <h4 class="fw-bold mb-1 text-center">Buat Akun Baru</h4>
      <p class="text-muted mb-4 text-center">Daftar ke Azizi.io</p>

      <form action="proses_daftar.php" method="POST">
        <div class="mb-3">
          <label for="nama" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukan Nama Lengkap" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email atau No. WhatsApp</label>
          <input type="text" class="form-control" id="email" name="email" placeholder="Masukan Email atau No. WA" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password" required>
        </div>

        <div class="mb-3">
          <label for="konfirmasi" class="form-label">Konfirmasi Password</label>
          <input type="password" class="form-control" id="konfirmasi" name="konfirmasi" placeholder="Ulangi Password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Daftar Sekarang</button>
      </form>

      <div class="text-center mt-3">
        <a href="index.php" class="text-decoration-none">Kembali ke Beranda</a>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
