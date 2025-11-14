<?php include '../includes/headerGuest.php'; ?>

<div class="login-card">
  <img src="../asset/logo-ikhsan.png" alt="Logo Buku Ikhsan" class="mb-3 d-block mx-auto" style="width: 80px;">
  <h4 class="fw-bold mb-1 text-center">Welcome to</h4>
  <p class="text-muted mb-4 text-center">Login to Azizi.io</p>

  <!-- Tombol Login Google -->
  <a href="#" class="btn btn-google w-100 mb-3 d-flex align-items-center justify-content-center">
    <img src="../asset/google-icon.png" alt="Google"> Masuk dengan Google
  </a>

  <div class="text-muted mb-3 text-center">atau login dengan Manual</div>

  <!-- Form Login Manual -->
  <form action="../models/proses_login.php" method="POST">
    <div class="mb-3">
      <label for="email" class="form-label">No. WhatsApp atau Email</label>
      <input type="text" class="form-control" id="email" name="email" placeholder="Masukan No. WhatsApp atau Email" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password Anda" required>
    </div>

    <div class="text-end mb-3">
      <a href="#" class="text-decoration-none small">Lupa Kata Sandi?</a>
    </div>

    <button type="submit" class="btn btn-primary w-100">Lanjutkan</button>
  </form>

  <div class="text-center mt-3">
    <a href="../index.php" class="text-decoration-none">Kembali ke Beranda</a>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
