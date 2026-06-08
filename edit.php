<?php

header("Access-Control-Allow-Origin: *"); // Mengizinkan akses API dari semua domain (CORS)
header("Content-Type: application/json"); // Menentukan format response sebagai JSON(errorny disini ngbcany html)

$koneksi = new mysqli("localhost", "root", "", "db_stock");

if ($koneksi->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Koneksi gagal: " . $koneksi->connect_error
    ]);
    exit;
}

if (
    !isset($_POST['id']) ||
    !isset($_POST['nama_produk']) ||
    !isset($_POST['stok']) ||
    !isset($_POST['harga'])
) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_POST['id']);
$nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
$stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
$harga = mysqli_real_escape_string($koneksi, $_POST['harga']);

$shopee_stock = mysqli_real_escape_string($koneksi, $_POST['shopee_stock']);
$tokopedia_stock = mysqli_real_escape_string($koneksi, $_POST['tokopedia_stock']);

$query = mysqli_query(
    $koneksi,
    "UPDATE tb_stock
     SET
        nama_produk='$nama_produk',
        stok='$stok',
        harga='$harga',
        shopee_stock='$shopee_stock',
        tokopedia_stock='$tokopedia_stock'
     WHERE id='$id'"
);

if ($query) {

    //  HISTORY EDIT
    mysqli_query($koneksi,
        "INSERT INTO tb_history (product_name, action, description)
         VALUES (
            '$nama_produk',
            'Update Produk',
            'Stok: $stok | Shopee: $shopee_stock | Tokopedia: $tokopedia_stock'
         )"
         );

    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil diubah"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($koneksi)
    ]);
} 
