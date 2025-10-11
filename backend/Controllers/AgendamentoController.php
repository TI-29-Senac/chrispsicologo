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
        // --- LÓGICA DE PAGINAÇÃO ---
        $pagina = $_GET['pagina'] ?? 1;
        $dadosPaginados = $this->agendamento->paginacao((int)$pagina, 10); // Limite de 5 por página

        // --- LÓGICA PARA OS CARDS DE STATS ---
        // Para os cards, precisamos contar todos os registros. 
        // A informação do total já vem da paginação, então vamos reutilizá-la.
        $totalAgendamentos = $dadosPaginados['total'];
        
        // Para os status (pendente, confirmado, etc.), precisamos de uma busca que retorne todos os agendamentos.
        // O ideal é ter métodos específicos no Model para isso, mas por enquanto faremos a contagem aqui.
        $todosAgendamentos = $this->agendamento->buscarAgendamentos(); // Este método já existe no seu Model
        
        $pendentes = 0;
        $confirmados = 0;
        $cancelados = 0;

        foreach ($todosAgendamentos as $ag) {
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
            "agendamentos" => $dadosPaginados['data'], 
            "paginacao" => $dadosPaginados,        
            "stats" => $stats                 
        ]);
    }
    
    public function viewEditarAgendamentos($id) {
        $agendamento = $this->agendamento->buscarAgendamentoPorId((int)$id);
        if (!$agendamento) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Agendamento não encontrado.");
            return;
        }
        View::render("agendamento/edit", ["agendamento" => $agendamento]);
    } 

        public function viewCriarAgendamentos() {
        View::render("agendamento/create");
    }
    
// Implemente viewExcluirAgendamentos (substitua o placeholder da linha 99)
    public function viewExcluirAgendamentos($id) {
        $agendamento = $this->agendamento->buscarAgendamentoPorId((int)$id);
        if (!$agendamento) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Agendamento não encontrado.");
            return;
        }
        View::render("agendamento/delete", ["agendamento" => $agendamento]);
    }

// Implemente atualizarAgendamentos (substitua o placeholder da linha 113)
    public function atualizarAgendamentos($id) {
        // O validador existente verifica a data de agendamento.
        $erros = AgendamentoValidador::ValidarEntradas($_POST);
        if (!empty($erros)) {
            Redirect::redirecionarComMensagem("agendamentos/editar/{$id}", "error", implode("<br>", $erros));
            return;
        }
        
        $data_agendamento = $_POST['data_agendamento'];
        $status_consulta = $_POST['status_consulta'] ?? 'pendente';
        
        $sucesso = $this->agendamento->atualizarAgendamento(
            (int)$id,
            $data_agendamento,
            $status_consulta
        );

        if ($sucesso) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "success", "Agendamento atualizado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("agendamentos/editar/{$id}", "error", "Erro ao atualizar agendamento.");
        }
    }

// Implemente deletarAgendamentos (substitua o placeholder da linha 116)
    public function deletarAgendamentos($id) {
        $sucesso = $this->agendamento->deletarAgendamento((int)$id);
        
        if ($sucesso) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "success", "Agendamento excluído/cancelado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Erro ao excluir agendamento. Ele pode já ter sido excluído.");
        }
    }
}