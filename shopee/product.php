<?php

require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

// =========================
// AMBIL LIST PRODUK SHOPEE
// =========================

function getShopeeProducts($accessToken)
{
    $path = "/api/v2/product/get_item_list";
    $timestamp = time();

    $sign = generateSign($path, $timestamp, $accessToken, SHOPEE_SHOP_ID);

    $url = SHOPEE_BASE_URL . $path .
        "?partner_id=" . SHOPEE_PARTNER_ID .
        "&timestamp=$timestamp" .
        "&access_token=$accessToken" .
        "&shop_id=" . SHOPEE_SHOP_ID .
        "&sign=$sign" .
        "&page_size=20";

    return shopeeRequest($url);
}

// =========================
// ENDPOINT API
// =========================

$accessToken = $_GET['token'] ?? '';

if (!$accessToken) {
    echo json_encode([
        "success" => false,
        "message" => "Token kosong"
    ]);
    exit;
}

$data = getShopeeProducts($accessToken);

echo json_encode([
    "success" => true,
    "data" => $data
]);