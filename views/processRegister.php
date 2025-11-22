<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($nama_lengkap) || empty($email) || empty($password)) {
        header("Location: daftar.php?status=error");
        exit();
    }
    
    if (strlen($password) < 6) {
        header("Location: daftar.php?status=error");
        exit();
    }
    
    try {
        // Cek apakah email sudah terdaftar
        $check_email = $connection->prepare("SELECT user_id FROM user WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        
        if ($check_email->num_rows > 0) {
            header("Location: daftar.php?status=email_exists");
            exit();
        }
        
        // Generate username dari email (ambil bagian sebelum @)
        $username = strtolower(explode('@', $email)[0]);
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user baru
        $insert_user = $connection->prepare("INSERT INTO user (name, username, email, password) VALUES (?, ?, ?, ?)");
        $insert_user->bind_param("ssss", $nama_lengkap, $username, $email, $hashed_password);
        
        if ($insert_user->execute()) {
            header("Location: login.php?status=registered");
            exit();
        } else {
            header("Location: daftar.php?status=error");
            exit();
        }
        
    } catch (Exception $e) {
        header("Location: daftar.php?status=error");
        exit();
    }
} else {
    header("Location: daftar.php");
    exit();
}
?>