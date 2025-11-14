<?php
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    include __DIR__ . '/../includes/headerUser.php';
} else {
    include __DIR__ . '/../includes/headerGuest.php';
}
?>

<style>
  /* Import font Raleway dari Google Fonts */
  @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap');

  body {
    background-color: #3d395e; /* ungu tua sebagai background utama */
    font-family: 'Raleway', sans-serif; /* font global */
    margin: 0;
    padding: 0;
  }

  .contact-section {
    background-color: #ffffff; /* box putih agar form jelas */
    border-radius: 12px;
    padding: 2rem;
    max-width: 500px;
    margin: auto;
    margin-top: 80px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    font-family: 'Raleway', sans-serif; /* pastikan konsisten */
  }

  .contact-section h2 {
    font-weight: 700;
    margin-bottom: 1rem;
    color: #f8b57a; /* judul oranye terang */
    text-transform: uppercase;
  }

  .contact-section p {
    color: #676f8d; /* abu keunguan */
    font-weight: 400;
  }

  .form-label {
    font-weight: 600;
    color: #3d395e;
  }

  .form-control {
    background-color: #f9f9fb;
    border: 1px solid #d0d0e0;
    color: #3d395e;
    font-family: 'Raleway', sans-serif;
  }

  .form-control::placeholder {
    color: #999;
    font-family: 'Raleway', sans-serif;
  }

  .btn-kirim {
    background-color: #f8b57a; /* oranye terang */
    color: #3d395e; /* teks ungu tua */
    font-weight: 600;
    border: none;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-family: 'Raleway', sans-serif;
  }

  .btn-kirim:hover {
    background-color: #ffffff;
    color: #3d395e;
    border: 1px solid #f8b57a;
  }
</style>

<div class="contact-section">
  <h2 class="text-center">HUBUNGI KAMI</h2>
  <p class="text-center mb-4">Ada pertanyaan atau kebutuhan? Kirimkan pesanmu di bawah ini.</p>

  <form action="proses_kontak.php" method="POST">
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Lengkap</label>
      <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama kamu..." required>
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="example@gmail.com" required>
    </div>
    <div class="mb-3">
      <label for="pesan" class="form-label">Pesan</label>
      <textarea class="form-control" id="pesan" name="pesan" rows="4" placeholder="Tulis pesanmu di sini..." required></textarea>
    </div>
    <div class="mb-3">
      <label for="captcha" class="form-label">Captcha</label>
      <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Masukkan kode..." required>
    </div>
    <div class="d-grid">
      <button type="submit" class="btn btn-kirim">KIRIM PESAN</button>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
