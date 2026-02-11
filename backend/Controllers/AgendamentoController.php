<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Models\Usuario;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Controllers\Admin\AuthenticatedController;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AgendamentoValidador;
use DateTime;

class AgendamentoController extends AuthenticatedController {
    public $agendamento;   
    public $db;
    public $usuario;
    public $profissional;
    public function __construct(){
        parent::__construct();
        $this->db = Database::getInstance();
        $this->agendamento = new Agendamento($this->db);
        $this->usuario = new Usuario($this->db);
        $this->profissional = new Profissional($this->db); 
    }
    
    public function index() {
        $agendamentos = $this->agendamento->buscarAgendamentos();
    }

    

    public function viewListarAgendamentos() {
        $this->verificarAcesso(['admin', 'profissional', 'recepcionista']);
        
        $tipoUsuario = $_SESSION['usuario_tipo'] ?? '';
        $idUsuario = $_SESSION['usuario_id'] ?? null;
        $idProfissional = null;

        if ($tipoUsuario === 'profissional' && $idUsuario) {
            $profissionalData = $this->profissional->buscarProfissionalPorUsuarioId($idUsuario);
            if ($profissionalData) {
                $idProfissional = $profissionalData->id_profissional;
            }
        }

        $pagina = $_GET['pagina'] ?? 1;
        // Passa o $idProfissional (pode ser null) para a paginação
        $dadosPaginados = $this->agendamento->paginacao((int)$pagina, 10, $idProfissional);

        $totalAgendamentos = $dadosPaginados['total'];

        // Também filtra a busca global para calcular as estatísticas dos cards (pendentes, confirmados...)
        $todosAgendamentos = $this->agendamento->buscarAgendamentos($idProfissional);

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
        $this->verificarAcesso(['admin', 'profissional', 'recepcionista']);
        $agendamento = $this->agendamento->buscarAgendamentoPorId((int)$id);
        if (!$agendamento) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Agendamento não encontrado.");
            return;
        }
        View::render("agendamento/edit", ["agendamento" => $agendamento]);
    }

        public function viewCriarAgendamentos() {
        $this->verificarAcesso(['admin', 'profissional', 'recepcionista']);
        $pacientes = $this->usuario->buscarTodosUsuarios();
        $profissionais = $this->profissional->listarProfissionais();

        View::render("agendamento/create", [
            "pacientes" => $pacientes,
            "profissionais" => $profissionais
        ]);
    }


    public function viewExcluirAgendamentos($id) {
        $this->verificarAcesso(['admin', 'profissional', 'recepcionista']);
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
        
        try {
            $payload = \App\Psico\Core\Auth::check(); // Verifica token JWT
            $id_usuario = $payload->sub;

            $agendamentos = $this->agendamento->buscarAgendamentosPorUsuario((int)$id_usuario);
            
            \App\Psico\Core\Response::success(['agendamentos' => $agendamentos]);

        } catch (\Exception $e) {
            \App\Psico\Core\Response::error($e->getMessage(), 401); 
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