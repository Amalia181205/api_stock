<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/fcm_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "error" => "Method not allowed"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$productId = intval($input["product_id"] ?? 0);
$newStock  = intval($input["stok"] ?? -1);

if ($productId <= 0 || $newStock < 0) {
    http_response_code(400);
    echo json_encode([
        "error" => "Data tidak valid"
    ]);
    exit;
}

$conn = getConnection();

$stmt = $conn->prepare("
    SELECT
        id,
        nama_produk,
        stok,
        harga
    FROM products
    WHERE id = ?
");

$stmt->bind_param("i", $productId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "error" => "Produk tidak ditemukan"
    ]);
    exit;
}

$product = $result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("
    UPDATE products
    SET stok = ?
    WHERE id = ?
");

$stmt->bind_param("ii", $newStock, $productId);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("
    SELECT fcm_token
    FROM fcm_tokens
");

$stmt->execute();
$tokens = $stmt->get_result();
$stmt->close();

$title = "";
$body  = "";
$type  = "";

if ($newStock == 0) {

    $title = "Stok Habis";
    $body  = $product["nama_produk"] . " sudah habis";
    $type  = "out_of_stock";

} elseif ($newStock <= 5) {

    $title = "Peringatan Stok";
    $body  = $product["nama_produk"] . " tinggal " . $newStock . " unit";
    $type  = "low_stock";

} else {

    echo json_encode([
        "success" => true,
        "message" => "Stok berhasil diperbarui"
    ]);

    $conn->close();
    exit;
}

$fcmResults = [];

while ($row = $tokens->fetch_assoc()) {

    if (!empty($row["fcm_token"])) {

        $fcmResults[] = sendFcmNotification(
            $row["fcm_token"],
            $title,
            $body,
            [
                "product_id" => $productId,
                "stok"       => $newStock,
                "type"       => $type
            ]
        );
    }
}

$conn->close();

echo json_encode([
    "success"    => true,
    "message"    => "Stok berhasil diperbarui & notifikasi dikirim",
    "fcm_result" => $fcmResults
]);