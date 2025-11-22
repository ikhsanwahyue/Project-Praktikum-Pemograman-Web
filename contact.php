<?php include 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - E-Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header/Navbar -->
    <?php include 'components/header.php'; ?>

    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <!-- Contact Info -->
                <div class="md:w-1/3 bg-blue-600 text-white p-8">
                    <h2 class="text-2xl font-bold mb-6">Informasi Kontak</h2>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">Alamat</h3>
                        <p>Jl. Informatika No. 123<br>Kota Mahasiswa, 12345</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">Email</h3>
                        <p>info@ebook-university.com</p>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="font-semibold mb-2">Telepon</h3>
                        <p>+62 123 4567 890</p>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold mb-2">Jam Operasional</h3>
                        <p>Senin - Jumat: 08:00 - 17:00<br>Sabtu: 09:00 - 15:00</p>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="md:w-2/3 p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Hubungi Kami</h2>
                    
                    <?php
                    if (isset($_POST['submit'])) {
                        $name = $_POST['name'];
                        $email = $_POST['email'];
                        $subject = $_POST['subject'];
                        $message = $_POST['message'];
                        
                        // Simulasi pengiriman pesan (bisa diganti dengan email atau penyimpanan ke database)
                        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>
                                Terima kasih, $name! Pesan Anda telah berhasil dikirim. Kami akan merespons dalam 1-2 hari kerja.
                              </div>";
                    }
                    ?>
                    
                    <form method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 mb-2" for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" required 
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-2" for="email">Email</label>
                                <input type="email" id="email" name="email" required 
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 mb-2" for="subject">Subjek</label>
                            <input type="text" id="subject" name="subject" required 
                                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 mb-2" for="message">Pesan</label>
                            <textarea id="message" name="message" rows="5" required 
                                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <button type="submit" name="submit" 
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-semibold">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
</body>
</html>