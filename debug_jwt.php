<?php
require __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

echo "JWT Class: " . JWT::class . "\n";

$payload = ['user_id' => 1];
$key = 'secret_key';
$alg = 'HS256';

try {
    echo "Trying encode...\n";
    $token = JWT::encode($payload, $key, $alg);
    echo "Encode Success: $token\n";
    
    echo "Trying decode...\n";
    $decoded = JWT::decode($token, new Key($key, $alg));
    echo "Decode Success\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
