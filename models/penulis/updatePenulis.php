<?php
require_once '../../config/database.php';

if(isset($_POST['update_penulis'])){
    $penulis_id = $_POST['penulis_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    try {
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid");
        }

        $stmt = $connection->prepare(
            "UPDATE penulis SET name=?, email=? WHERE penulis_id=?"
        );
        $stmt->bind_param("ssi", $name, $email, $penulis_id);

        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Tidak ada perubahan data atau penulis tidak ditemukan");
        }
        
        $stmt->close();
        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        if($e->getCode() == 1062) {
            echo "Error: Email sudah digunakan";
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