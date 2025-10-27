<?php

namespace App\Psico;

session_start();

require __DIR__ . '/../vendor/autoload.php';

use App\Psico\Rotas\Rotas;
use Bramus\Router\Router;

$router = new Router();

$router->setBasePath('/backend'); 


$rotas = Rotas::get();


foreach ($rotas['GET'] as $uri => $action) {

    $uri_parsed = preg_replace('/\{\w+\}/', '(.+)', $uri); 

    $router->get($uri_parsed, function(...$params) use ($action, $router) {
        list($controller, $method) = explode('@', $action);
        $fullController = "App\\Psico\\Controllers\\" . $controller;

        if (!class_exists($fullController)) {
            http_response_code(500);
            echo "O controlador GET ($controller) não foi encontrado.";
            return;
        }

        $controllerInstance = new $fullController();

        call_user_func_array([$controllerInstance, $method], $params);
    });
}

foreach ($rotas['POST'] as $uri => $action) {
    $uri_parsed = preg_replace('/\{\w+\}/', '(.+)', $uri);

    $router->post($uri_parsed, function(...$params) use ($action, $router) {
        list($controller, $method) = explode('@', $action);
        $fullController = "App\\Psico\\Controllers\\" . $controller;
        
        if (!class_exists($fullController)) {
            http_response_code(500);
            echo "O controlador POST ($controller) não foi encontrado.";
            return;
        }

        $controllerInstance = new $fullController();
        call_user_func_array([$controllerInstance, $method], $params);
    });
}

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    echo "Página não encontrada (404)";
});


// Roda o roteador
$router->run();