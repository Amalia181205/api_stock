<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "db_stock");

$idToken = $_POST["id_token"] ?? "";

if (!$idToken) {
    echo json_encode([
        "success" => false,
        "message" => "Token kosong"
    ]);
    exit;
}

// verifikasi Google
$url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $idToken;
$response = file_get_contents($url);
$data = json_decode($response, true);

if (isset($data["error"])) {
    echo json_encode([
        "success" => false,
        "message" => "Token tidak valid"
    ]);
    exit;
}

$email = $data["email"];
$nama = $data["name"];

// cek user
$cek = $conn->query("SELECT id FROM users WHERE email='$email'");

if ($cek->num_rows == 0) {
    $conn->query("INSERT INTO users (nama, email, provider)
    VALUES ('$nama', '$email', 'google')");
}

echo json_encode([
    "success" => true,
    "message" => "Login Google berhasil",
    "name" => $nama,
    "email" => $email
]);