<?php

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
