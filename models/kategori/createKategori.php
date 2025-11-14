<?php
require_once '../../config/database.php';

if(isset($_POST['add_kategori'])){
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];

    try {
        $stmt = $connection->prepare(
            "INSERT INTO kategori (kategori, deskripsi) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $kategori, $deskripsi);

        $stmt->execute();
        $stmt->close();

        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        if($e->getCode() == 1062) {
            echo "Error: Kategori sudah ada";
        } else {
            echo "Database Error: " . $e->getMessage();
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}