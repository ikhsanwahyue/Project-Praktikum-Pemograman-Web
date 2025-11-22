<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="public/asset/LogoAziziTeks.png" alt="Logo Azizi.io" style="height: 60px;">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAzizi">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarAzizi">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="?page=beranda">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="?page=about">About</a></li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
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

          <li class="nav-item"><a class="nav-link" href="?page=contact">Contact</a></li>
        </ul>

        <div class="dropdown">
          <a class="btn btn-light border-0 dropdown-toggle" href="#" data-bs-toggle="dropdown">
            <span class="navbar-toggler-icon"></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="?page=dashboard">Dashboard</a></li>
            <li><a class="dropdown-item" href="?page=profil">Profil Saya</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="?page=logout">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
</nav>
