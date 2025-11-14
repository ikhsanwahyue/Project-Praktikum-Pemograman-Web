<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
  /* Footer utama */
  footer {
    background-color: #f8f9fa; /* sama dengan bg-light */
    color: #676f8d; /* abu keunguan */
    font-size: 0.95rem;
  }

  /* Judul footer */
  footer .fw-semibold {
    color: #3d395e; /* ungu tua */
  }

  /* Link footer */
  footer a {
    color: #676f8d;
    transition: color 0.3s ease;
  }

  footer a:hover {
    color: #f8b57a; /* oranye terang saat hover */
  }

  /* Garis atas footer */
  footer.border-top {
    border-color: #e0e0e0;
  }

  /* Copyright */
  footer .small {
    color: #3d395e;
  }
</style>

  </head>
  <body>
    <div class="footer">
        <footer class="bg-light text-center text-muted py-4 mt-5 border-top">
            <div class="container">
                <p class="mb-1 fw-semibold">Azizi.io — Platform Buku Digital Mahasiswa</p>
                <p class="mb-2">Temukan, langganan, dan baca karya tulis dari penulis muda Indonesia.</p>
    
                <div class="d-flex justify-content-center gap-3 mb-3">
                    <a href="?page=tentang" class="text-decoration-none text-muted">Tentang Kami</a>
                    <a href="?page=kontak" class="text-decoration-none text-muted">Kontak</a>
                    <a href="?page=syarat" class="text-decoration-none text-muted">Syarat & Ketentuan</a>
                </div>

                <p class="mb-0 small">
                    &copy; <?= date('Y') ?> Azizi.io — Hak Cipta Badan Pengembangan dan Pembinaan Bahasa.<br>
                    Dibuat oleh <strong>Ikhsan Wahyu Endriarto</strong> dan <strong>Aziz Putra Darnawan</strong>
                </p>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>