<?php
require __DIR__ . '/../../vendor/autoload.php';
use App\Chrispsicologo\Admin\Controllers\UsuarioController;
use App\Chrispsicologo\Admin\Models\Usuario;


// var_dump($_SERVER["REQUEST_URI"]);
// echo "\n\n\n\n";
// var_dump($_SERVER["REQUEST_METHOD"]);
// exit;
if($_SERVER["REQUEST_URI"] == "/backend/buscarusuarios"&& $_SERVER["REQUEST_METHOD"] == "GET")
{
   $controller = new UsuarioController();
   $resultado = $controller->index();
    var_dump($resultado);
}else {
    echo "Rota não encontrada.";
}


