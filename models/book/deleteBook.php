<?php
require_once '../../config/database.php';

if(isset($_POST['delete_buku'])){
    $buku_id = $_POST['buku_id'];

    try {
        // Cek apakah buku ada
        $check_stmt = $connection->prepare("SELECT buku_id FROM buku WHERE buku_id = ?");
        $check_stmt->bind_param("i", $buku_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Buku tidak ditemukan");
        }
        $check_stmt->close();

        // Hapus data terkait di tabel lain terlebih dahulu
        $connection->begin_transaction();

        // Hapus comments yang terkait
        $delete_comments = $connection->prepare("DELETE FROM comments WHERE buku_id = ?");
        $delete_comments->bind_param("i", $buku_id);
        $delete_comments->execute();
        $delete_comments->close();

        // Hapus buku_favorit yang terkait
        $delete_favorit = $connection->prepare("DELETE FROM buku_favorit WHERE buku_id = ?");
        $delete_favorit->bind_param("i", $buku_id);
        $delete_favorit->execute();
        $delete_favorit->close();

        // Hapus simpan_buku yang terkait
        $delete_simpan = $connection->prepare("DELETE FROM simpan_buku WHERE buku_id = ?");
        $delete_simpan->bind_param("i", $buku_id);
        $delete_simpan->execute();
        $delete_simpan->close();

        // Hapus buku
        $stmt = $connection->prepare("DELETE FROM buku WHERE buku_id = ?");
        $stmt->bind_param("i", $buku_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menghapus buku");
        }
        
        $stmt->close();
        $connection->commit();

        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        $connection->rollback();
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        $connection->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
    exit();
}