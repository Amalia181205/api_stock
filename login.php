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


// header("Content-Type: application/json");

// // Konfigurasi koneksi ke MySQL
// $host = "localhost";
// $user = "root";   
// $pass = "";
// $db   = "db_stock";

// $conn = new mysqli($host, $user, $pass, $db);

// // Cek koneksi
// if ($conn->connect_error) {
//     echo json_encode([
//         "status" => "error",
//         "message" => "Koneksi database gagal!"
//     ]);
//     exit;
// }

// // 1. Terima token yang dikirim dari Flutter
// $idToken = $_POST["id_token"] ?? "";

// if (empty($idToken)) {
//     echo json_encode([
//         "status" => "error",
//         "message" => "Token kosong!"
//     ]);
//     exit;
// }

// // 2. Verifikasi token ke Google
// $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $idToken;

// $context = stream_context_create([
//     "http" => [
//         "ignore_errors" => true
//     ]
// ]);

// $response = file_get_contents($url, false, $context);
// $userData = json_decode($response, true);

// if (isset($userData["error"])) {
//     echo json_encode([
//         "status" => "error",
//         "message" => "Token tidak valid!"
//     ]);
//     exit;
// }

// // 3. Token valid — ambil data dari Google
// $email = $conn->real_escape_string($userData["email"] ?? "");
// $nama  = $conn->real_escape_string($userData["name"] ?? "");

// // 4. Cek apakah user sudah pernah daftar
// $cekUser = $conn->query("SELECT id FROM users WHERE email = '$email'");

// if ($cekUser && $cekUser->num_rows == 0) {
//     // Belum ada → insert data baru
//     $conn->query("INSERT INTO users (nama, email) VALUES ('$nama', '$email')");
//     $pesan = "Akun baru berhasil didaftarkan di StokAll!";
// } else {
//     // Sudah ada → login biasa
//     $pesan = "Selamat datang kembali!";
// }

// // Response JSON
// echo json_encode([
//     "status" => "success",
//     "message" => $pesan
// ]);

// $conn->close();
// ?