<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar | Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #2d3250;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .register-card {
        margin-left: 500px;
        background-color: #424769;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 500px;
      }
      .form-label {
        font-weight: 500;
        color: #fff;
      }
      h4, p {
        color: #fff;
      }
      .btn {
        background-color: #f9b17a;
        border: none;
      }
      .btn:hover {
        background-color: #e89e6a;
      }
      .text-light-link {
        color: #f9b17a;
        text-decoration: none;
      }
      .text-white-muted {
        color: white;
        opacity: 0.85;
      }
    </style>
  </head>
  <body>

    <div class="register-card">
      <img src="../public/asset/LogoAziziz.png" alt="Logo Azizi.io" class="mb-3 d-block mx-auto" style="width: 80px;">
      <h4 class="fw-bold mb-3 text-center">Buat Akun Baru</h4>
      <p class="mb-4 text-center text-white-muted" style="color: #ccc;">Daftar ke Azizi.io dan mulai akses buku digital</p>

      <form action="proses_daftar.php" method="POST">
        <div class="mb-3">
          <label for="nama" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukan Nama Lengkap" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="text" class="form-control" id="email" name="email" placeholder="Masukan Email Anda" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password" required>
        </div>

        <div class="mb-5">
          <label for="konfirmasi" class="form-label">Konfirmasi Password</label>
          <input type="password" class="form-control" id="konfirmasi" name="konfirmasi" placeholder="Ulangi Password" required>
        </div>

        <button type="submit" class="btn w-100 text-white">Daftar Sekarang</button>
      </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>