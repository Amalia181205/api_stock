<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$input    = json_decode(file_get_contents("php://input"), true);
$userId   = trim($input["user_id"] ?? "");
$fcmToken = trim($input["fcm_token"] ?? "");

if (empty($userId) || empty($fcmToken)) {
    http_response_code(400);
    echo json_encode(["error" => "user_id dan fcm_token wajib diisi"]);
    exit;
}

$conn = getConnection();

// INSERT atau UPDATE jika user_id sudah ada (UPSERT)
$stmt = $conn->prepare(
    "INSERT INTO fcm_tokens (user_id, fcm_token)
     VALUES (?, ?)
     ON DUPLICATE KEY UPDATE fcm_token = VALUES(fcm_token)"
);

$stmt->bind_param("ss", $userId, $fcmToken);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Token berhasil disimpan"
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Gagal menyimpan token"]);
}

$stmt->close();
$conn->close();