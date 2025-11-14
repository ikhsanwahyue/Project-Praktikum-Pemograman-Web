<?php
require_once '../../config/database.php';

if(isset($_POST['update_admin'])){
    $admin_id = $_POST['admin_id'];
    $username = $_POST['username'];

    try {
        // Cek jika password diupdate
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $connection->prepare(
                "UPDATE admin SET username=?, password=? WHERE admin_id=?"
            );
            $stmt->bind_param("ssi", $username, $password, $admin_id);
        } else {
            $stmt = $connection->prepare(
                "UPDATE admin SET username=? WHERE admin_id=?"
            );
            $stmt->bind_param("si", $username, $admin_id);
        }

        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Tidak ada perubahan data atau admin tidak ditemukan");
        }
        
        $stmt->close();
        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        if($e->getCode() == 1062) {
            echo "Error: Username sudah digunakan";
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