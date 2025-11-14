<?php
require_once '../../config/database.php';

if(isset($_POST['update_simpan'])){
    $simpan_id = $_POST['simpan_id'];
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

        $stmt = $connection->prepare(
            "UPDATE simpan_buku SET buku_id=?, user_id=? WHERE simpan_id=?"
        );
        $stmt->bind_param("iii", $buku_id, $user_id, $simpan_id);

        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Tidak ada perubahan data atau simpan buku tidak ditemukan");
        }
        
        $stmt->close();
        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        if($e->getCode() == 1062) {
            echo "Error: Kombinasi user dan buku sudah ada";
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