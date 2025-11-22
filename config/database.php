<?php

// session_start();

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

// Base URL configuration
define('BASE_URL', '/Project-Praktikum-Pemograman-Web');
define('CSS_PATH', BASE_URL . '/public/css/style.css');
define('IMG_PATH', BASE_URL . '/public/asset/');