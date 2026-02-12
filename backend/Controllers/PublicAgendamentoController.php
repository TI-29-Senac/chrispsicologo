<?php
namespace App\Psico\Controllers;

// Importe todas as classes que os métodos vão usar
use App\Psico\Models\Agendamento;
use App\Psico\Models\Usuario;
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\Auth; // Adicionando Auth
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

        $id_usuario = null;

        // 1. Tenta autenticação por Sessão (Legacy/Web)
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['usuario_tipo'] === 'cliente') {
             $id_usuario = $_SESSION['usuario_id'];
        } 
        // 2. Tenta autenticação por JWT (API/Modern Web)
        else {
            try {
                $payload = Auth::check(); // Verifica o cabeçalho Authorization: Bearer
                $id_usuario = $payload->sub;
                
                // Opcional: Verificar se é cliente, se necessário, buscando no BD
                // Mas geralmente quem tem token válido pode agendar
            } catch (\Exception $e) {
                // Token inválido ou não fornecido
            }
        }

        if (!$id_usuario) {
             http_response_code(401); 
             echo json_encode(['success' => false, 'message' => 'Acesso não autorizado. Faça login como cliente para agendar.']);
             return;
        }

        // Continua com $id_usuario definido...
        
        // Ler input JSON se disponível, fallback para $_POST
        $json_input = json_decode(file_get_contents('php://input'), true);

        $id_profissional = $json_input['id_profissional'] ?? $_POST['id_profissional'] ?? null;
        $data_selecionada = $json_input['data_selecionada'] ?? $_POST['data_selecionada'] ?? null;
        $horario_selecionado = $json_input['horario_selecionado'] ?? $_POST['horario_selecionado'] ?? null;

        
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


        
        try {
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
                throw new \Exception("Erro ao salvar o agendamento no banco de dados.");
            }
        } catch (\Exception $e) {
            if ($e->getMessage() === "Este horário já está reservado.") {
                http_response_code(409); // Conflict
            } else {
                http_response_code(500);
            }
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getDetalhesPagamento($id) {
        
        while (ob_get_level() > 0) { 
             ob_end_clean(); 
        }

        header('Content-Type: application/json'); 
        
        try {
            $detalhes = Agendamento::getDetalhesPagamento($id); 
            
            if (!$detalhes) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Agendamento não encontrado.']);
                exit; 
            }

            $response = [
                'success' => true,
                'agendamento' => [
                    'id_agendamento' => $detalhes['id_agendamento'],
                    'data_agendamento' => $detalhes['data_agendamento'], // Campo DATETIME
                    'valor_sinal' => $detalhes['sinal_consulta']
                ],
                'profissional' => [
                    'id_profissional' => $detalhes['id_profissional'],
                    'nome_usuario' => $detalhes['profissional_nome']
                ],
                'cliente' => [
                    'id_usuario' => $detalhes['id_usuario'],
                    'nome_completo' => $detalhes['cliente_nome']
                ]
            ];

            http_response_code(200);
            echo json_encode($response);
            
        } catch (\Exception $e) {
            error_log("Erro ao buscar detalhes de pagamento: " . $e->getMessage()); 
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro interno ao buscar detalhes: ' . $e->getMessage()]);
        } finally {
            // Garante que a execução do script para imediatamente após a resposta.
            exit;
        }
    }

    public function confirmarSinal($id) {
        header('Content-Type: application/json');

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID do agendamento inválido.']);
            return;
        }

        // Ler o corpo da requisição (JSON)
        $input = json_decode(file_get_contents('php://input'), true);
        $tipo_pagamento = $input['tipo_pagamento'] ?? 'pix'; // Default pix

        // Mapeamento simples (idealmente buscar do banco 'forma_pagamento')
        // 1 = Pix, 2 = Cartão de Crédito
        $id_forma_pagamento = ($tipo_pagamento === 'cartao' || $tipo_pagamento === 'credito') ? 2 : 1;

        // Chama o método estático do Model que já implementa a transação e updates
        $sucesso = Agendamento::marcarSinalComoPago($id, $id_forma_pagamento);

        if ($sucesso) {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Pagamento confirmado com sucesso!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao processar confirmação de pagamento.']);
        }
    }
}