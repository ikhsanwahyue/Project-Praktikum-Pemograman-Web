<?php 
// Pastikan sesi sudah dimulai dan $connection tersedia di file database.php
include 'config/database.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - Azizi.io E-Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Palet Warna Kustom (WAJIB ADA di file utama atau di sini) */
        .bg-primary-dark { background-color: #192038; } 
        .bg-soft-blue { background-color: #6f76fd; }
        .bg-dark-accent { background-color: #2d3250; }
        .bg-highlight { background-color: #f6b17a; }
        .text-soft-blue { color: #6f76fd; }
        .text-highlight { color: #f6b17a; }
        .border-soft-blue { border-color: #6f76fd; }
        .text-primary-dark { color: #192038; }
    </style>
</head>
<body class="bg-primary-dark text-white">
    <?php include 'components/header.php'; ?>

    <div class="container mx-auto px-4 py-16 lg:py-24">
        <div class="max-w-5xl mx-auto bg-dark-accent rounded-2xl shadow-2xl overflow-hidden border border-gray-700">
            <div class="md:flex">
                
                <div class="md:w-1/3 bg-soft-blue text-white p-10 flex flex-col justify-center">
                    <h2 class="text-3xl font-bold mb-8 flex items-center">
                        <i class="fas fa-headset mr-3"></i> Informasi Kontak
                    </h2>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2 flex items-center text-highlight">
                            <i class="fas fa-map-marker-alt mr-3"></i> Alamat
                        </h3>
                        <p class="text-gray-100">Jl. Kapten Ikhsan Wahyu Endriarto No. 01, Kota Yogyakarta</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2 flex items-center text-highlight">
                            <i class="fas fa-envelope mr-3"></i> Email
                        </h3>
                        <p class="text-gray-100">Azizi@azizi.com</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2 flex items-center text-highlight">
                            <i class="fas fa-phone-alt mr-3"></i> Telepon
                        </h3>
                        <p class="text-gray-100">+62 896 0157 6276 (Aziz)</p>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-2 flex items-center text-highlight">
                            <i class="fas fa-clock mr-3"></i> Jam Operasional
                        </h3>
                        <p class="text-gray-100">Senin - Jumat: 08:00 - 17:00<br>Sabtu: 09:00 - 15:00</p>
                    </div>
                </div>
                
                <div class="md:w-2/3 p-10">
                    <h2 class="text-3xl font-bold mb-8 text-white">
                        Kirimkan Pesan kepada Kami
                    </h2>
                    
                    <?php
                    // Logika PHP DIBIARKAN SAMA PERSIS
                    if (isset($_POST['submit'])) {
                        $name = $_POST['name'];
                        $email = $_POST['email'];
                        $subject = $_POST['subject'];
                        $message = $_POST['message'];
                        
                        // Simulasi pengiriman pesan (bisa diganti dengan email atau penyimpanan ke database)
                        echo "<div class='bg-green-700 border border-green-500 text-white px-4 py-3 rounded-lg mb-6 shadow-md'>
                                Terima kasih, <span class='font-bold'>$name</span>! Pesan Anda telah berhasil dikirim. Kami akan merespons dalam 1-2 hari kerja.
                              </div>";
                    }
                    ?>
                    
                    <form method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-300 mb-2 font-medium" for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" required 
                                        class="w-full px-5 py-3 border border-gray-600 bg-primary-dark rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-soft-blue focus:border-soft-blue transition">
                            </div>
                            <div>
                                <label class="block text-gray-300 mb-2 font-medium" for="email">Email</label>
                                <input type="email" id="email" name="email" required 
                                        class="w-full px-5 py-3 border border-gray-600 bg-primary-dark rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-soft-blue focus:border-soft-blue transition">
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-300 mb-2 font-medium" for="subject">Subjek</label>
                            <input type="text" id="subject" name="subject" required 
                                   class="w-full px-5 py-3 border border-gray-600 bg-primary-dark rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-soft-blue focus:border-soft-blue transition">
                        </div>
                        
                        <div class="mb-8">
                            <label class="block text-gray-300 mb-2 font-medium" for="message">Pesan</label>
                            <textarea id="message" name="message" rows="5" required 
                                     class="w-full px-5 py-3 border border-gray-600 bg-primary-dark rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-soft-blue focus:border-soft-blue transition"></textarea>
                        </div>
                        
                        <button type="submit" name="submit" 
                                class="w-full bg-highlight text-primary-dark py-4 px-4 rounded-xl hover:bg-highlight/90 transition font-extrabold text-lg shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
</body>
</html>