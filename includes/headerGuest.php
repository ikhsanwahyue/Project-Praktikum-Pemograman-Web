<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>

  <nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
      <!-- Logo -->
      <a class="navbar-brand" href="index.php">
        <img src="../public/asset/LogoAziziTeks.png" alt="Logo Azizi.io" class="d-inline-block align-text-top" style="height: 60px;">
      </a>

      <!-- Toggle button for mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAzizi" aria-controls="navbarAzizi" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar content -->
      <div class="collapse navbar-collapse" id="navbarAzizi">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="?page=Beranda">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="?page=About">About</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Book
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="?kategori=fiksi">Fiksi</a></li>
              <li><a class="dropdown-item" href="?kategori=nonfiksi">Non-Fiksi</a></li>
              <li><a class="dropdown-item" href="?kategori=puisi">Puisi</a></li>
              <li><a class="dropdown-item" href="?kategori=esai">Esai</a></li>
              <li><a class="dropdown-item" href="?kategori=ilmiah">Ilmiah</a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="?page=Contact">Contact</a></li>
        </ul>

        <!-- Login & Register buttons -->
        <div class="d-flex">
          <a href="?page=login" class="btn btn-primary me-2">Masuk</a>
          <a href="?page=daftar" class="btn btn-outline-primary">Daftar</a>
        </div>
      </div>
    </div>
  </nav>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
