<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "db_stock");

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$name || !$email || !$password) {
    echo json_encode([
        "success" => false,
        "message" => "Data tidak lengkap"
    ]);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

// cek email
$cek = $conn->query("SELECT id FROM users WHERE email='$email'");
if ($cek->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Email sudah terdaftar"
    ]);
    exit;
}

$conn->query("INSERT INTO users (nama, email, password, provider)
VALUES ('$name', '$email', '$hash', 'manual')");

echo json_encode([
    "success" => true,
    "message" => "Register berhasil"
]);