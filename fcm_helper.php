<?php 

require_once __DIR__ . '/config/credentials.php';

/**
 * Mengambil OAuth2 Access Token dari Google menggunakan Service Account.
 */
function getFcmAccessToken()
{
    $serviceAccount = json_decode(file_get_contents(SERVICE_ACCOUNT_PATH), true);

    $now    = time();
    $header = base64url_encode(json_encode([
        "alg" => "RS256",
        "typ" => "JWT"
    ]));

    $claim  = base64url_encode(json_encode([
        "iss"   => $serviceAccount["client_email"],
        "scope" => "https://www.googleapis.com/auth/firebase.messaging",
        "aud"   => "https://oauth2.googleapis.com/token",
        "iat"   => $now,
        "exp"   => $now + 3600
    ]));

    $signature = "";
    openssl_sign(
        "$header.$claim",
        $signature,
        openssl_pkey_get_private($serviceAccount["private_key"]),
        "SHA256"
    );

    $jwt = "$header.$claim." . base64url_encode($signature);

    // Tukarkan JWT dengan Access Token
    $ch = curl_init("https://oauth2.googleapis.com/token");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            "grant_type" => "urn:ietf:params:oauth2:grant-type:jwt-bearer",
            "assertion"  => $jwt
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false // TAMBAHAN AGAR TIDAK DIBLOKIR OLEH SSL XAMPP
    ]);

    // $result = json_decode(curl_exec($ch), true);
    // curl_close($ch);

    // return $result["access_token"] ?? null;

    $result = curl_exec($ch);

    if ($result === false) {
        return null;
    }

    $result = json_decode($result, true);

    if (!isset($result["access_token"])) {
        return null;
    }
}

/**
 * Mengirimkan notifikasi push ke satu FCM token.
 * @param string $fcmToken  Token perangkat tujuan
 * @param string $title     Judul notifikasi
 * @param string $body      Isi pesan notifikasi
 * @param array  $data      Data tambahan (opsional)
 */
function sendFcmNotification($fcmToken, $title, $body, $data = [])
{
    $accessToken = getFcmAccessToken();
    if (!$accessToken) return ["error" => "Gagal mendapatkan access token"];

    $projectId = FIREBASE_PROJECT_ID;
    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

    $payload = [
        "message" => [
            "token" => $fcmToken,
            "notification" => [
                "title" => $title,
                "body"  => $body
            ],
            "android" => [
                "notification" => [
                    "channel_id"    => "high_importance_channel",
                    "default_sound" => true
                ]
            ],
            "data" => array_map("strval", $data)
        ]
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false // TAMBAHAN AGAR TIDAK DIBLOKIR OLEH SSL XAMPP
    ]);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    return $response;
}

function base64url_encode($data)
{
    return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
}