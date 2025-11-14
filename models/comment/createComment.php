<?php
require_once '../../config/database.php';

if(isset($_POST['add_comment'])){
    $user_id = $_POST['user_id'];
    $buku_id = $_POST['buku_id'];
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

        // Cek apakah buku_id dan user_id ada di database
        $check_stmt = $connection->prepare("SELECT buku_id FROM buku WHERE buku_id = ?");
        $check_stmt->bind_param("i", $buku_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Buku tidak ditemukan");
        }
        $check_stmt->close();

        $check_stmt = $connection->prepare("SELECT user_id, email FROM user WHERE user_id = ?");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        $check_stmt->bind_result($db_user_id, $db_email);
        $check_stmt->fetch();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("User tidak ditemukan");
        }
        
        // Verifikasi email user
        if ($db_email !== $email) {
            throw new Exception("Email tidak sesuai dengan user");
        }
        $check_stmt->close();

        // Insert comment
        $stmt = $connection->prepare(
            "INSERT INTO comments (user_id, buku_id, email, comment) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("iiss", $user_id, $buku_id, $email, $comment);

        $stmt->execute();
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