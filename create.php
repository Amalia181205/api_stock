<?php

header("Access-Control-Allow-Origin: *"); // Mengizinkan akses API dari semua domain (CORS)
header("Content-Type: application/json"); // Menentukan format response sebagai JSON(errorny disini ngbcany html)

$koneksi = new mysqli("localhost", "root", "", "db_stock");

if ($koneksi->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Koneksi gagal"
    ]);
    exit;
}

if (!isset($_POST['nama_produk']) || !isset($_POST['stok']) || !isset($_POST['harga'])) {
    echo json_encode([
        "success" => false,
        "message" => "Data POST kosong"
    ]);
    exit;
}

$nama_produk = $_POST['nama_produk'];
$stok = $_POST['stok'];
$harga = $_POST['harga'];
$shopee_stock = $_POST['shopee_stock'] ?? 0;
$tokopedia_stock = $_POST['tokopedia_stock'] ?? 0;

$query = mysqli_query($koneksi,
    "INSERT INTO tb_stock (nama_produk, stok, harga, shopee_stock, tokopedia_stock)
     VALUES ('$nama_produk', '$stok', '$harga', '$shopee_stock', '$tokopedia_stock')"
);

echo json_encode([
    "success" => $query,
    "message" => $query ? "Produk berhasil ditambahkan" : mysqli_error($koneksi)
]);  
