<?php
require_once '../../config/database.php';

if(isset($_POST['update_favorit'])){
    $favorit_id = $_POST['favorit_id'];
    $rating = $_POST['rating'];

    try {
        // Validasi rating
        if ($rating < 0 || $rating > 5) {
            throw new Exception("Rating harus antara 0 dan 5");
        }

        $stmt = $connection->prepare(
            "UPDATE buku_favorit SET rating=? WHERE favorit_id=?"
        );
        $stmt->bind_param("di", $rating, $favorit_id);

        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Tidak ada perubahan data atau favorit tidak ditemukan");
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