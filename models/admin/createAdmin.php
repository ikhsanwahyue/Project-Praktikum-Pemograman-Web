<?php
require_once '../../config/database.php';

if(isset($_POST['add_admin'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $connection->prepare(
            "INSERT INTO admin (username, password) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $username, $hashed_password);

        $stmt->execute();
        $stmt->close();

        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        if($e->getCode() == 1062) {
            echo "Error: Username sudah digunakan";
        } else {
            echo "Database Error: " . $e->getMessage();
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}