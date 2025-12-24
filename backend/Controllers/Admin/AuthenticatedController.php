<?php
namespace App\Psico\Controllers\Admin;

use App\Psico\Core\Session;
use App\Psico\Core\Redirect;
use App\Psico\Core\Flash;

abstract class AuthenticatedController {
    protected Session $session;
    protected string $usuarioTipo; // Para guardar o tipo do usuário logado

    public function __construct(){
        $this->session = new Session();

        if(!$this->session->has('usuario_id')){
            $this->session->destroy();
            Flash::set(
                'error',
                'Você precisa estar logado para acessar esta área.'
            );
            header('Location: /index.html'); // Redireciona para a raiz
            exit();
        }

        $this->usuarioTipo = $this->session->get('usuario_tipo') ?? '';
    }

    /**
     * Verifica se o tipo de usuário logado está na lista de tipos permitidos.
     * Redireciona para o dashboard com mensagem de erro se não tiver permissão.
     *
     * @param array $tiposPermitidos Array com os tipos de usuário permitidos (ex: ['admin', 'recepcionista'])
     */
    protected function verificarAcesso(array $tiposPermitidos): void {
        if (!in_array($this->usuarioTipo, $tiposPermitidos)) {
            Redirect::redirecionarComMensagem(
                'dashboard', // Redireciona para o dashboard geral
                'error',
                'Você não tem permissão para acessar esta funcionalidade.'
            );
            // exit(); // O redirecionamento já inclui exit()
        }
    }
}