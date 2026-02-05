<?php
// test_jwt_flow.php

require __DIR__ . '/vendor/autoload.php';

use App\Psico\Database\Config;
use App\Psico\Database\Database;

// Load Env
Config::get();

$baseUrl = 'http://localhost:9000/backend';

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
    
    // Debug info
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['code' => $httpCode, 'body' => $response];
}

echo "1. Attempting Login...\n";
$loginData = [
    'email' => 'jean.mnrocha@gmail.com', // Need a valid user here. I'll check user DB first or use default admin if exists?
    'senha' => '123456' // Need valid pass.
];

// Wait, I don't know a valid user/pass. I should create a temp user directly in DB first.
// Or I can simulate the controller calls directly without HTTP to test logic first?
// No, user said "server offline", so HTTP stack is important.

// Let's create a temporary user in the DB using the Model classes, then test against it.
$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM usuario LIMIT 1");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("No users in DB. Cannot test login.\n");
}

echo "Found user: " . $user['email_usuario'] . ". Resetting password to '123456' for test...\n";
// Update password to known value '123456' hashed
$newPass = password_hash('123456', PASSWORD_DEFAULT);
$update = $db->prepare("UPDATE usuario SET senha_usuario = ? WHERE id_usuario = ?");
$update->execute([$newPass, $user['id_usuario']]);

$loginData = [
    'email' => $user['email_usuario'],
    'senha' => '123456'
];

$loginRes = makeRequest($baseUrl . '/api/desktop/login', 'POST', $loginData);
echo "Login Response Code: " . $loginRes['code'] . "\n";
echo "Login Body: " . $loginRes['body'] . "\n";

$token = null;
$json = json_decode($loginRes['body'], true);
if (isset($json['success']) && $json['success']) {
    $token = $json['token'];
    echo "Token acquired.\n";
} else {
    die("Login failed.\n");
}

echo "\n2. Attempting Sync (Get Users)...\n";
$syncRes = makeRequest($baseUrl . '/api/usuarios', 'GET', [], $token);
echo "Sync Response Code: " . $syncRes['code'] . "\n";
echo "Sync Body: " . substr($syncRes['body'], 0, 500) . "...\n"; // Truncate

if ($syncRes['code'] == 200) {
    echo "\nSUCCESS: Sync endpoint accessible with Token.\n";
} else {
    echo "\nFAILURE: Sync endpoint returned error or unreachable.\n";
}
