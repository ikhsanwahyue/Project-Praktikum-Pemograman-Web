<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($email) || empty($password)) {
        header("Location: login.php?status=failed");
        exit();
    }
    
    try {
        // Cari user berdasarkan email
        $stmt = $connection->prepare("SELECT user_id, name, username, email, password FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                header("Location: beranda.php");
                exit();
            } else {
                header("Location: login.php?status=failed");
                exit();
            }
        } else {
            header("Location: login.php?status=failed");
            exit();
        }
        
    } catch (Exception $e) {
        header("Location: login.php?status=failed");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>