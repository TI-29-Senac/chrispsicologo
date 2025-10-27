<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Psico\Rotas\Rotas;
use Bramus\Router\Router;
use App\Psico\Database\Database; // Garante que o namespace está correto

try {
    // Usa o método estático correto para obter a instância do PDO
    $db = Database::getInstance();
} catch (\PDOException $e) {
    http_response_code(503); // Service Unavailable
    // Envia um JSON de erro claro
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]); // Adiciona mensagem de erro
    exit;
}

$router = new Router();
$router->setBasePath('/backend');
$rotas = Rotas::get();


foreach ($rotas['GET'] as $uri => $action) {

    $uri_parsed = preg_replace('/\{\w+\}/', '(.+)', $uri);

    // Passa $db para dentro do escopo da função anônima
    $router->get($uri_parsed, function(...$params) use ($action, $router, $db) {
        list($controller, $method) = explode('@', $action);
        $fullController = "App\\Psico\\Controllers\\" . $controller;

        if (!class_exists($fullController)) {
            http_response_code(500);
            echo "O controlador GET ($controller) não foi encontrado.";
            return;
        }

        // Instancia o controlador corretamente
        if ($fullController === "App\\Psico\\Controllers\\UsuarioController") {
            $controllerInstance = new $fullController(); // UsuarioController NÃO recebe $db no construtor
        } else {
            $controllerInstance = new $fullController($db); // Todos os outros recebem $db
        }

        call_user_func_array([$controllerInstance, $method], $params);
    });
}

foreach ($rotas['POST'] as $uri => $action) {
    $uri_parsed = preg_replace('/\{\w+\}/', '(.+)', $uri);

    // Passa $db para dentro do escopo da função anônima
    $router->post($uri_parsed, function(...$params) use ($action, $router, $db) {
        list($controller, $method) = explode('@', $action);
        $fullController = "App\\Psico\\Controllers\\" . $controller;

        if (!class_exists($fullController)) {
            http_response_code(500);
            echo "O controlador POST ($controller) não foi encontrado.";
            return;
        }

       // Instancia o controlador corretamente
       if ($fullController === "App\\Psico\\Controllers\\UsuarioController") {
            $controllerInstance = new $fullController(); // UsuarioController NÃO recebe $db no construtor
        } else {
            $controllerInstance = new $fullController($db); // Todos os outros recebem $db
        }

        call_user_func_array([$controllerInstance, $method], $params);
    });
}

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    echo "Página não encontrada (404)";
});


// Roda o roteador
$router->run();