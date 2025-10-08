<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;

use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;

class ProfissionalController {
    public $profissional;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->profissional = new Profissional($this->db);

    }
    // Index
    public function index(){
        $profissionais = $this->profissional->buscarprofissionais();
        var_dump($profissionais);
    }

    public function viewListarProfissionais(){
        $dados = $this->profissional->buscarprofissionais();
        View::render("profissional/index",["profissionais"=>$dados]);
    }
    public function viewCriarProfissionais(){
        View::render("profissional/create");
    }
    public function viewEditarProfissionais(){
        View::render("profissional/edit");
    }
    public function viewExcluirProfissionais(){
        View::render("profissional/delete");
    }
    public function salvarProfissionais(){
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

    public function atualizarProfissionais(){
        echo "Atualizar Profissionais";
    }
    public function deletarProfissionais(){
        echo "Deletar Profissionais";
    }

}