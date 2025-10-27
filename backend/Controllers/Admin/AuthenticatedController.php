<?php
namespace App\Psico\Controllers\Admin;

use App\Psico\Core\Session;
use App\Psico\Core\Redirect;

abstract class AuthenticatedController {
    protected Session $session;

    public function __construct(){
        $this->session = new Session();
        if(!$this->session->has('usuario_id')){
            Redirect::redirecionarComMensagem(
                'login',
                'error',
                'Você precisa estar logado para acessar essa área.'
            );
        }
    }
}