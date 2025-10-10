<?php
namespace App\Psico\Controllers;

use App\Psico\Core\View;
use App\Psico\Models\Usuario;
use App\Psico\Models\Avaliacao;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\Redirect;
use App\Psico\Core\Flash;
use App\Psico\Validadores\UsuarioValidador;
use App\Psico\Core\FileManager;

class UsuarioController {
    public $usuario;
    public $db;
    public $avaliacao;
    public $gerenciarImagem;
    public $profissional;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->usuario = new Usuario($this->db);
        $this->avaliacao = new Avaliacao($this->db);
        $this->gerenciarImagem = new FileManager('upload');
        $this->profissional = new Profissional($this->db);
    }

    public function viewListarUsuarios() {
        $usuarios = $this->usuario->buscarUsuarios();
        $totalUsuarios = 0;
        $usuariosAtivos = 0;
        $totalProfissionais = 0;

        foreach ($usuarios as $usuario) {
            $totalUsuarios++;
            if ($usuario->status_usuario === 'ativo') {
                $usuariosAtivos++;
            }
            if ($usuario->tipo_usuario === 'profissional') {
                $totalProfissionais++;
            }
        }
        $usuariosInativos = $totalUsuarios - $usuariosAtivos;

        $stats = [
            ['label' => 'Total de Usuários', 'value' => $totalUsuarios, 'icon' => 'fa-users'],
            ['label' => 'Usuários Ativos', 'value' => $usuariosAtivos, 'icon' => 'fa-check-circle'],
            ['label' => 'Usuários Inativos', 'value' => $usuariosInativos, 'icon' => 'fa-times-circle'],
            ['label' => 'Profissionais', 'value' => $totalProfissionais, 'icon' => 'fa-user-md']
        ];

        View::render("usuario/index", ["usuarios" => $usuarios, "stats" => $stats]);
    }

    public function salvarUsuarios() {
        $erros = UsuarioValidador::ValidarEntradas($_POST);
        if(!empty($erros)){
            Redirect::redirecionarComMensagem("usuario/criar", "error", implode("<br>", $erros));
            return;
        }
        
        // Adicionada a captura do CPF
        $id = $this->usuario->inserirUsuario(
            $_POST["nome_usuario"],
            $_POST["email_usuario"],
            $_POST["senha_usuario"],
            $_POST["tipo_usuario"],
            $_POST["cpf"] 
        );

        if($id){
            Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário criado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("usuario/criar", "error", "Erro ao criar usuário!");
        }
    }

    public function atualizarUsuarios($id) {
        $erros = UsuarioValidador::ValidarEntradas($_POST, true);
        if(!empty($erros)){
            Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", implode("<br>", $erros));
            return;
        }

        // Adicionada a passagem do CPF
        $sucesso = $this->usuario->atualizarUsuario(
            (int)$id,
            $_POST['nome_usuario'],
            $_POST['email_usuario'],
            $_POST['senha_usuario'] ?? null,
            $_POST['tipo_usuario'],
            $_POST['cpf']
        );

        if ($sucesso) {
            Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário atualizado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", "Erro ao atualizar usuário.");
        }
    }
    
    // Demais métodos (create, edit, delete, etc.)
    public function viewCriarUsuarios(){
        View::render("usuario/create");
    }

    public function viewEditarUsuarios($id){
        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
        View::render("usuario/edit", ["usuario" => $usuario]);
    }

    public function viewExcluirUsuarios($id){
        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
        View::render("usuario/delete", ["usuario" => $usuario]);
    }

    public function deletarUsuarios($id){
        $sucesso = $this->usuario->excluirUsuario((int)$id);
        if ($sucesso) {
            Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário excluído com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Erro ao excluir usuário.");
        }
    }
}