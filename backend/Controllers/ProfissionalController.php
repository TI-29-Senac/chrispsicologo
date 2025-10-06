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
        // Validação dos dados pode ser adicionada aqui
        $id = $this->profissional->inserirProfissional(
            $_POST["nome"],
            $_POST["email"],
            $_POST["telefone"],
            $_POST["especialidade"]
        );

        if($id){
            Redirect::redirecionarComMensagem("profissionais/salvar","sucess","Profissional criado com sucesso!");
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