<?php
require __DIR__ . '/vendor/autoload.php';

// Simula ambiente
$_ENV['JWT_SECRET'] = 'teste_secret_must_be_very_long_to_work_32chars';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_ORIGIN'] = 'test';

// Mock do Input
$input = json_encode(['email' => 'teste@teste.com', 'senha' => '123456']); // Ajuste com credenciais reais se souber, ou mocke o DB
// Como não temos credenciais reais fáceis e o DB é singleton, melhor testar unitariamente o Auth Helper isolado.

// Teste do Helper Auth
use App\Psico\Core\Auth;

echo "1. Gerando Token...\n";
$token = Auth::generate(1, 'admin');
echo "Token Gerado: " . substr($token, 0, 20) . "...\n";

echo "2. Validando Token...\n";
$payload = Auth::validate($token);
if ($payload && $payload->sub == 1 && $payload->role == 'admin') {
    echo "SUCCESS: Token validado corretamente.\n";
} else {
    echo "FAIL: Token inválido.\n";
    print_r($payload);
}

echo "3. Testando Token Inválido...\n";
$invalid = Auth::validate($token . 'ba');
if ($invalid === null) {
    echo "SUCCESS: Token adulterado rejeitado.\n";
} else {
    echo "FAIL: Token adulterado aceito.\n";
}
