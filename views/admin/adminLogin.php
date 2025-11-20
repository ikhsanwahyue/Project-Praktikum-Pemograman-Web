<?php
// file: views/admin/adminLogin.php

// 1. Inisiasi Sesi: Wajib untuk mengambil dan menampilkan pesan error (jika ada) dari Controller.
session_start(); 
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin Azizi.io</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        /* ========================================================= */
        /* 2. Style Kustom (Menggunakan Palet Warna Azizi.io) */
        /* ========================================================= */
        
        /* Definisi Variabel CSS agar mudah diubah */
        :root {
            --bg-body: #2d3250; 
            --bg-card: #424769; 
            --color-text: #ffffff;
            --color-accent: #f9b17a; 
            --color-secondary: #676f9d; 
            --font-family: 'Raleway', sans-serif;
        }

        /* Mengatur background dan memposisikan konten di tengah layar */
        body {
            background-color: var(--bg-body);
            height: 100vh;
            display: flex;
            align-items: center; /* Vertikal center */
            justify-content: center; /* Horizontal center */
            font-family: var(--font-family);
            color: var(--color-text);
        }

        /* Styling Card Login */
        .login-card {
            background-color: var(--bg-card);
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); /* Efek bayangan 3D */
            width: 100%;
            max-width: 450px;
        }

        /* Styling Judul dan Label Form */
        .login-card h4 {
            font-weight: 600; /* Menggunakan Semibold */
            color: var(--color-text);
        }
        .form-label {
            font-weight: 400; /* Menggunakan Regular */
            color: var(--color-text);
        }

        /* Styling Tombol Login (Menggunakan gradasi ungu untuk kesan modern) */
        .btn-custom {
            /* Gradien dari warna secondary ke warna primary */
            background: linear-gradient(135deg, var(--color-secondary), var(--bg-card)); 
            border: none;
            color: var(--color-text);
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-custom:hover {
            /* Efek hover membalik gradasi */
            background: linear-gradient(135deg, var(--bg-card), var(--color-secondary));
            color: var(--color-text);
        }
        
        /* Styling Link "Daftar di sini" */
        .link-secondary {
            color: var(--color-secondary) !important;
        }
        .link-secondary:hover {
            color: var(--color-accent) !important; /* Berubah ke warna aksen saat hover */
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h4 class="fw-bold mb-5 text-center">Login Admin</h4>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-warning text-center small py-2" role="alert">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="../../proses_admin_login.php" method="POST">
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukan Email Admin" required>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password" required>
            </div>

            <button type="submit" class="btn btn-custom w-100 py-2">Login</button>
        </form>

        <div class="text-center mt-4">
            <a href="../../daftar.php" class="text-decoration-none small link-secondary">Belum punya akun? Daftar di sini</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>