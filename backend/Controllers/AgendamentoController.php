<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;
use App\Psico\Core\View;

class AgendamentoController {
    public $agendamento;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->agendamento = new Agendamento($this->db);
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

        $stats = [
            ['label' => 'Total de Agendamentos', 'value' => $totalAgendamentos, 'icon' => 'fa-calendar'],
            ['label' => 'Confirmados', 'value' => $confirmados, 'icon' => 'fa-calendar-check-o'],
            ['label' => 'Pendentes', 'value' => $pendentes, 'icon' => 'fa-clock-o'],
            ['label' => 'Cancelados', 'value' => $cancelados, 'icon' => 'fa-calendar-times-o']
        ];

        View::render("agendamento/index", ["agendamentos" => $agendamentos, "stats" => $stats]);
    }
}