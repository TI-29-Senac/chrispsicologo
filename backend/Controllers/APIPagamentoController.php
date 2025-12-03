<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Pagamento;
use App\Psico\Database\Database;

class APIPagamentoController {
    private $pagamentoModel;
    // Mantenha a mesma chave de API
    private $chaveAPI = "73C60B2A5B23B2300B235AF6EE616F46167F2B830E78F0A8DDCBDF5C9598BCAD";

    public function __construct() {
        $db = Database::getInstance();
        $this->pagamentoModel = new Pagamento($db);
    }

    /**
     * Verifica se o Token Bearer enviado é válido.
     */
    private function buscaChaveAPI() {
        // 1. Tenta obter todos os headers
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = null;

        // 2. Procura pelo header Authorization (case-insensitive e fallback para $_SERVER)
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        // 3. Se o cabeçalho não existe, retorna falso
        if (!$authHeader) {
            return false;
        }

        // 4. Separa "Bearer" do "TOKEN" com segurança
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            $token = $authHeader;
        }

        return $token === $this->chaveAPI;
    }

    /**
     * Lista os pagamentos (GET)
     * URL: /api/pagamentos ou /api/pagamentos/{pagina}
     */
    public function getPagamentos($pagina = 0) {
        // Validação de segurança
        if (!$this->buscaChaveAPI()) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Chave de API inválida ou ausente.']);
            exit;
        }

        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        // Chama o método paginacao do Model Pagamento
        $dados = $this->pagamentoModel->paginacao($pagina, $registros_por_pagina);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => $dados['data'],
            'meta' => [
                'total' => $dados['total'],
                'pagina_atual' => $dados['pagina_atual'],
                'ultima_pagina' => $dados['ultima_pagina']
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Cria um novo pagamento (POST)
     * URL: /api/pagamentos/salvar
     */
    public function salvarPagamento() {
        // Validação de segurança
        if (!$this->buscaChaveAPI()) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Chave de API inválida.']);
            exit;
        }

        header('Content-Type: application/json');
        
        // Lê o JSON do corpo da requisição
        $input = json_decode(file_get_contents('php://input'), true);

        // Validação básica
        if (empty($input) || !is_array($input)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Nenhum dado recebido ou formato inválido.']);
            exit;
        }

        // Verifica campos obrigatórios
        $erros = [];
        if (empty($input['id_agendamento'])) {
            $erros[] = "O campo 'id_agendamento' é obrigatório.";
        }
        if (empty($input['tipo_pagamento'])) {
            $erros[] = "O campo 'tipo_pagamento' é obrigatório (pix, credito, debito, dinheiro).";
        }

        // Validação de tipos permitidos (opcional, mas recomendado)
        $tiposPermitidos = ['pix', 'credito', 'debito', 'dinheiro'];
        if (!empty($input['tipo_pagamento']) && !in_array($input['tipo_pagamento'], $tiposPermitidos)) {
            $erros[] = "Tipo de pagamento inválido. Permitidos: " . implode(', ', $tiposPermitidos);
        }

        if (!empty($erros)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Erro de validação.', 'errors' => $erros]);
            exit;
        }

        // Tenta inserir
        try {
            $novoId = $this->pagamentoModel->inserirPagamento(
                (int)$input['id_agendamento'],
                $input['tipo_pagamento']
            );

            if ($novoId) {
                http_response_code(201); // Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Pagamento registrado com sucesso.',
                    'id_pagamento' => $novoId
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao salvar pagamento no banco de dados. Verifique se o ID do agendamento é válido.'
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Erro interno: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}