<?php
// file: views/admin/adminRegister.php
// Tanggung Jawab: Tampilan (Frontend)

// 1. Inisiasi Sesi: Wajib untuk mengambil dan menampilkan pesan status dari Controller.
session_start();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Admin Azizi.io</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        /* ========================================================= */
        /* STYLE KUSTOM AZIZI.IO (Konsisten dengan Palet Warna) */
        /* ========================================================= */
        
        /* Variabel CSS dari Palet Anda: #2d3250, #424769, #f9b17a, dll. */
        :root {
            --bg-body: #2d3250; 
            --bg-card: #424769; 
            --color-text: #ffffff;
            --color-accent: #f9b17a; 
            --color-secondary: #676f9d; 
            --font-family: 'Raleway', sans-serif;
        }

        body {
            /* Gradasi yang mirip dengan adminLogin.php */
            background: linear-gradient(135deg, var(--color-secondary) 0%, var(--bg-body) 100%); 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-family);
            color: var(--color-text);
        }

        .register-card { 
            background-color: var(--bg-card);
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
        }

        .register-card h4 {
            font-weight: 600;
            color: var(--color-text);
        }
        .form-label {
            font-weight: 400;
            color: var(--color-text);
        }
        
        /* Tombol Daftar/Button Style */
        .btn-custom {
            /* Gradien yang sama dengan tombol Login Admin */
            background: linear-gradient(135deg, var(--color-secondary), var(--bg-card));
            border: none;
            color: var(--color-text);
            font-weight: 600;
            transition: all 0.2s;
            padding: 0.75rem; 
            width: 100%;
        }
        .btn-custom:hover {
            background: linear-gradient(135deg, var(--bg-card), var(--color-secondary));
            color: var(--color-text);
        }
        
        /* Tautan Login di Sini */
        .link-secondary {
            color: var(--color-secondary) !important;
        }
        .link-secondary:hover {
            color: var(--color-accent) !important;
        }
        
        /* Style untuk pesan sukses/error (agar pakai warna konsisten) */
        .alert-custom {
            background-color: #f9b17a20; /* Sedikit transparan dari warna aksen */
            color: var(--color-accent);
            border: 1px solid var(--color-accent);
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h4 class="fw-bold mb-5 text-center">Daftar Admin</h4>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-custom text-center small py-2" role="alert">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success text-center small py-2" role="alert">
                <?= $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="../../proses_admin_register.php" method="POST">
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukan Email Admin" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukan Password" required>
            </div>

            <div class="mb-4">
                <label for="konfirmasi" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="konfirmasi" name="konfirmasi" placeholder="Ulangi Password" required>
            </div>

            <button type="submit" name="daftar_admin" class="btn btn-custom">Daftar</button>
        </form>

        <div class="text-center mt-4">
            <a href="adminLogin.php" class="text-decoration-none small link-secondary">Sudah punya akun? Login di sini</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>