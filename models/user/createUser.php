<?php
require_once '../../config/database.php';

if(isset($_POST['add_user'])){
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Handle file upload untuk foto_profil
    $foto_profil = null;
    if(isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK){
        $foto_profil = file_get_contents($_FILES['foto_profil']['tmp_name']);
    }

    try {
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $connection->prepare(
            "INSERT INTO user (name, username, email, password, foto_profil) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssb", $name, $username, $email, $hashed_password, $foto_profil);

        if ($foto_profil) {
            $stmt->send_long_data(4, $foto_profil);
        }

        $stmt->execute();
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