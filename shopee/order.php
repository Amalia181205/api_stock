<?php

require_once __DIR__ . '/auth.php';

header('Content-Type: application/json');

/**
 * AMBIL ORDER SHOPEE
 */
function getShopeeOrders($accessToken)
{
    $path = "/api/v2/order/get_order_list";
    $timestamp = time();

    $sign = generateSign($path, $timestamp, $accessToken, SHOPEE_SHOP_ID);

    $url = SHOPEE_BASE_URL . $path .
        "?partner_id=" . SHOPEE_PARTNER_ID .
        "&timestamp=$timestamp" .
        "&access_token=$accessToken" .
        "&shop_id=" . SHOPEE_SHOP_ID .
        "&time_range_field=create_time" .
        "&page_size=20" .
        "&sign=$sign";

    return shopeeRequest($url);
}

/**
 * ENDPOINT
 */
$accessToken = $_GET['token'] ?? '';

if (!$accessToken) {
    echo json_encode([
        "success" => false,
        "message" => "Token kosong"
    ]);
    exit;
}

$data = getShopeeOrders($accessToken);

echo json_encode([
    "success" => true,
    "data" => $data
]);