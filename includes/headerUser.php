<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .profile-img {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 50%;
      }
    </style>
  </head>
  <body>

  <nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">
        <span class="text-primary">Azizi</span><span class="text-warning">.io</span>
      </a>

      <ul class="navbar-nav mx-auto d-none d-lg-flex">
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

      <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="<?= $_SESSION['foto'] ?? 'asset/default.png' ?>" alt="Foto Profil" class="profile-img me-2">
          <span class="fw-semibold"><?= $_SESSION['nama'] ?? 'Pengguna' ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="?page=profil">Profil Saya</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="?page=logout">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>