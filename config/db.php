<?php

// Konfigurasi koneksi database
define("DB_HOST", "localhost");
define("DB_USER", "root");      // Sesuaikan username MySQL Anda
define("DB_PASS", "");          // Sesuaikan password MySQL Anda
define("DB_NAME", "db_stock");

function getConnection()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode([
            "error" => "Koneksi database gagal: " . $conn->connect_error
        ]));
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}