<?php 
// about.php

// Asumsikan Anda memiliki file konfigurasi dan helper
// Sertakan file yang dibutuhkan (sesuaikan path jika perlu)
include 'config/database.php'; 
// include 'helpers/functions.php'; // Jika ada fungsi helper

// Tentukan data statis untuk halaman About Us
$company_name = "E-Book Platform";
$tagline = "Jembatan Digital Anda Menuju Dunia Pengetahuan";
$mission = "Misi kami adalah mendemokratisasi akses ke literatur berkualitas, menyediakan platform yang intuitif dan kaya fitur bagi para pembaca dan penulis di seluruh dunia.";
$vision = "Menjadi perpustakaan digital terkemuka yang menginspirasi pembelajaran seumur hidup.";

// Data Tim (Contoh)
$team_members = [
    ['name' => 'Budi Setiawan', 'role' => 'Founder & CEO', 'bio' => 'Visioner di bidang teknologi pendidikan dengan pengalaman 10 tahun.', 'icon' => 'fas fa-lightbulb'],
    ['name' => 'Sari Dewi', 'role' => 'Head of Content', 'bio' => 'Kurator literatur yang bersemangat dalam membawa buku-buku terbaik ke platform.', 'icon' => 'fas fa-book-reader'],
    ['name' => 'Ahmad Yusuf', 'role' => 'Lead Developer', 'bio' => 'Memastikan platform berjalan lancar dan aman dengan teknologi terbaru.', 'icon' => 'fas fa-code'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - <?php echo $company_name; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        // Skema Warna Gelap yang Konsisten
                        primary: {
                            dark: '#1e2030', // Background utama gelap
                            medium: '#2a2d44', // Card/panel background
                            light: '#4a4f73' // Border/garis pemisah
                        },
                        accent: {
                            DEFAULT: '#5d69b6', // Biru-Ungu (Warna Utama Aksen)
                            secondary: '#f9b17a' // Orange (Warna Kedua Aksen/Kontras)
                        },
                        text: {
                            light: '#ffffff', // Teks terang (putih)
                            muted: '#a0a3b8' // Teks sekunder/deskripsi
                        },
                        // Gunakan warna ini di seluruh platform Anda
                    },
                    fontFamily: {
                        'raleway': ['Raleway', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Raleway', sans-serif;
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        /* Efek hover yang sama di seluruh platform */
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
            border-color: #f9b17a; /* Aksen kontras saat hover */
        }
    </style>
</head>
<body class="bg-primary-dark text-text-light font-raleway">
    
    <?php include 'components/header.php'; ?>

    <div class="min-h-screen py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-16">
                <h1 class="text-5xl font-extrabold text-text-light mb-4 relative pb-5 inline-block">
                    Tentang Kami 🌍
                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-20 h-1 bg-accent-secondary rounded-full"></div>
                </h1>
                <p class="text-text-muted text-xl mt-4 max-w-3xl mx-auto"><?php echo $tagline; ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                
                <div class="card-hover bg-primary-medium rounded-2xl shadow-xl p-8 border border-primary-light">
                    <div class="text-center">
                        <i class="fas fa-bullseye text-4xl text-accent-secondary mb-4"></i>
                        <h2 class="text-3xl font-bold text-text-light mb-4">Misi Kami</h2>
                        <p class="text-text-muted text-lg leading-relaxed"><?php echo $mission; ?></p>
                    </div>
                </div>

                <div class="card-hover bg-primary-medium rounded-2xl shadow-xl p-8 border border-primary-light">
                    <div class="text-center">
                        <i class="fas fa-eye text-4xl text-accent-secondary mb-4"></i>
                        <h2 class="text-3xl font-bold text-text-light mb-4">Visi Kami</h2>
                        <p class="text-text-muted text-lg leading-relaxed"><?php echo $vision; ?></p>
                    </div>
                </div>
            </div>

            <div id="team" class="mb-16">
                <h2 class="text-3xl font-bold text-text-light mb-8 text-center">Kenalan dengan Tim Kami</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <?php foreach ($team_members as $member): ?>
                    <div class="card-hover bg-primary-medium rounded-2xl shadow-xl p-6 text-center border border-primary-light">
                        <div class="w-20 h-20 rounded-full bg-accent-secondary/50 text-accent-secondary mx-auto mb-4 flex items-center justify-center text-3xl">
                            <i class="<?php echo $member['icon']; ?>"></i>
                        </div>
                        <h3 class="text-xl font-bold text-text-light mb-1"><?php echo $member['name']; ?></h3>
                        <p class="text-accent-DEFAULT font-semibold mb-3"><?php echo $member['role']; ?></p>
                        <p class="text-text-muted text-sm"><?php echo $member['bio']; ?></p>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>

            <div class="bg-accent-DEFAULT/20 rounded-2xl p-10 text-center border border-accent-DEFAULT">
                <h2 class="text-3xl font-bold text-text-light mb-3">Punya Pertanyaan?</h2>
                <p class="text-text-light text-lg mb-6">Hubungi kami atau mulai jelajahi koleksi buku kami sekarang.</p>
                <a href="contact.php" class="inline-block bg-accent-secondary text-text-light px-8 py-3 rounded-xl hover:bg-accent-secondary/80 transition-all duration-300 font-semibold shadow-lg transform hover:scale-105 mr-4">
                    <i class="fas fa-envelope mr-2"></i> Hubungi Kami
                </a>
                <a href="library.php" class="inline-block bg-primary-light text-text-light px-8 py-3 rounded-xl hover:bg-primary-light/80 transition-all duration-300 font-semibold shadow-lg transform hover:scale-105">
                    <i class="fas fa-book mr-2"></i> Ke Perpustakaan
                </a>
            </div>
            
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

</body>
</html>