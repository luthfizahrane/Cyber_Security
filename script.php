<?php

$url = "http://localhost/Rest1/server.php/product"; // Sesuaikan dengan endpoint
$headers = ["Content-Type: application/json"];
$body = json_encode(["productId" => 1]);

for ($i = 0; $i < 110; $i++) { // Kirim 110 permintaan
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Permintaan ke-" . ($i + 1) . " - Status: $httpCode, Response: $response\n";

    // Tunggu sedikit agar tidak terlalu cepat (opsional)
    usleep(50000); // 50ms
}