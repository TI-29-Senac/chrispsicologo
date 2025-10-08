<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Usuario;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Core\FileManager;
use App\Psico\Validadores\UsuarioValidador;

class UsuarioController {
    public $usuario;   
    public $db;
    public $gerenciarImagem;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->usuario = new Usuario($this->db);
        $this->gerenciarImagem = new FileManager('upload');
    }

    // Listar usuários
    public function index(){
        $resultado = $this->usuario->buscarUsuarios();
        var_dump($resultado);
    }

    public function viewListarUsuarios() {
        $dados = $this->usuario->buscarUsuarios();
        View::render("usuario/index", ["usuarios" => $dados]);
    }

    // Criar usuário
    public function viewCriarUsuarios() {

        View::render("usuario/create");
    }

    // Editar usuário
    public function viewEditarUsuarios() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "ID do usuário não informado.";
            return;
        }

        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);

        if (!$usuario) {
            echo "Usuário não encontrado.";
            return;
        }

        View::render("usuario/edit", ["usuario" => $usuario]);
    }

    // Excluir usuário
    public function viewExcluirUsuarios() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "ID do usuário não informado.";
            return;
        }

        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);

        if (!$usuario) {
            echo "Usuário não encontrado.";
            return;
        }

        View::render("usuario/delete", ["usuario" => $usuario]);
    }

    // Salvar usuário (POST)
public function salvarUsuarios() {
    $erros = UsuarioValidador::ValidarEntradas($_POST);
    if(!empty($erros)){
        Redirect::redirecionarComMensagem('/backend/usuario/criar', 'error', implode('<br>', $erros));
        return;
    }
 
    $imagem = $this->gerenciarImagem->salvarArquivo($_FILES['imagem'], 'usuario');
    if($this->usuario->inserirUsuario(
        $_POST['nome_usuario'],
        $_POST['email_usuario'],
        $_POST['senha_usuario'],
        $_POST['tipo_usuario'],
        'Ativo',
        $imagem
    )){
        Redirect::redirecionarComMensagem('/backend/usuario/listar', 'success', 'Usuário criado com sucesso!');
    }else{
        Redirect::redirecionarComMensagem('/backend/usuario/criar', 'error', 'Erro ao criar usuário. Tente novamente.');
    }
}

    public function relatorioUsuarios($id, $data1, $data2) {
        View::render("usuario/relatorio", ["id" => $id, "data1" => $data1, "data2" => $data2]);
        }
 

    // Atualizar usuário (POST)
public function atualizarUsuarios() {
    $id = $_POST['id_usuario'] ?? null;
    $nome = $_POST['nome_usuario'] ?? '';
    $email = $_POST['email_usuario'] ?? '';
    $senha = $_POST['senha_usuario'] ?? null; // senha opcional
    $tipo = $_POST['tipo_usuario'] ?? 'user';

    if (!$id) {
        echo "ID do usuário não informado.";
        return;
    }

    // Atualiza todos os campos do usuário
    $resultado = $this->usuario->atualizarUsuario((int)$id, $nome, $email, $senha, $tipo);

    if ($resultado) {
        echo "Usuário atualizado com sucesso.";
    } else {
        echo "Erro ao atualizar usuário ou nenhum campo alterado.";
    }
}


    // Deletar usuário (POST)
    public function deletarUsuarios() {
        $id = $_POST['id_usuario'] ?? null;

        if (!$id) {
            echo "ID do usuário não informado.";
            return;
        }

        $resultado = $this->usuario->deletarUsuario((int)$id);

        if ($resultado) {
            echo "Usuário deletado com sucesso.";
        } else {
            echo "Erro ao deletar usuário.";
        }
    }

        public function login() {
        // Assume que o frontend enviará 'email' e 'senha'
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        header('Content-Type: application/json'); // Garante que a resposta é JSON

        $usuarioAutenticado = $this->usuario->autenticarUsuario($email, $senha);

        if ($usuarioAutenticado) {
            // Se o login for bem-sucedido
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Login realizado com sucesso!", "user" => $usuarioAutenticado]);
        } else {
            // Se o login falhar
            http_response_code(401); // Unauthorized
            echo json_encode(["success" => false, "message" => "Email ou senha inválidos."]);
        }
    }


}
