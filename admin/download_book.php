<?php
require_once '../config/database.php';
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_logged_in']) && !isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = '../uploads/books/' . $filename;
    
    // Security check
    if(!file_exists($filepath) || !is_file($filepath)) {
        header("HTTP/1.0 404 Not Found");
        exit('File not found');
    }
    
    // Set headers for download
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    
    // Clear output buffer
    ob_clean();
    flush();
    
    readfile($filepath);
    exit();
} else {
    header("HTTP/1.0 400 Bad Request");
    exit('No file specified');
}
?>