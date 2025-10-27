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
    
    public function index() {
        $agendamentos = $this->agendamento->buscarAgendamentos();
    }

    public function buscarDisponibilidade($id_profissional, $data) {
        header('Content-Type: application/json');

        
        $d = DateTime::createFromFormat('Y-m-d', $data);
        if (!$d || $d->format('Y-m-d') !== $data) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Formato de data inválido. Use YYYY-MM-DD.']);
            return;
        }

        
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
            error_log("Erro ao buscar disponibilidade: " . $e->getMessage()); 
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar horários disponíveis.']);
        }
    }

    
    public function salvarAgendamentos() {
        header('Content-Type: application/json'); 

        
         if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['usuario_tipo'] !== 'cliente') {
             http_response_code(401); 
             echo json_encode(['success' => false, 'message' => 'Acesso não autorizado. Faça login como cliente para agendar.']);
             return;
         }
         $id_usuario = $_SESSION['usuario_id']; 

        
        
        $id_profissional = $_POST['id_profissional'] ?? null;
        $data_selecionada = $_POST['data_selecionada'] ?? null;
        $horario_selecionado = $_POST['horario_selecionado'] ?? null;

        
        if (empty($id_profissional) || empty($data_selecionada) || empty($horario_selecionado)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dados incompletos para agendamento.']);
            return;
        }

        
        $data_agendamento_str = $data_selecionada . ' ' . $horario_selecionado . ':00';
        $data_agendamento = DateTime::createFromFormat('Y-m-d H:i:s', $data_agendamento_str);

        if (!$data_agendamento) {
            http_response_code(400);
             echo json_encode(['success' => false, 'message' => 'Data ou hora inválida.']);
             return;
        }
         
         $horariosDisponiveis = $this->agendamento->calcularHorariosDisponiveis((int)$id_profissional, $data_selecionada);
         if (!in_array($horario_selecionado, $horariosDisponiveis)) {
             http_response_code(409); 
             echo json_encode(['success' => false, 'message' => 'Desculpe, este horário acabou de ser reservado. Por favor, escolha outro.']);
             return;
         }


        
        $resultado = $this->agendamento->inserirAgendamento(
            (int)$id_usuario,
            (int)$id_profissional,
            $data_agendamento->format('Y-m-d H:i:s'), 
            'pendente' 
        );

        if($resultado){
            http_response_code(201); 
            
            echo json_encode([
                'success' => true,
                'message' => 'Agendamento solicitado com sucesso! Efetue o pagamento do sinal para confirmar.',
                'agendamentoId' => $resultado
                
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar o agendamento no banco de dados.']);
        }
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


    public function viewExcluirAgendamentos($id) {
        $agendamento = $this->agendamento->buscarAgendamentoPorId((int)$id);
        if (!$agendamento) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Agendamento não encontrado.");
            return;
        }
        View::render("agendamento/delete", ["agendamento" => $agendamento]);
    }

    public function atualizarAgendamentos($id) {
         
        $data_agendamento_str = $_POST['data_agendamento']; 
        $status_consulta = $_POST['status_consulta'] ?? 'pendente';

        
         try {
            $data_agendamento_dt = new DateTime($data_agendamento_str);
            $data_agendamento_bd = $data_agendamento_dt->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
             Redirect::redirecionarComMensagem("agendamentos/editar/{$id}", "error", "Formato de data inválido.");
             return;
        }


        $sucesso = $this->agendamento->atualizarAgendamento(
            (int)$id,
             $data_agendamento_bd, 
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

    // ... (Dentro da classe AgendamentoController) ...

    public function buscarMeusAgendamentosApi() {
        header('Content-Type: application/json');
        
        // 1. Verificar se há cliente logado na sessão
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['usuario_tipo'] !== 'cliente') {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Acesso não autorizado. Faça login como cliente.']);
            return;
        }

        $id_usuario = $_SESSION['usuario_id']; 

        try {
            // 2. Chamar um novo método no Model Agendamento (ver próxima seção)
            $agendamentos = $this->agendamento->buscarAgendamentosPorUsuario((int)$id_usuario);
            
            http_response_code(200);
            echo json_encode(['success' => true, 'agendamentos' => $agendamentos]);

        } catch (\Exception $e) {
            error_log("Erro API buscarMeusAgendamentosApi: " . $e->getMessage()); 
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro interno ao buscar agendamentos.']);
        }
    }

    public function buscarAgendamentosPorUsuarioApi() {
        // --- INÍCIO DA CORREÇÃO ---
        header('Content-Type: application/json'); // Garante o cabeçalho JSON

        // Limpa qualquer buffer de saída anterior para evitar output indesejado
        while (ob_get_level() > 0) { ob_end_clean(); }

        // Verifica a sessão
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['usuario_tipo'] ?? '') !== 'cliente') {
            http_response_code(401); // Não autorizado
            echo json_encode(['success' => false, 'message' => 'Acesso não autorizado. Faça login como cliente.']);
            exit; // Termina a execução
        }
        $id_cliente = $_SESSION['usuario_id'];

        try {
            // Chama o método do Model para buscar os agendamentos
            $agendamentos = $this->agendamento->buscarAgendamentosPorUsuario((int)$id_cliente);

            // Verifica se a busca foi bem-sucedida (retorna array ou false)
            if ($agendamentos === false) {
                 // Considera como um erro interno se a busca falhar, mas não for exceção
                 throw new \RuntimeException("Erro ao buscar agendamentos no Model.");
            }

            // Envia a resposta JSON de sucesso
            http_response_code(200);
            echo json_encode(['success' => true, 'agendamentos' => $agendamentos]);

        } catch (\PDOException $e) {
            // Captura erros específicos do banco de dados
            error_log("Erro PDO ao buscar agendamentos do cliente: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Erro interno [DB] ao buscar seus agendamentos.']);
        } catch (\Exception $e) {
            // Captura outros erros gerais
            error_log("Erro geral ao buscar agendamentos do cliente: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro interno ao processar sua solicitação: ' . $e->getMessage()]);
        } finally {
            // Garante que a execução termina após enviar a resposta JSON
            exit;
        }
        // --- FIM DA CORREÇÃO ---
    }
}