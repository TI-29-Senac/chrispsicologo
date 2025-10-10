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
        $agendamentos = $this->agendamento->buscarAgendamentos();
        $totalAgendamentos = 0;
        $pendentes = 0;
        $confirmados = 0;
        $cancelados = 0;

        foreach ($agendamentos as $ag) {
            $totalAgendamentos++;
            if ($ag['status_consulta'] === 'pendente') {
                $pendentes++;
            } elseif ($ag['status_consulta'] === 'confirmada') {
                $confirmados++;
            } elseif ($ag['status_consulta'] === 'cancelada') {
                $cancelados++;
            }
        }

        // --- ARRAY DE STATS PADRONIZADO ---
        $stats = [
            [
                'label' => 'Total de Agendamentos',
                'value' => $totalAgendamentos,
                'icon' => 'fa-calendar'
            ],
            [
                'label' => 'Confirmados',
                'value' => $confirmados,
                'icon' => 'fa-calendar-check-o'
            ],
            [
                'label' => 'Pendentes',
                'value' => $pendentes,
                'icon' => 'fa-clock-o'
            ],
            [
                'label' => 'Cancelados',
                'value' => $cancelados,
                'icon' => 'fa-calendar-times-o'
            ]
        ];

        View::render("agendamento/index", [
            "agendamentos" => $agendamentos,
            "stats" => $stats 
        ]);
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
            $_POST["id_usuario"],
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