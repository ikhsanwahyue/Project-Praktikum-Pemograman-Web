<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ebook';

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = new mysqli($host, $username, $password, $database);
} catch (mysqli_sql_exception $e){
    echo "Error occured : " . $e->getMessage();
}

// Fungsi untuk memeriksa apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk mendapatkan data user yang sedang login
function getCurrentUser($connection) {
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $stmt = $connection->prepare("SELECT * FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    return null;
}

?>