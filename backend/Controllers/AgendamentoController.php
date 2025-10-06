<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AgendamentoValidador;

class AgendamentoController {
    public $agendamento;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->agendamento = new Agendamento($this->db);

    }
    // Index
    public function index() {
        $agendamentos = $this->agendamento->buscarAgendamentos();
        var_dump($agendamentos);
    }
    public function viewListarAgendamentos() {
        $dados = $this->agendamento->buscarAgendamentos();
        View::render("agendamento/index",["agendamentos"=>$dados]);
    }
    public function viewCriarAgendamentos() {
        View::render("agendamento/create");
    }
    public function viewEditarAgendamentos() {
        View::render("agendamento/edit");
    } 
    public function viewExcluirAgendamentos() {
        View::render("agendamento/delete");
    }
    
    public function salvarAgendamentos() {
        $erros = AgendamentoValidador::ValidarEntradas($_POST);
        if(!empty($erros)){ 
            Redirect::redirecionarComMensagem("agendamento/criar","error",implode("<br>",$erros));
        }
        $id = $this->agendamento->inserirAgendamento(
            $_POST["id_paciente"],
            $_POST["id_profissional"],
            $_POST["data_agendamento"],
            "Pendente"
        );

        if ($id) {
            echo "Agendamento feito com sucesso. ID: $id";
        } else {
            echo "Erro ao criar agendamento.";
        }
    }
    public function atualizarAgendamentos() {
        echo "Atualizar Agendamentos";
    }
    public function deletarAgendamentos() {
        echo "Deletar Agendamentos";
    }
}