<?php
namespace App\Psico;
require __DIR__ . '/../vendor/autoload.php';
use App\Psico\Rotas\Rotas;
 
use Bramus\Router\Router;
$router = new Router();
 
$rotas = Rotas::get();
$router->setNamespace('\App\Psico\Controllers');

foreach ($rotas as $metodoHttp => $rota){
    foreach ($rota as $uri => $acao){
        $metodoBramus = strtolower($metodoHttp);
        $router->{$metodoBramus}($uri, $acao); 
    }
}
$router->set404(function() {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo '404, rota não encontrada!';
});
 
$router->run();