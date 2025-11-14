<?php
require_once '../../config/database.php';

if(isset($_POST['delete_comment'])){
    $comment_id = $_POST['comment_id'];

    try {
        // Cek apakah comment ada
        $check_stmt = $connection->prepare("SELECT comment_id FROM comments WHERE comment_id = ?");
        $check_stmt->bind_param("i", $comment_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Comment tidak ditemukan");
        }
        $check_stmt->close();

        $stmt = $connection->prepare("DELETE FROM comments WHERE comment_id = ?");
        $stmt->bind_param("i", $comment_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menghapus comment");
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