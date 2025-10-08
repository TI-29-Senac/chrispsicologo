<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Models\Usuario;

use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Core\Flash;
use App\Psico\Validadores\ProfissionalValidador;
use App\Psico\Validadores\UsuarioValidador;


class ProfissionalController {
    public $profissional;   
    public $db;
    public $usuario;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->profissional = new Profissional($this->db);
        $this->usuario = new Usuario($this->db);

    }
    // Index
    public function index(){
        $profissionais = $this->profissional->buscarprofissionais();
        var_dump($profissionais);
    }

    public function viewCriarProfissionais(){
        View::render("profissional/create");
    }
    public function viewExcluirProfissionais(){
        View::render("profissional/delete");
    }
    public function salvarProfissionais(){
        $erros = ProfissionalValidador::ValidarEntradas($_POST);
        if(!empty($erros)){ 
            Redirect::redirecionarComMensagem("profissionais/criar","error",implode("<br>",$erros));
        // Captura as variáveis do POST
        $id_usuario = $_POST["id_usuario"] ?? null;
        $especialidade = $_POST["especialidade"] ?? '';
           if (empty($id_usuario) || empty($especialidade)) {
            Redirect::redirecionarComMensagem("profissionais/criar", "error", "O ID do Usuário e a Especialidade são obrigatórios.");
            return;
            }
            $id = $this->profissional->inserirProfissional(
            (int)$id_usuario,
            $especialidade
            );
            if($id){
            Redirect::redirecionarComMensagem("profissionais/listar","success","Profissional criado com sucesso! ID: $id");
            }else{
            Redirect::redirecionarComMensagem("profissionais/criar","error","Erro ao criar profissional!");
            }
        }
    }
public function atualizarProfissionais($id_profissional) {
        $dados = $_POST;
        $id_usuario = $dados['id_usuario'] ?? null;

        // 1. Validar os dados do formulário
        $errosUsuario = UsuarioValidador::ValidarEntradas($dados, true); // Valida nome, email, etc.
        $errosProfissional = ProfissionalValidador::ValidarEntradas($dados); // Valida especialidade

        $erros = array_merge($errosUsuario, $errosProfissional);

        if (!empty($erros)) {
            Flash::set('validation_errors', $erros);
            Flash::set('old_input', $dados);
            Redirect::redirecionarComMensagem("profissionais/editar/{$id_profissional}", "error", "Erro de validação. Verifique os campos.");
            return;
        }

        // 2. Atualizar dados na tabela de USUÁRIO
        $this->usuario->atualizarUsuario(
            (int)$id_usuario,
            $dados['nome_usuario'],
            $dados['email_usuario'],
            $dados['senha_usuario'] ?? null, // Senha é opcional
            $dados['tipo_usuario']
        );

        // 3. Atualizar dados na tabela de PROFISSIONAL
        $this->profissional->atualizarProfissional(
            (int)$id_usuario,
            $dados['especialidade']
        );

        Redirect::redirecionarComMensagem("profissionais/listar", "success", "Profissional atualizado com sucesso!");
    }

    public function deletarProfissionais(){
        echo "Deletar Profissionais";
    }


      public function viewListarProfissionais()
    {
        // O Controller pede ao Model para buscar os profissionais
        $profissionais = $this->profissional->listarProfissionais();
        View::render('profissional/index', ['profissionais' => $profissionais]);
    }

    public function viewEditarProfissionais($id_profissional)
    {
        if (!$id_profissional) {
            echo "ID do profissional não informado.";
            return;
        }

        // O Controller pede ao Model para buscar um profissional específico
        $profissional_data = $this->profissional->buscarProfissionalPorId((int)$id_profissional);

        if (!$profissional_data) {
            echo "Profissional não encontrado.";
            return;
        }

        View::render('profissional/edit', ["usuario" => $profissional_data]);
    }
}

