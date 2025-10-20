<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Models\Usuario;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AgendamentoValidador;
use DateTime;

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

    public function buscarDisponibilidade($id_profissional, $data) {
        header('Content-Type: application/json');

        // Validação básica da data (formato YYYY-MM-DD)
        $d = DateTime::createFromFormat('Y-m-d', $data);
        if (!$d || $d->format('Y-m-d') !== $data) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Formato de data inválido. Use YYYY-MM-DD.']);
            return;
        }

        // Verifica se o profissional existe e está ativo (opcional, mas bom)
        $profissional = $this->profissional->buscarProfissionalPublicoPorId((int)$id_profissional);
        if (!$profissional) {
            http_response_code(404);
             echo json_encode(['success' => false, 'message' => 'Profissional não encontrado ou indisponível.']);
             return;
        }

        try {
            $horariosDisponiveis = $this->agendamento->calcularHorariosDisponiveis((int)$id_profissional, $data);
            http_response_code(200);
            echo json_encode(['success' => true, 'horarios' => $horariosDisponiveis]);
        } catch (\Exception $e) {
            http_response_code(500);
            error_log("Erro ao buscar disponibilidade: " . $e->getMessage()); // Log do erro
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar horários disponíveis.']);
        }
    }

    // --- MÉTODO SALVAR AJUSTADO PARA AJAX E SESSÃO ---
    public function salvarAgendamentos() {
        header('Content-Type: application/json'); // Resposta será JSON

        // Verifica se o usuário está logado (CLIENTE)
         if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['usuario_tipo'] !== 'cliente') {
             http_response_code(401); // Unauthorized
             echo json_encode(['success' => false, 'message' => 'Acesso não autorizado. Faça login como cliente para agendar.']);
             return;
         }
         $id_usuario = $_SESSION['usuario_id']; // Pega ID do cliente da sessão

        // Recebe dados via JSON (ou formulário, ajuste o frontend)
        // Se o frontend enviar como form data:
        $id_profissional = $_POST['id_profissional'] ?? null;
        $data_selecionada = $_POST['data_selecionada'] ?? null;
        $horario_selecionado = $_POST['horario_selecionado'] ?? null;

        // Validações básicas
        if (empty($id_profissional) || empty($data_selecionada) || empty($horario_selecionado)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dados incompletos para agendamento.']);
            return;
        }

        // Combina data e hora no formato YYYY-MM-DD HH:MM:SS
        $data_agendamento_str = $data_selecionada . ' ' . $horario_selecionado . ':00';
        $data_agendamento = DateTime::createFromFormat('Y-m-d H:i:s', $data_agendamento_str);

        if (!$data_agendamento) {
            http_response_code(400);
             echo json_encode(['success' => false, 'message' => 'Data ou hora inválida.']);
             return;
        }
         // Verifica se o horário ainda está disponível (importante para evitar condição de corrida)
         $horariosDisponiveis = $this->agendamento->calcularHorariosDisponiveis((int)$id_profissional, $data_selecionada);
         if (!in_array($horario_selecionado, $horariosDisponiveis)) {
             http_response_code(409); // Conflict
             echo json_encode(['success' => false, 'message' => 'Desculpe, este horário acabou de ser reservado. Por favor, escolha outro.']);
             return;
         }


        // Tenta inserir no banco
        $resultado = $this->agendamento->inserirAgendamento(
            (int)$id_usuario,
            (int)$id_profissional,
            $data_agendamento->format('Y-m-d H:i:s'), // Formato correto para o BD
            'pendente' // Status inicial
        );

        if($resultado){
            http_response_code(201); // Created
            // Aqui seria o ponto para iniciar o pagamento do sinal
            echo json_encode([
                'success' => true,
                'message' => 'Agendamento solicitado com sucesso! Efetue o pagamento do sinal para confirmar.',
                'agendamentoId' => $resultado
                // 'redirectPagamento' => '/pagamento/iniciar/' . $resultado // Exemplo
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar o agendamento no banco de dados.']);
        }
    }

    // ... (outros métodos como viewListar, viewEditar, atualizar, viewExcluir, deletar permanecem) ...
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


    public function viewExcluirAgendamentos($id) {
        $agendamento = $this->agendamento->buscarAgendamentoPorId((int)$id);
        if (!$agendamento) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Agendamento não encontrado.");
            return;
        }
        View::render("agendamento/delete", ["agendamento" => $agendamento]);
    }

    public function atualizarAgendamentos($id) {
         // Validação pode ser adicionada aqui se necessário
        $data_agendamento_str = $_POST['data_agendamento']; // Vem do datetime-local
        $status_consulta = $_POST['status_consulta'] ?? 'pendente';

        // Converte o formato do datetime-local para o formato do banco
         try {
            $data_agendamento_dt = new DateTime($data_agendamento_str);
            $data_agendamento_bd = $data_agendamento_dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
             Redirect::redirecionarComMensagem("agendamentos/editar/{$id}", "error", "Formato de data inválido.");
             return;
        }


        $sucesso = $this->agendamento->atualizarAgendamento(
            (int)$id,
             $data_agendamento_bd, // Usa a data formatada
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