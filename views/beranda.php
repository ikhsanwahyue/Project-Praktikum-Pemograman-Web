<link rel="stylesheet" href="/Project-Praktikum-Pemograman-Web/public/css/style.css?v=<?= time() ?>">

<!-- HERO SECTION (Semi Dark Full Width) -->
<section class="hero-semidark">
    <div class="container hero-wrapper">
        <div class="hero-text">
            <h1>Bersama Azizi.io Jelajahi Dunia Buku Digital Tanpa Ribet!</h1>
            <p>Akses buku berkualitas, dimulai dari sini!</p>

            <div class="hero-actions">
                <a href="buku.php" class="btn-primary">Jelajahi Buku</a>
                <?php if (!isset($_SESSION['is_logged_in'])): ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- SECTION: REKOMENDASI UTAMA -->
<section class="book-section">
    <div class="container">
        <h2 class="section-title">Rekomendasi untuk-mu!</h2>

        <div class="featured-layout">
            <!-- Poster (Tetap / Tidak Scroll) -->
            <div class="featured-big">
                <img src="https://placehold.co/400x500/2d3147/648599?text=Buku+Unggulan" 
                     alt="Buku Unggulan" class="featured-img">
            </div>

            <!-- Buku Scroll Horizontal -->
            <div class="featured-list">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="book-card modern">
                    <div class="book-cover">
                        <img src="https://placehold.co/200x250/2d3147/648599?text=Cover<?= $i ?>" 
                             alt="Buku Rekomendasi <?= $i ?>">
                    </div>
                    <div class="book-info">
                        <h3 class="book-title">Buku Rekomendasi <?= $i ?></h3>
                        <p class="author">Penulis <?= chr(64 + (($i % 26) ?: 1)) ?></p>
                        <div class="rating">★★★★★</div>
                        <button class="btn-small">Gratis Ebook</button>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

        </div>
    </div>
</section>

<!-- SECTION: NEW RELEASE -->
<section class="book-section">
    <div class="container">
        <h2 class="section-title">New Release!</h2>

        <div class="featured-layout">
            <!-- Poster -->
            <div class="featured-big">
                <img src="https://placehold.co/400x500/2d3147/648599?text=New+Release" 
                     alt="New Release" class="featured-img">
            </div>

            <!-- Scroll List -->
            <div class="featured-list">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="book-card modern">
                    <div class="book-cover">
                        <img src="https://placehold.co/200x250/2d3147/648599?text=Baru<?= $i ?>" 
                             alt="Buku Baru <?= $i ?>">
                    </div>
                    <div class="book-info">
                        <h3 class="book-title">Buku Baru <?= $i ?></h3>
                        <p class="author">Penulis <?= chr(64 + (($i % 26) ?: 1)) ?></p>
                        <div class="rating">★★★★★</div>
                        <button class="btn-small">Gratis Ebook</button>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

        </div>
    </div>
</section>

<!-- SECTION: PALING POPULER -->
<section class="book-section">
    <div class="container">
        <h2 class="section-title">Paling Populer!</h2>

        <div class="featured-layout">
            <!-- Poster -->
            <div class="featured-big">
                <img src="https://placehold.co/400x500/2d3147/648599?text=Populer" 
                     alt="Buku Populer" class="featured-img">
            </div>

            <!-- Scroll List -->
            <div class="featured-list">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="book-card modern">
                    <div class="book-cover">
                        <img src="https://placehold.co/200x250/2d3147/648599?text=Populer<?= $i ?>" 
                             alt="Buku Populer <?= $i ?>">
                    </div>
                    <div class="book-info">
                        <h3 class="book-title">Populer <?= $i ?></h3>
                        <p class="author">Penulis <?= chr(64 + (($i % 26) ?: 1)) ?></p>
                        <div class="rating">★★★★★</div>
                        <button class="btn-small">Gratis Ebook</button>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

        </div>
    </div>
</section>

<!-- CTA SECTION -->
<section class="cta-semidark">
    <div class="container">
        <div class="cta-card">
            <h2>Mau Jadi Penulis di Azizi.io?</h2>
            <a href="penulis.php" class="cta-btn">Jadi Penulis</a>
        </div>
    </div>
</section>

<!-- LANGGANAN SECTION -->
<section class="subscribe-info">
    <div class="container">
        <h2>Bagaimana Cara Langganan Buku di Azizi.io?</h2>
        <h6>Sangat mudah untuk berlangganan di digital books by Azizi.io! Mari ikuti cara ini.</h6>

        <div class="steps-grid">
            <div class="step-box">
                <h3>Daftar Akun</h3>
                <hr>
                <p>Buat akun baru dalam hitungan detik untuk mulai menggunakan platform.</p>
            </div>
            
            <div class="step-box">
                <h3>Pilih Langganan</h3>
                <hr>
                <p>Pilih paket langganan yang sesuai kebutuhan membaca Anda.</p>
            </div>

            <div class="step-box">
                <h3>Proses Pembayaran</h3>
                <hr>
                <p>Lakukan pembayaran dengan metode yang mudah dan aman.</p>
            </div>
            <div class="step-box">
                <h3>Baca Semua Buku</h3>
                <hr>
                <p>Nikmati akses penuh ke semua koleksi buku digital kami.</p>
            </div>
        </div>

        <div class="text-center">
            <a href="daftar.php" class="btn-primary center">Pilih Bukunya Sekarang!</a>
        </div>
    </div>
</section>
