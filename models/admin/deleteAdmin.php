<?php
require_once '../../config/database.php';

if(isset($_POST['delete_admin'])){
    $admin_id = $_POST['admin_id'];

    try {
        // Cek apakah admin ada
        $check_stmt = $connection->prepare("SELECT admin_id FROM admin WHERE admin_id = ?");
        $check_stmt->bind_param("i", $admin_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if($check_stmt->num_rows === 0) {
            throw new Exception("Admin tidak ditemukan");
        }
        $check_stmt->close();

        $stmt = $connection->prepare("DELETE FROM admin WHERE admin_id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception("Gagal menghapus admin");
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