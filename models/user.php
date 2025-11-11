<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  </head>
  <body class="bg-light">

    <div class="div-navbar">
      <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid px-4">
          <div class="div-judul">
            <a class="navbar-brand fw-bold text-primary" href="#">User Dashboard</a>
          </div>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-between" id="navbarContent">
            <div class="div-menu w-100">
              <ul class="navbar-nav justify-content-center w-100">
                <li class="nav-item"><a class="nav-link active text-primary" href="#">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Buku</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Kontak</a></li>
              </ul>
            </div>
            <div class="div-ikon d-flex align-items-center gap-3">
              <a href="#" class="text-secondary"><i class="bi bi-person-circle fs-5"></i></a>
              <a href="#" class="text-secondary"><i class="bi bi-gear-fill fs-5"></i></a>
            </div>
          </div>
        </div>
      </nav>
    </div>

    <div class="div-hero justify-content-center">
      <div class="container-fluid py-5">
        <div class="row">
          <div class="col-lg-10 col-xl-8 justify-content-center mx-auto">
            <div class="div-header text-start mb-4">
              <h2 class="text-primary">Hubungi Kami</h2>
              <p class="text-muted">Kami siap membantu Anda! Jangan ragu untuk menghubungi kami dengan pertanyaan atau kebutuhan Anda.</p>
            </div>
            <form action="#" method="POST" class="div-form">
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="div-nama">
                    <label for="namaLengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="namaLengkap" name="namaLengkap" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="div-email">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                  </div>
                </div>
              </div>
              <div class="div-topik mb-3">
                <label for="topik" class="form-label">Topik</label>
                <input type="text" class="form-control" id="topik" name="topik" required>
              </div>
              <div class="div-pesan mb-3">
                <label for="pesan" class="form-label">Pesan</label>
                <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
              </div>
              <div class="div-captcha mb-3">
                <label for="captcha" class="form-label">Captcha</label>
                <input type="text" class="form-control" id="captcha" name="captcha" required>
              </div>
              <div class="div-tombol d-grid">
                <button type="submit" class="btn btn-primary">KIRIM PESAN</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="div-footer bg-white text-center py-4 border-top">
      <p class="mb-1 fw-semibold">Pusat Pembinaan Bahasa dan Sastra</p>
      <p class="mb-1">Kementerian Pendidikan Dasar dan Menengah</p>
      <p class="text-muted small">Hak Cipta Badan Pengembangan dan Pembinaan Bahasa © 2023 Bahasa</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
