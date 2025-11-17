<?php include '../includes/headerGuest.php'; ?>

<section class="bg-light py-5 text-center">
  <div class="container">
    <h1 class="display-4 fw-bold text-primary">Azizi.io</h1>
    <p class="lead">Bersama Azizi.io<br>Jelajahi Dunia Buku Digital Tanpa Ribet!</p>
    <p class="mb-4">Akses buku berkualitas, dimulai dari sini!</p>
    <a href="?page=daftar" class="btn btn-primary btn-lg px-4">Daftar Sekarang</a>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <h2 class="mb-4 fw-semibold text-center">Rekomendasi untuk-mu!</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="public/asset/placeholder.png" class="card-img-top" alt="Cover Buku">
          <div class="card-body">
            <h5 class="card-title">Judul Buku <?= $i ?></h5>
            <p class="card-text">Penulis <?= $i ?></p>
            <div class="mb-2 text-warning">★★★★★</div>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-sm btn-primary">Baca</a>
              <a href="#" class="btn btn-sm btn-outline-secondary">Favoritkan</a>
            </div>
          </div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container">
    <h2 class="mb-4 fw-semibold text-center">New Release!</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="public/asset/placeholder.png" class="card-img-top" alt="Cover Buku">
          <div class="card-body">
            <h5 class="card-title">Buku Baru <?= $i ?></h5>
            <p class="card-text">Penulis Baru <?= $i ?></p>
            <div class="mb-2 text-warning">★★★★☆</div>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-sm btn-primary">Baca</a>
              <a href="#" class="btn btn-sm btn-outline-secondary">Favoritkan</a>
            </div>
          </div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <h2 class="mb-4 fw-semibold text-center">Paling Populer!</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
      <?php for ($i = 1; $i <= 4; $i++): ?>
      <div class="col">
        <div class="card h-100 shadow-sm">
          <img src="public/asset/placeholder.png" class="card-img-top" alt="Cover Buku">
          <div class="card-body">
            <h5 class="card-title">Buku Populer <?= $i ?></h5>
            <p class="card-text">Penulis Populer <?= $i ?></p>
            <div class="mb-2 text-warning">★★★★★</div>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-sm btn-primary">Baca</a>
              <a href="#" class="btn btn-sm btn-outline-secondary">Favoritkan</a>
            </div>
          </div>
        </div>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<section class="py-5 bg-primary text-white text-center">
  <div class="container">
    <h3 class="mb-3">Mau Jadi Penulis di Azizi.io?</h3>
    <a href="?page=daftar" class="btn btn-light btn-lg px-4">Daftar Sekarang</a>
  </div>
</section>

<section class="py-5 bg-light">
  <div class="container text-center">
    <h4 class="fw-semibold mb-3">Bagaimana Cara Langganan Buku di Azizi.io?</h4>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <ol class="list-group list-group-numbered">
          <li class="list-group-item">Daftar akun Azizi.io</li>
          <li class="list-group-item">Pilih buku yang ingin dibaca</li>
          <li class="list-group-item">Mulai membaca dan nikmati!</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
