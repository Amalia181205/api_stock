<?php

header('Content-Type: application/json'); // Menentukan format response sebagai JSON(errorny disini ngbcany html)

$koneksi = new mysqli('localhost', 'root', '', 'db_stock');

if (!isset($_POST['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "ID tidak terkirim"
    ]);
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_POST['id']);

$data = mysqli_query(
    $koneksi,
    "DELETE FROM tb_stock WHERE id='$id'"
);

if ($data) {
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
