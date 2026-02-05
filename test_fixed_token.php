<?php
// test_fixed_token.php

require __DIR__ . '/vendor/autoload.php';

use App\Psico\Database\Config;

// Load Env
Config::get();

$baseUrl = 'http://localhost:9000/backend';
$apiToken = $_ENV['API_TOKEN'] ?? '';

if (empty($apiToken)) {
    die("API_TOKEN not found in .env\n");
}

echo "Testing access with Fixed API Token: " . substr($apiToken, 0, 10) . "...\n";

function makeRequest($url, $method = 'GET', $data = [], $token = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['code' => $httpCode, 'body' => $response];
}

// Try to access a protected route (e.g. get users) using the Fixed Token
echo "\nAttempting Sync (Get Users) with Fixed Token...\n";
$syncRes = makeRequest($baseUrl . '/api/usuarios', 'GET', [], $apiToken);

echo "Response Code: " . $syncRes['code'] . "\n";
echo "Body: " . substr($syncRes['body'], 0, 500) . "...\n";

if ($syncRes['code'] == 200) {
    echo "\nSUCCESS: Accessed protected route with Fixed API Token!\n";
} else {
    echo "\nFAILURE: Could not access protected route with Fixed API Token.\n";
}
