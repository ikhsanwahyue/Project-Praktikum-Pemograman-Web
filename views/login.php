<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #2d3250;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .login-card {
        margin-left: 500px;
        background-color: #424769;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 0 30px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 500px;
      }
      .form-label {
        font-weight: 500;
        color: #fff;
      }
      .btn {
        background-color: #f9b17a;
      }
    </style>
  </head>
  <body>
    <div class="login-card">
      <img src="../public/asset/LogoAziziz.png" alt="Logo Azizi.io" class="mb-3 d-block mx-auto" style="width: 80px; color: white; ">
      <h4 class="fw-bold mb-5 text-center" style="color: white;">Masuk ke Buku Digital By Azizi.io</h4>
      
      <form action="proses_login.php" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="text" class="form-control" id="email" name="email" placeholder="Masukan Email Anda" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password Anda" required>
        </div>

        <div class="text-end mb-3">
          <a href="#" class="text-decoration-none small text-light">Lupa Kata Sandi?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100">Lanjutkan</button>
      </form>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
