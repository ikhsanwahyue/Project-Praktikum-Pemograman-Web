<?php
require_once '../../config/database.php';

if(isset($_POST['update_user'])){
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Handle file upload untuk foto_profil
    $foto_profil = null;
    $update_foto = false;
    
    if(isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK){
        $foto_profil = file_get_contents($_FILES['foto_profil']['tmp_name']);
        $update_foto = true;
    }

    try {
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid");
        }

        // Cek jika password diupdate
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            if ($update_foto) {
                $stmt = $connection->prepare(
                    "UPDATE user SET name=?, username=?, email=?, password=?, foto_profil=? WHERE user_id=?"
                );
                $stmt->bind_param("ssssbi", $name, $username, $email, $password, $foto_profil, $user_id);
                $stmt->send_long_data(4, $foto_profil);
            } else {
                $stmt = $connection->prepare(
                    "UPDATE user SET name=?, username=?, email=?, password=? WHERE user_id=?"
                );
                $stmt->bind_param("ssssi", $name, $username, $email, $password, $user_id);
            }
        } else {
            if ($update_foto) {
                $stmt = $connection->prepare(
                    "UPDATE user SET name=?, username=?, email=?, foto_profil=? WHERE user_id=?"
                );
                $stmt->bind_param("sssbi", $name, $username, $email, $foto_profil, $user_id);
                $stmt->send_long_data(3, $foto_profil);
            } else {
                $stmt = $connection->prepare(
                    "UPDATE user SET name=?, username=?, email=? WHERE user_id=?"
                );
                $stmt->bind_param("sssi", $name, $username, $email, $user_id);
            }
        }

        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Tidak ada perubahan data atau user tidak ditemukan");
        }
        
        $stmt->close();
        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        if($e->getCode() == 1062) {
            echo "Error: Username atau email sudah digunakan";
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