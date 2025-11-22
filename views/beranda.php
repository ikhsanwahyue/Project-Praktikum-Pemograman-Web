<link rel="stylesheet" href="/Project-Praktikum-Pemograman-Web/public/css/style.css?v=<?= time() ?>">

<!-- HERO SECTION AZIZI.IO - REVISED -->
<section class="hero-azizi-revised">

    <div class="container">
        <div class="hero-layout-revised">
            <!-- Left Side - Text Content -->
            <div class="hero-text-revised">
                <h1 class="hero-adventure-title">Ayo, bertualang!</h1>
                <p class="hero-adventure-subtitle">Temukan keseruan dan keajaiban dari setiap kisah di dalam buku digital.</p>
            </div>

            <!-- Right Side - Search Box -->
            <div class="hero-search-revised">
                <div class="search-box-azizi">
                    <h3 class="search-title-revised">PENCARIAN AZIZI.IO</h3>
                    
                    <div class="search-filters-revised">
                        <div class="filter-row-revised">
                            <div class="filter-group-revised">
                                <label>Jenjang</label>
                                <select class="filter-select-revised">
                                    <option>Semua Jenjang</option>
                                    <option>SD</option>
                                    <option>SMP</option>
                                    <option>SMA</option>
                                    <option>Umum</option>
                                </select>
                            </div>
                            <div class="filter-group-revised">
                                <label>Tema</label>
                                <select class="filter-select-revised">
                                    <option>Semua Tema</option>
                                    <option>Fiksi</option>
                                    <option>Non-Fiksi</option>
                                    <option>Pendidikan</option>
                                    <option>Bisnis</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="filter-row-revised">
                            <div class="filter-group-revised">
                                <label>Bahasa</label>
                                <select class="filter-select-revised">
                                    <option>Semua Bahasa</option>
                                    <option>Indonesia</option>
                                    <option>Inggris</option>
                                    <option>Arab</option>
                                </select>
                            </div>
                            <div class="filter-group-revised">
                                <label>Format</label>
                                <select class="filter-select-revised">
                                    <option>Semua Format</option>
                                    <option>PDF</option>
                                    <option>EPUB</option>
                                    <option>MOBI</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="keyword-search-revised">
                            <label>Kata Kunci</label>
                            <div class="search-input-container-revised">
                                <input type="text" placeholder="Masukkan kata kunci yang akan dicari..." class="keyword-input-revised">
                                <button class="search-btn-revised">
                                    <i class="fas fa-search"></i>
                                    CARI BUKU
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Text Below Search -->
        <div class="welcome-section">
            <h1 class="welcome-title">Selamat Datang di Azizi.io!</h1>
            <p class="welcome-subtitle">Bahan bacaan literasi yang menarik tersedia untukmu.</p>
        </div>
    </div>
</section>

<!-- SECTION: REKOMENDASI UTAMA -->


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
<!-- <section class="cta-semidark">
    <div class="container">
        <div class="cta-card">
            <h2>Mau Jadi Penulis di Azizi.io?</h2>
            <a href="penulis.php" class="cta-btn">Jadi Penulis</a>
        </div>
    </div>
</section> -->

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