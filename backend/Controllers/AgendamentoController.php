<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Models\Usuario;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AgendamentoValidador;

class AgendamentoController {
    public $agendamento;   
    public $db;
    public $usuario;
    public $profissional;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->agendamento = new Agendamento($this->db);
        $this->usuario = new Usuario($this->db);
        $this->profissional = new Profissional($this->db); 
    }
    // Index
    public function index() {
        $agendamentos = $this->agendamento->buscarAgendamentos();
        var_dump($agendamentos);
    }

        public function viewListarAgendamentos() {
        $pagina = $_GET['pagina'] ?? 1;
        $dadosPaginados = $this->agendamento->paginacao((int)$pagina, 10);

        $totalAgendamentos = $dadosPaginados['total'];

        $todosAgendamentos = $this->agendamento->buscarAgendamentos(); 
        
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
        $pacientes = $this->usuario->buscarTodosUsuarios();
        $profissionais = $this->profissional->listarProfissionais();

        View::render("agendamento/create", [
            "pacientes" => $pacientes,
            "profissionais" => $profissionais
        ]);
    }
    
    public function salvarAgendamentos()
    {
        $erros = AgendamentoValidador::ValidarEntradas($_POST);
        if (!empty($erros)) {
            Redirect::redirecionarComMensagem("agendamentos/criar", "error", implode("<br>", $erros));
            return;
        }

        $id_usuario = $_POST['id_usuario'];
        $id_profissional = $_POST['id_profissional'];
        $data_agendamento = $_POST['data_agendamento'];

        if ($this->agendamento->inserirAgendamento((int)$id_usuario, (int)$id_profissional, $data_agendamento)) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "success", "Agendamento criado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("agendamentos/criar", "error", "Erro ao criar agendamento.");
        }
    }

    public function viewExcluirAgendamentos($id) {
        $agendamento = $this->agendamento->buscarAgendamentoPorId((int)$id);
        if (!$agendamento) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Agendamento não encontrado.");
            return;
        }
        View::render("agendamento/delete", ["agendamento" => $agendamento]);
    }

    public function atualizarAgendamentos($id) {
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

    public function deletarAgendamentos($id) {
        $sucesso = $this->agendamento->deletarAgendamento((int)$id);
        
        if ($sucesso) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "success", "Agendamento excluído/cancelado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Erro ao excluir agendamento. Ele pode já ter sido excluído.");
        }
    }
}