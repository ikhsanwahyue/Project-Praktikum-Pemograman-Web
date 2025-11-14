<?php
require_once '../../config/database.php';

if(isset($_POST['update_kategori'])){
    $kategori_id = $_POST['kategori_id'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];

    try {
        $stmt = $connection->prepare(
            "UPDATE kategori SET kategori=?, deskripsi=? WHERE kategori_id=?"
        );
        $stmt->bind_param("ssi", $kategori, $deskripsi, $kategori_id);

        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Tidak ada perubahan data atau kategori tidak ditemukan");
        }
        
        $stmt->close();
        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        if($e->getCode() == 1062) {
            echo "Error: Kategori sudah ada";
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