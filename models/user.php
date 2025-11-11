<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body class="bg-light">

    <!-- 📨 Hero Section -->
    <div class="container mt-5 mb-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h2 class="card-title text-center text-primary mb-3">Hubungi Kami</h2>
          <p class="text-center text-muted mb-4">Kami siap membantu Anda! Jangan ragu untuk menghubungi kami dengan pertanyaan atau kebutuhan Anda.</p>

          <form action="#" method="POST">
            <!-- Nama & Email Bersebelahan -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="namaLengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="namaLengkap" name="namaLengkap" required>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
            </div>

            <!-- Topik -->
            <div class="mb-3">
              <label for="topik" class="form-label">Topik</label>
              <input type="text" class="form-control" id="topik" name="topik" required>
            </div>

            <!-- Pesan -->
            <div class="mb-3">
              <label for="pesan" class="form-label">Pesan</label>
              <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
            </div>

            <!-- Captcha -->
            <div class="mb-3">
              <label for="captcha" class="form-label">Captcha</label>
              <input type="text" class="form-control" id="captcha" name="captcha" required>
            </div>

            <!-- Tombol Kirim -->
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">KIRIM PESAN</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
