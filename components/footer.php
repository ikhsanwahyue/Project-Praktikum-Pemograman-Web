<footer class="bg-gray-800 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-xl font-bold mb-4">Tentang Kami</h3>
                <p class="text-gray-300">
                    Platform e-book terpercaya yang menyediakan berbagai buku digital berkualitas 
                    untuk meningkatkan pengetahuan dan hiburan Anda.
                </p>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-xl font-bold mb-4">Menu Cepat</h3>
                <ul class="space-y-2">
                    <li><a href="index.php" class="text-gray-300 hover:text-white transition">Beranda</a></li>
                    <li><a href="books.php" class="text-gray-300 hover:text-white transition">Koleksi Buku</a></li>
                    <li><a href="contact.php" class="text-gray-300 hover:text-white transition">Kontak</a></li>
                </ul>
            </div>
            
            <!-- Categories -->
            <div>
                <h3 class="text-xl font-bold mb-4">Kategori Populer</h3>
                <ul class="space-y-2">
                    <?php
                    $pop_kategori = $connection->query("
                        SELECT k.kategori 
                        FROM kategori k 
                        JOIN buku b ON k.kategori_id = b.kategori_id 
                        GROUP BY k.kategori_id 
                        ORDER BY COUNT(b.buku_id) DESC 
                        LIMIT 5
                    ");
                    while ($kategori = $pop_kategori->fetch_assoc()) {
                        echo "<li><a href='books.php?kategori={$kategori['kategori_id']}' class='text-gray-300 hover:text-white transition'>{$kategori['kategori']}</a></li>";
                    }
                    ?>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h3 class="text-xl font-bold mb-4">Kontak</h3>
                <div class="space-y-2 text-gray-300">
                    <p>Email: info@ebook-university.com</p>
                    <p>Telepon: +62 123 4567 890</p>
                    <p>Alamat: Jl. Informatika No. 123</p>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 E-Book University. All rights reserved.</p>
        </div>
    </div>
</footer>