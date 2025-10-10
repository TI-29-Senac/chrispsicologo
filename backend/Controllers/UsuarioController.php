<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Usuario;
use App\Psico\Models\Avaliacao;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Core\FileManager;
use App\Psico\Validadores\UsuarioValidador;
use App\Psico\Core\Flash;

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

    // Listar usuários
    public function index(){
        $resultado = $this->usuario->buscarUsuarios();
        var_dump($resultado);
    }

 public function viewListarUsuarios() {
        $usuarios = $this->usuario->buscarUsuarios();

        // --- LÓGICA DE STATS ESPECÍFICA PARA USUÁRIOS ---
        $totalUsuarios = 0;
        $usuariosAtivos = 0;
        $usuariosInativos = 0;
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

        // --- NOVO ARRAY DE STATS PADRONIZADO ---
        $stats = [
            [
                'label' => 'Total de Usuários',
                'value' => $totalUsuarios,
                'icon' => 'fa-users'
            ],
            [
                'label' => 'Usuários Ativos',
                'value' => $usuariosAtivos,
                'icon' => 'fa-check-circle'
            ],
            [
                'label' => 'Usuários Inativos',
                'value' => $usuariosInativos,
                'icon' => 'fa-times-circle'
            ],
            [
                'label' => 'Profissionais',
                'value' => $totalProfissionais,
                'icon' => 'fa-user-md'
            ]
        ];

        View::render("usuario/index", [
            "usuarios" => $usuarios,
            "stats" => $stats 
        ]);
    }

    // Criar usuário
    public function viewCriarUsuarios() {

        View::render("usuario/create");
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
 

public function viewEditarUsuarios($id) {
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
    
    // ... (outros métodos)

    // CORREÇÃO: Receber $id como argumento e remover a busca pelo ID no $_POST
    public function atualizarUsuarios($id) {
        $dados = $_POST;

        if (!$id) {
            die('ID do usuário não informado.');
        }

        $nome = $dados['nome_usuario'] ?? '';
        $email = $dados['email_usuario'] ?? '';
        $senha = $dados['senha_usuario'] ?? null;
        $tipo = $dados['tipo_usuario'] ?? 'cliente';

        $erros = UsuarioValidador::ValidarEntradas($dados, true);

        if (!empty($erros)) {
            Flash::set('validation_errors', $erros);
            Flash::set('old_input', $dados);
            Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", "Erro de validação. Verifique os campos.");
            return;
        }

        // O hash da senha já estava correto
        $senha_hash = empty($senha) ? null : password_hash($senha, PASSWORD_DEFAULT);

        $resultado = $this->usuario->atualizarUsuario(
            (int)$id, // Usar o $id vindo da URL
            $nome,
            $email,
            $senha_hash,
            $tipo
        );

        if ($resultado) {
            Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário ID: $id atualizado com sucesso.");
        } else {
            Redirect::redirecionarComMensagem("usuario/editar/{$id}", "error", "Erro ao atualizar usuário ou nenhum campo alterado.");
        }
    }

    public function deletarUsuario($id_usuario)
    {
        if (!$id_usuario) {
            Redirect::redirecionarComMensagem('usuarios/listar', 'error', 'ID do usuário não fornecido.');
            return;
        }

        $sucesso = $this->usuario->excluirUsuario((int)$id_usuario);

        if ($sucesso) {
            Redirect::redirecionarComMensagem('usuarios/listar', 'success', 'Usuário excluído com sucesso!');
        } else {
            Redirect::redirecionarComMensagem('usuarios/listar', 'error', 'Ocorreu um erro ao excluir o usuário.');
        }
    }

        public function login() {   
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        header('Content-Type: application/json'); 

        $usuarioAutenticado = $this->usuario->autenticarUsuario($email, $senha);

        if ($usuarioAutenticado) {
 
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Login realizado com sucesso!", "user" => $usuarioAutenticado]);
        } else {

            http_response_code(401); 
            echo json_encode(["success" => false, "message" => "Email ou senha inválidos."]);
        }
    }


}
