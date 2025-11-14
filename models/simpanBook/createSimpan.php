<?php
require_once '../../config/database.php';

if(isset($_POST['add_simpan'])){
    $buku_id = $_POST['buku_id'];
    $user_id = $_POST['user_id'];

    try {
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

        // Insert data simpan buku
        $stmt = $connection->prepare(
            "INSERT INTO simpan_buku (buku_id, user_id) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $buku_id, $user_id);

        $stmt->execute();
        $stmt->close();

        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        if($e->getCode() == 1062) {
            echo "Error: Buku sudah disimpan sebelumnya";
        } else {
            echo "Database Error: " . $e->getMessage();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
    exit();
}