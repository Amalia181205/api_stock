<?php

header('Content-Type: application/json');

$koneksi = new mysqli('localhost', 'root', '', 'db_stock');

$id = $_POST['id'];

// ambil nama produk dulu
$get = mysqli_query($koneksi, "SELECT nama_produk FROM tb_stock WHERE id='$id'");
$row = mysqli_fetch_assoc($get);
$nama = $row['nama_produk'];

$query = mysqli_query($koneksi,
    "DELETE FROM tb_stock WHERE id='$id'"
);

if ($query) {

    mysqli_query($koneksi,
        "INSERT INTO tb_history (product_name, action, description)
         VALUES ('$nama', 'DELETE', 'Produk dihapus')"
    );

    echo json_encode([
        "success" => true,
        "message" => "Produk berhasil dihapus"
    ]);

} else {
    echo json_encode([
        "success" => false,
        "message" => mysqli_error($koneksi)
    ]);
}
