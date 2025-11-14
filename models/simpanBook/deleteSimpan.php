<?php
require_once '../../config/database.php';

if(isset($_POST['delete_simpan'])){
    $simpan_id = $_POST['simpan_id'];

    try {
        // Cek apakah simpan buku ada
        $check_stmt = $connection->prepare("SELECT simpan_id FROM simpan_buku WHERE simpan_id = ?");
        $check_stmt->bind_param("i", $simpan_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Simpan buku tidak ditemukan");
        }
        $check_stmt->close();

        $stmt = $connection->prepare("DELETE FROM simpan_buku WHERE simpan_id = ?");
        $stmt->bind_param("i", $simpan_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menghapus simpan buku");
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