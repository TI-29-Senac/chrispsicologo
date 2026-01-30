<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Psico\Rotas\Rotas;
use Bramus\Router\Router;
use App\Psico\Database\Database;
use App\Psico\Database\Config;

// Garante que o .env seja carregado
Config::get();

// --- CONFIGURAÇÃO DE CORS (SEGURANÇA) ---
$allowedOrigin = $_ENV['CORS_ALLOWED_ORIGIN'] ?? '*';
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Se for *, permite tudo. Se for específico, verifica se bate com a origem da requisição.
if ($allowedOrigin === '*' || $origin === $allowedOrigin) {
    header("Access-Control-Allow-Origin: " . ($allowedOrigin === '*' ? '*' : $origin));
}

header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Responde imediatamente a requisições de "preflight" (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}


// --- CONEXÃO COM O BANCO DE DADOS ---
try {
    $db = Database::getInstance();
} catch (\PDOException $e) {
    http_response_code(503); 
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit;
}

// --- INICIALIZAÇÃO DO ROTEADOR ---
$router = new Router();
$router->setBasePath('/backend');

// Registra todas as rotas definidas na classe Rotas
Rotas::register($router);

// --- ROTA 404 ---
$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json'); // Resposta JSON ajuda o Electron a entender melhor
    echo json_encode(['success' => false, 'error' => 'Rota não encontrada (404)']);
});

// --- EXECUTA O ROTEADOR ---
$router->run();