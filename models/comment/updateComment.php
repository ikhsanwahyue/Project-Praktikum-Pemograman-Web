<?php
require_once '../../config/database.php';

if(isset($_POST['update_comment'])){
    $comment_id = $_POST['comment_id'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];

    try {
        // Validasi email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format email tidak valid");
        }

        // Validasi comment tidak kosong
        if (empty(trim($comment))) {
            throw new Exception("Comment tidak boleh kosong");
        }

        $stmt = $connection->prepare(
            "UPDATE comments SET email=?, comment=? WHERE comment_id=?"
        );
        $stmt->bind_param("ssi", $email, $comment, $comment_id);

        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Tidak ada perubahan data atau comment tidak ditemukan");
        }
        
        $stmt->close();
        header("Location: ../index.php?success=1");
        exit();

    } catch (mysqli_sql_exception $e){
        echo "Database Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../index.php");
    exit();
}