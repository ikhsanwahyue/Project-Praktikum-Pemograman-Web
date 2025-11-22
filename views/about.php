<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Azizi.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* RESET DAN BASE */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Raleway', sans-serif; background: white; }
        
        /* HERO SECTION */
        .hero-about { 
            background: linear-gradient(135deg, #2d3250 0%, #424769 100%); 
            min-height: 80vh;
            display: flex;
            align-items: center;
            color: white;
            padding: 80px 0;
        }
        .hero-about h1 { 
            font-size: 3.5rem; 
            font-weight: 800;
            margin-bottom: 1.5rem;
        }
        .hero-about .lead {
            font-size: 1.4rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
        }
        .hero-buttons .btn {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 10px;
            margin-right: 15px;
            margin-bottom: 10px;
        }
        .btn-primary-custom {
            background: #f9b17a;
            border: none;
            color: #2d3250;
        }
        .btn-primary-custom:hover {
            background: #e89e6a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(249, 177, 122, 0.3);
        }
        .hero-image img {
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            max-height: 450px;
            width: 100%;
            object-fit: cover;
        }

        /* VISION MISSION SECTION */
        .vision-mission-section {
            padding: 100px 0;
            background: white;
        }
        .vision-card, .mission-card {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 40px;
            height: 100%;
            border-left: 5px solid #f9b17a;
            transition: all 0.3s ease;
        }
        .mission-card {
            border-left-color: #676f9d;
        }
        .vision-card:hover, .mission-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(45, 50, 80, 0.1);
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            font-size: 2rem;
        }
        .vision-icon { background: #f9b17a; }
        .mission-icon { background: #676f9d; }
        .vision-card h3, .mission-card h3 {
            color: #2d3250;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
        .vision-card p, .mission-card p, 
        .vision-card li, .mission-card li {
            color: #676f9d;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .mission-card ul li {
            margin-bottom: 12px;
            padding-left: 10px;
        }

        /* VALUES SECTION */
        .values-section {
            padding: 100px 0;
            background: #f8f9fa;
        }
        .section-title {
            color: #2d3250;
            font-weight: 800;
            font-size: 2.8rem;
            margin-bottom: 1rem;
        }
        .section-subtitle {
            color: #676f9d;
            font-size: 1.3rem;
            margin-bottom: 4rem;
        }
        .value-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .value-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        .value-icon {
            font-size: 3.5rem;
            margin-bottom: 25px;
        }
        .collaboration { color: #f9b17a; }
        .innovation { color: #676f9d; }
        .passion { color: #424769; }
        .value-card h5 {
            color: #2d3250;
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 15px;
        }
        .value-card p {
            color: #676f9d;
            line-height: 1.6;
        }

        /* TEAM SECTION */
        .team-section {
            padding: 100px 0;
            background: white;
        }
        .team-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            height: 100%;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .team-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2.5rem;
            color: white;
        }
        .avatar-ceo { background: #676f9d; }
        .avatar-content { background: #f9b17a; }
        .avatar-tech { background: #424769; }
        .team-card h5 {
            color: #2d3250;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }
        .team-card .role {
            color: #f9b17a;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .team-card .bio {
            color: #676f9d;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* CTA SECTION */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f9b17a 0%, #e89e6a 100%);
            color: white;
            text-align: center;
        }
        .cta-section h2 {
            font-weight: 800;
            font-size: 2.8rem;
            margin-bottom: 1.5rem;
        }
        .cta-section .lead {
            font-size: 1.3rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }
        .cta-buttons .btn {
            padding: 15px 35px;
            font-weight: 700;
            border-radius: 12px;
            margin: 0 10px 15px;
            font-size: 1.1rem;
        }
        .btn-light-custom {
            background: rgba(255,255,255,0.95);
            color: #2d3250;
            border: none;
        }
        .btn-light-custom:hover {
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(45,50,80,0.2);
        }
        .btn-outline-light-custom {
            border: 2px solid rgba(255,255,255,0.8);
            background: transparent;
            color: white;
        }
        .btn-outline-light-custom:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
            transform: translateY(-3px);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero-about h1 { font-size: 2.5rem; }
            .hero-about .lead { font-size: 1.1rem; }
            .section-title { font-size: 2.2rem; }
            .section-subtitle { font-size: 1.1rem; }
            .cta-section h2 { font-size: 2.2rem; }
            .hero-buttons .btn, .cta-buttons .btn { 
                width: 100%; 
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1>Tentang Azizi.io</h1>
                    <p class="lead">Platform buku digital inovatif yang menghubungkan para pecinta literasi dengan dunia membaca yang tak terbatas.</p>
                    <div class="hero-buttons">
                        <a href="Contact.php" class="btn btn-primary-custom">Hubungi Kami</a>
                        <a href="beranda.php" class="btn btn-outline-light">Jelajahi Buku</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <img src="https://cdn.pixabay.com/photo/2018/03/06/17/40/paper-3204064_1280.jpg" alt="Reading Community">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section class="vision-mission-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="vision-card">
                        <div class="icon-circle vision-icon">
                            <i class="fas fa-eye text-white"></i>
                        </div>
                        <h3>Visi Kami</h3>
                        <p>Menjadi platform buku digital terdepan yang memajukan literasi Indonesia dan membangun generasi pembaca yang cerdas.</p>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="mission-card">
                        <div class="icon-circle mission-icon">
                            <i class="fas fa-bullseye text-white"></i>
                        </div>
                        <h3>Misi Kami</h3>
                        <ul class="list-unstyled">
                            <li>• Menyediakan akses buku digital yang terjangkau</li>
                            <li>• Membangun komunitas pembaca yang aktif</li>
                            <li>• Mendorong minat baca di kalangan generasi muda</li>
                            <li>• Mendukung penulis lokal Indonesia</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-12">
                    <h2 class="section-title">Nilai-Nilai Kami</h2>
                    <p class="section-subtitle">Prinsip yang menjadi pedoman dalam setiap langkah kami</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon collaboration">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5>Kolaborasi</h5>
                        <p>Bekerja sama dengan berbagai pihak untuk menciptakan ekosistem literasi yang sehat</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon innovation">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h5>Inovasi</h5>
                        <p>Terus berinovasi dalam teknologi untuk pengalaman membaca yang lebih baik</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="value-card">
                        <div class="value-icon passion">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5>Passion</h5>
                        <p>Melakukan segala sesuatu dengan penuh semangat dan dedikasi</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-12">
                    <h2 class="section-title">Tim Kami</h2>
                    <p class="section-subtitle">Orang-orang berdedikasi di balik Azizi.io</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4">
                    <div class="team-card">
                        <div class="team-avatar avatar-ceo">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5>Ikhsan Wahyu Endriarto</h5>
                        <div class="role">Front End Developer</div>
                        <p class="bio">Merancang antarmuka yang jernih dan harmonis. Fokus pada pengalaman pengguna, struktur modular, dan branding yang konsisten. Selalu siap menyulap ide menjadi tampilan yang bisa dinikmati semua orang.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="team-card">
                        <div class="team-avatar avatar-content">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5>Aziz Putra Darmawan</h5>
                        <div class="role">Back End Developer</div>
                        <p class="bio">Penggiat literasi digital dan kurator konten. Menjaga logika dan alur data tetap solid di balik layar. Membangun fondasi sistem yang kokoh agar platform berjalan mulus dan efisien.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2>Bergabunglah dengan Komunitas Pembaca Kami</h2>
                    <p class="lead">Mulai perjalanan membaca Anda bersama ribuan pembaca lainnya</p>
                    <div class="cta-buttons">
                        <a href="?page=daftar" class="btn btn-light-custom">Daftar Sekarang</a>
                        <a href="?page=contact" class="btn btn-outline-light-custom">Tanya Jawab</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>