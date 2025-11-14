<?php
require_once '../../config/database.php';

if(isset($_POST['add_favorit'])){
    $buku_id = $_POST['buku_id'];
    $user_id = $_POST['user_id'];
    $rating = $_POST['rating'];

    try {
        // Validasi rating
        if ($rating < 0 || $rating > 5) {
            throw new Exception("Rating harus antara 0 dan 5");
        }

        // Cek apakah buku_id dan user_id ada di database
        $check_stmt = $connection->prepare("SELECT buku_id FROM buku WHERE buku_id = ?");
        $check_stmt->bind_param("i", $buku_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Buku tidak ditemukan");
        }
        $check_stmt->close();

        $check_stmt = $connection->prepare("SELECT user_id FROM user WHERE user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("User tidak ditemukan");
        }
        $check_stmt->close();

        // Insert data favorit
        $stmt = $connection->prepare(
            "INSERT INTO buku_favorit (buku_id, user_id, rating) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iid", $buku_id, $user_id, $rating);

        $stmt->execute();
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