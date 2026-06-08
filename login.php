<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "db_stock");

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode([
        "success" => false,
        "message" => "Email / password kosong"
    ]);
    exit;
}

$result = $conn->query("SELECT * FROM users WHERE email='$email'");

if ($result->num_rows == 0) {
    echo json_encode([
        "success" => false,
        "message" => "User tidak ditemukan"
    ]);
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Password salah"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Login berhasil",
    "name" => $user['nama'],
    "email" => $user['email']
]);
