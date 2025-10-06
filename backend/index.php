<?php
namespace App\Psico;
require __DIR__ . '/../vendor/autoload.php';
use App\Psico\Rotas\Rotas;
 
$rotas = Rotas::get();
 
$metodoHttp = $_SERVER["REQUEST_METHOD"];
$rota = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if(!array_key_exists($rota, $rotas[$metodoHttp])) {
    http_response_code(404);
    echo "Página não encontrada";
    exit;
}

$partes = explode("@", $rotas[$metodoHttp][$rota]);
$nomeController = $partes[0];
$metodoController = $partes[1];
$nomeCompletoController = "App\\Psico\\Controllers\\" . $nomeController;

if(!class_exists($nomeCompletoController)) {
    http_response_code(500);
    echo "O controlador não foi encontrado";
    exit;
}

$controller = new $nomeCompletoController();
$controller->$metodoController();



