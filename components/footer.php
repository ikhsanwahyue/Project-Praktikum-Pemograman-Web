<footer class="bg-primary-dark text-white pt-16 pb-10 border-t border-gray-700">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-12">
            
            <div class="col-span-2 md:col-span-1">
                <a href="index.php" class="text-3xl font-extrabold text-highlight mb-4 block hover:text-soft-blue transition">
                    AZIZI.IO
                </a>
                <p class="text-gray-400 mt-2 text-sm leading-relaxed">
                    Platform e-book terpercaya yang menyediakan berbagai buku digital berkualitas 
                    untuk meningkatkan pengetahuan dan hiburan Anda.
                </p>
                <div class="flex space-x-4 mt-4 text-gray-400">
                    <a href="#" class="hover:text-soft-blue transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="hover:text-soft-blue transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-soft-blue transition"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-5 text-soft-blue">Menu Cepat</h3>
                <ul class="space-y-3">
                    <li><a href="index.php" class="text-gray-300 hover:text-highlight transition flex items-center"><i class="fas fa-angle-right mr-2 text-soft-blue"></i>Beranda</a></li>
                    <li><a href="books.php" class="text-gray-300 hover:text-highlight transition flex items-center"><i class="fas fa-angle-right mr-2 text-soft-blue"></i>Koleksi Buku</a></li>
                    <li><a href="contact.php" class="text-gray-300 hover:text-highlight transition flex items-center"><i class="fas fa-angle-right mr-2 text-soft-blue"></i>Kontak</a></li>
                    <li><a href="about.php" class="text-gray-300 hover:text-highlight transition flex items-center"><i class="fas fa-angle-right mr-2 text-soft-blue"></i>Tentang Kami</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-5 text-soft-blue">Kategori Populer</h3>
                <ul class="space-y-3">
                    <?php
                    // Logika PHP DIBIARKAN SAMA PERSIS
                    // $pop_kategori perlu didefinisikan sebelum digunakan jika file ini adalah footer.php
                    if (isset($connection)) {
                        $pop_kategori = $connection->query("
                            SELECT k.kategori, k.kategori_id 
                            FROM kategori k 
                            JOIN buku b ON k.kategori_id = b.kategori_id 
                            GROUP BY k.kategori_id 
                            ORDER BY COUNT(b.buku_id) DESC 
                            LIMIT 5
                        ");
                    } else {
                         // Fallback jika $connection tidak tersedia (untuk menghindari error)
                         $pop_kategori = new ArrayObject(); 
                    }

                    if ($pop_kategori && $pop_kategori->num_rows > 0) {
                        while ($kategori = $pop_kategori->fetch_assoc()) {
                            echo "<li><a href='books.php?kategori={$kategori['kategori_id']}' class='text-gray-300 hover:text-highlight transition flex items-center'><i class='fas fa-angle-right mr-2 text-soft-blue'></i>{$kategori['kategori']}</a></li>";
                        }
                    } else {
                        echo "<li><span class='text-gray-400'>Belum ada kategori.</span></li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-5 text-soft-blue">Hubungi Kami</h3>
                <div class="space-y-4 text-gray-300 text-sm">
                    <p class="flex items-start">
                        <i class="fas fa-envelope mr-3 mt-1 text-highlight"></i> 
                        Azizi@azizi.com
                    </p>
                    <p class="flex items-start">
                        <i class="fas fa-phone-alt mr-3 mt-1 text-highlight"></i> 
                        +62 896 0157 6276
                    </p>
                    <p class="flex items-start">
                        <i class="fas fa-map-marker-alt mr-3 mt-1 text-highlight"></i> 
                        Jl. Kapten Ikhsan Wahyu Endriarto No. 01, Kota Yogyakarta
                    </p>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-700 mt-12 pt-6 text-center text-gray-500 text-sm">
    <p class="font-medium">&copy; 2025 <span class="text-soft-blue">Azizi.io</span>. Hak Cipta Dilindungi Undang-Undang.</p>
    <p class="mt-1 text-gray-400">Didukung oleh Teknologi Digital dan <span class="text-highlight">Literasi Modern</span>.</p>
</div>
    </div>
</footer>