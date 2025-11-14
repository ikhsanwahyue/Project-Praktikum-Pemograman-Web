<?php
require_once '../../config/database.php';

if(isset($_POST['add_penulis'])){
    $name = $_POST['name'];
    $email = $_POST['email'];

    try {
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid");
        }

        $stmt = $connection->prepare(
            "INSERT INTO penulis (name, email) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $name, $email);

        $stmt->execute();
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