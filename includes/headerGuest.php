<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styleAzizi.css">
    <style>
      .profile-img {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 50%;
      }

      .navbar {
        background-color: #ffffff; /* putih bersih */
        box-shadow: 0 2px 10px rgba(0,0,0,0.1); /* bayangan halus */
      }

      .navbar-brand {
        font-weight: bold;
        color: #3d395e;
      }

      .navbar .nav-link {
        color: #3d395e;
        font-weight: 500;
        transition: color 0.3s ease;
      }

      .navbar .nav-link:hover {
        color: #f8b57a;
      }

      .dropdown-menu {
        background-color: #ffffff;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      }

      .dropdown-menu .dropdown-item {
        color: #3d395e;
        transition: background-color 0.3s ease, color 0.3s ease;
      }

      .dropdown-menu .dropdown-item:hover {
        background-color: #f8b57a;
        color: #ffffff;
      }

      .btn-primary {
        background-color: #3d395e;
        border-color: #3d395e;
      }

      .btn-primary:hover {
        background-color: #f8b57a;
        border-color: #f8b57a;
      }

      .btn-outline-primary {
        color: #3d395e;
        border-color: #3d395e;
      }

      .btn-outline-primary:hover {
        background-color: #f8b57a;
        border-color: #f8b57a;
        color: #ffffff;
      }
    </style>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">

      <a class="navbar-brand" href="index.php">
        <img src="../public/asset/LogoAziziTeks.png" alt="Logo Azizi.io" class="d-inline-block align-text-top" style="height: 60px;">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAzizi" aria-controls="navbarAzizi" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

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