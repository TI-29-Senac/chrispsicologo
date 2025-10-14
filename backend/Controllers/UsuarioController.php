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
 
    // Listar usuários
    public function index(){
        $resultado = $this->usuario->buscarUsuarios();
        var_dump($resultado);
    }
    public function viewListarUsuarios() {
        $pagina = $_GET['pagina'] ?? 1;
        $dadosPaginados = $this->usuario->paginacao((int)$pagina, 10);
       
        // --- LÓGICA PARA OS CARDS ---
        $todosUsuarios = $this->usuario->buscarTodosUsuarios();
       
        $totalUsuarios = count($todosUsuarios);
        $usuariosAtivos = 0;
        $totalProfissionais = 0;
 
        foreach ($todosUsuarios as $usuario) {
            if ($usuario->status_usuario === 'ativo') {
                $usuariosAtivos++;
            }
            if ($usuario->tipo_usuario === 'profissional') {
                $totalProfissionais++;
            }
        }
        $usuariosInativos = $totalUsuarios - $usuariosAtivos;
 
        // --- ARRAY DE STATS ATUALIZADO COM 4 ITENS ---
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
                'icon' => 'fa-user-md' // Ícone para profissionais
            ]
        ];
 
        View::render("usuario/index", [
            "usuarios" => $dadosPaginados['data'],
            "paginacao" => $dadosPaginados,
            "stats" => $stats
        ]);
    }
 
    // Salvar usuário (POST)
public function salvarUsuarios() {
    $erros = UsuarioValidador::ValidarEntradas($_POST);
    if(!empty($erros)){
        Redirect::redirecionarComMensagem("usuario/criar", "error", implode("<br>", $erros));
        return;
    }
 
    if($this->usuario->inserirUsuario(
        $_POST['nome_usuario'],
        $_POST['email_usuario'],
        $_POST['senha_usuario'],
        $_POST['tipo_usuario'],
        $_POST['cpf'] ?? ''
    )){
        Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário criado com sucesso!");
    }else{
        Redirect::redirecionarComMensagem("usuario/criar", "error", "Erro ao criar usuário. Tente novamente.");
    }
}
 
 
    public function relatorioUsuarios($id, $data1, $data2) {
        View::render("usuario/relatorio", ["id" => $id, "data1" => $data1, "data2" => $data2]);
        }
 
 
    public function viewEditarUsuarios($id) {
        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
        if ($usuario) {
            View::render("usuario/edit", ["usuario" => $usuario]);
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Usuário não encontrado.");
        }
    }
 
    public function atualizarUsuarios($id) {
        $status = $_POST['status_usuario'] ?? 'ativo';
        $sucesso = $this->usuario->atualizarUsuario(
            (int)$id,
            $_POST['nome_usuario'],
            $_POST['email_usuario'],
            $_POST['senha_usuario'] ?? null,
            $_POST['tipo_usuario'],
            $_POST['cpf'],
            $status // Passa a variável corrigida
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
 
    public function viewExcluirUsuarios($id) {
        $usuario = $this->usuario->buscarUsuarioPorId((int)$id);
        if ($usuario) {
            View::render("usuario/delete", ["usuario" => $usuario]);
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Usuário não encontrado.");
        }
    }
        // --- NOVO MÉTODO PARA DELETAR ---
    public function deletarUsuarios($id) {
        $sucesso = $this->usuario->excluirUsuario((int)$id);
        if ($sucesso) {
            Redirect::redirecionarComMensagem("usuario/listar", "success", "Usuário excluído com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("usuario/listar", "error", "Erro ao excluir usuário.");
        }
    }
}