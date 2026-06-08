<?php
header("Content-Type: application/json");

$koneksi = new mysqli("localhost", "root", "", "db_stock");

$result = mysqli_query($koneksi, "
    SELECT * FROM tb_history 
    ORDER BY created_at DESC
");

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);