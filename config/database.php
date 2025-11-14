<?php

// pastikan sama dengan punya mu, jika ada password masukan pass ke variabel pass
$host = "localhost";
$username = "root";
$password = "";
$database = "e_book";

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = new mysqli($host, $username, $password, $database);
} catch (mysqli_sql_exception $e){
    echo "Error occured : " . $e->getMessage();
}