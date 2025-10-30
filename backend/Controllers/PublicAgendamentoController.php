<?php
namespace App\Psico\Controllers;

// Importe todas as classes que os métodos vão usar
use App\Psico\Models\Agendamento;
use App\Psico\Models\Usuario;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use DateTime;

class PublicAgendamentoController {
    
    public $agendamento;   
    public $db;
    public $usuario;
    public $profissional;

    // Este construtor NÃO chama o AuthenticatedController
    public function __construct(){
        // Inicia a sessão para podermos verificar o login do *cliente*
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = Database::getInstance();
        $this->agendamento = new Agendamento($this->db);
        $this->usuario = new Usuario($this->db);
        $this->profissional = new Profissional($this->db); 
    }

    /**
     * Método público para buscar disponibilidade.
     * (Este é o código que estava no AgendamentoController)
     */
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

    /**
     * Método público para salvar o agendamento do cliente.
     * (Este é o código que estava no AgendamentoController)
     */
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
}