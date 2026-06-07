<?php

require_once __DIR__ . '/config.php';

/**
 * Generate Shopee Signature
 */
function generateSign($path, $timestamp, $accessToken = '', $shopId = '')
{
    $baseString =
        SHOPEE_PARTNER_ID .
        $path .
        $timestamp .
        $accessToken .
        $shopId;

    return hash_hmac('sha256', $baseString, SHOPEE_PARTNER_KEY);
}

/**
 * Request API Shopee
 */
function shopeeRequest($url)
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}