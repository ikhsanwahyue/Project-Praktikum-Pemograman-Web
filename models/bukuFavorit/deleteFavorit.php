<?php
require_once '../../config/database.php';

if(isset($_POST['delete_favorit'])){
    $favorit_id = $_POST['favorit_id'];

    try {
        // Cek apakah favorit ada
        $check_stmt = $connection->prepare("SELECT favorit_id FROM buku_favorit WHERE favorit_id = ?");
        $check_stmt->bind_param("i", $favorit_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Favorit tidak ditemukan");
        }
        $check_stmt->close();

        $stmt = $connection->prepare("DELETE FROM buku_favorit WHERE favorit_id = ?");
        $stmt->bind_param("i", $favorit_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menghapus favorit");
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