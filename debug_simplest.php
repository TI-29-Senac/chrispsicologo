<?php
require __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

try {
    $t = JWT::encode(['a'=>1], 'key', 'HS256');
    echo "OK: $t";
} catch (\Throwable $e) {
    echo "ERR: " . $e->getMessage();
}
