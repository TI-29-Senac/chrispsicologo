<?php
namespace App\Psico\Controllers\Admin;
use App\Psico\Core\Redirect;

abstract class AdminController extends AuthenticatedController {
    public function __construct(){
        parent::__construct();
        if($this->session->get('usuario_tipo') !== 'admin' ){
            Redirect::redirecionarComMensagem(
            'admin/dashboard',
            'error',
            'Você não tem permissão para acessar essa área.'
        );
        }
    } 
}