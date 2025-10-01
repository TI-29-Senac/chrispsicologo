<?php

namespace App\Psico\Core;

class View{
    public static function render($nomeView, $dados = []){
        extract($dados);
        require_once __DIR__."/../Views/templates/partials/header.php";
        require_once __DIR__."/../Views/templates/{$nomeView}.php";
        require_once __DIR__."/../Views/templates/partials/footer.php";
    }
}