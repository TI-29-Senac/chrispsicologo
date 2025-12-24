<?php
session_start();

// --- CONFIGURAÇÃO DE CORS (SEGURANÇA) ---
// Permitir requisições de qualquer origem (Essencial para o Electron/Localhost)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Intercepta e responde imediatamente a requisições preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
require __DIR__ . '/../vendor/autoload.php';
use App\Psico\Rotas\Rotas;
use Bramus\Router\Router;
use App\Psico\Database\Database;

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
$rotas = Rotas::get();

// ==================================================================
// ROTAS MANUAIS (Prioridade Alta)
// Adicionamos aqui para garantir que funcionem independente do loop
// ==================================================================

// 1. Rota de Login do Desktop (A CORREÇÃO ESTÁ AQUI)
$router->post('/api/desktop/login', function() use ($db) {
    $classeController = 'App\\Psico\\Controllers\\DesktopApiController';

    if (class_exists($classeController)) {
        // Tenta instanciar. Se o controller pedir $db, passamos. Se não, tentamos sem.
        try {
            // Tenta passar o banco de dados (maioria dos controllers precisa)
            $controller = new $classeController($db);
        } catch (\ArgumentCountError $e) {
            // Se o construtor não aceitar argumentos, instancia vazio
            $controller = new $classeController();
        }

        // Executa o método login
        $controller->login();
    } else {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'error' => 'Erro Interno: Controller DesktopApiController não foi encontrado.'
        ]);
    }
});

// 2. Rota para o PIX
$router->post('/gerar-pix', function() use ($db) {
    (new \App\Psico\Controllers\ApiController())->gerarPix();
});


// ==================================================================
// ROTAS AUTOMÁTICAS (Loops)
// ==================================================================

// --- LOOP GET ---
foreach ($rotas['GET'] as $uri => $action) {
    $uri_parsed = preg_replace('/\{\w+\}/', '(.+)', $uri);

    $router->get($uri_parsed, function(...$params) use ($action, $router, $db) {
        list($controller, $method) = explode('@', $action);
        $fullController = "App\\Psico\\Controllers\\" . $controller;

        if (!class_exists($fullController)) {
            http_response_code(500);
            echo "O controlador GET ($controller) não foi encontrado.";
            return;
        }

        // Lógica de instanciação do loop original
        if ($fullController === "App\\Psico\\Controllers\\UsuarioController") {
            $controllerInstance = new $fullController(); 
        } else {
            $controllerInstance = new $fullController($db); 
        }

        call_user_func_array([$controllerInstance, $method], $params);
    });
}

// --- LOOP POST ---
foreach ($rotas['POST'] as $uri => $action) {
    $uri_parsed = preg_replace('/\{\w+\}/', '(.+)', $uri);

    $router->post($uri_parsed, function(...$params) use ($action, $router, $db) {
        list($controller, $method) = explode('@', $action);
        $fullController = "App\\Psico\\Controllers\\" . $controller;

        if (!class_exists($fullController)) {
            http_response_code(500);
            echo "O controlador POST ($controller) não foi encontrado.";
            return;
        }

        // Lógica de instanciação do loop original
        if ($fullController === "App\\Psico\\Controllers\\UsuarioController") {
            $controllerInstance = new $fullController();
        } else {
            $controllerInstance = new $fullController($db);
        }

        call_user_func_array([$controllerInstance, $method], $params);
    });
}

// --- ROTA 404 ---
$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json'); // Resposta JSON ajuda o Electron a entender melhor
    echo json_encode(['success' => false, 'error' => 'Rota não encontrada (404)']);
});

// --- EXECUTA O ROTEADOR ---
$router->run();