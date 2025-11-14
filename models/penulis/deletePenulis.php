<?php
require_once '../../config/database.php';

if(isset($_POST['delete_penulis'])){
    $penulis_id = $_POST['penulis_id'];

    try {
        // Cek apakah penulis ada
        $check_stmt = $connection->prepare("SELECT penulis_id FROM penulis WHERE penulis_id = ?");
        $check_stmt->bind_param("i", $penulis_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Penulis tidak ditemukan");
        }
        $check_stmt->close();

        // Cek apakah penulis masih digunakan di tabel buku
        $check_usage = $connection->prepare("SELECT buku_id FROM buku WHERE penulis_id = ?");
        $check_usage->bind_param("i", $penulis_id);
        $check_usage->execute();
        $check_usage->store_result();
        
        if($check_usage->num_rows > 0) {
            throw new Exception("Tidak dapat menghapus penulis karena masih memiliki buku. Hapus buku terlebih dahulu.");
        }
        $check_usage->close();

        $stmt = $connection->prepare("DELETE FROM penulis WHERE penulis_id = ?");
        $stmt->bind_param("i", $penulis_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menghapus penulis");
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