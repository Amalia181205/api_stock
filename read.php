<?php

header("Access-Control-Allow-Origin: *"); // Mengizinkan akses API dari semua domain (CORS)
header("Content-Type: application/json"); // Menentukan format response sebagai JSON(errorny disini ngbcany html)

$koneksi = new mysqli("localhost", "root", "", "db_stock");
$query = mysqli_query($koneksi, "SELECT * FROM tb_stock");
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
echo json_encode($data);
