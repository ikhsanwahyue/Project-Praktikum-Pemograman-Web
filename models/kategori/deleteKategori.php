<?php
require_once '../../config/database.php';

if(isset($_POST['delete_kategori'])){
    $kategori_id = $_POST['kategori_id'];

    try {
        // Cek apakah kategori ada
        $check_stmt = $connection->prepare("SELECT kategori_id FROM kategori WHERE kategori_id = ?");
        $check_stmt->bind_param("i", $kategori_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Kategori tidak ditemukan");
        }
        $check_stmt->close();

        // Cek apakah kategori masih digunakan di tabel buku
        $check_usage = $connection->prepare("SELECT buku_id FROM buku WHERE kategori_id = ?");
        $check_usage->bind_param("i", $kategori_id);
        $check_usage->execute();
        $check_usage->store_result();
        
        if($check_usage->num_rows > 0) {
            throw new Exception("Tidak dapat menghapus kategori karena masih digunakan oleh buku. Update kategori buku terlebih dahulu.");
        }
        $check_usage->close();

        $stmt = $connection->prepare("DELETE FROM kategori WHERE kategori_id = ?");
        $stmt->bind_param("i", $kategori_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menghapus kategori");
        }
        
        $stmt->close();
        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
    exit();
}