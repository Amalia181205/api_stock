<?php

header("Access-Control-Allow-Origin: *"); // Mengizinkan akses API dari semua domain (CORS)
header("Content-Type: application/json"); // Menentukan format response sebagai JSON(errorny disini ngbcany html)

$koneksi = new mysqli("localhost", "root", "", "db_stock");
$query = mysqli_query($koneksi, "SELECT * FROM tb_stock");
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

foreach ($data as &$item) {
    $item['selisih_shopee'] = $item['stok'] - $item['shopee_stock'];
    $item['selisih_tokopedia'] = $item['stok'] - $item['tokopedia_stock'];
}

echo json_encode($data);
