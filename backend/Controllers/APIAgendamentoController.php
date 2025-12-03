<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;

class APIAgendamentoController {
    private $agendamentoModel;
    // Mantenha a mesma chave ou use variáveis de ambiente para maior segurança
    private $chaveAPI = "73C60B2A5B23B2300B235AF6EE616F46167F2B830E78F0A8DDCBDF5C9598BCAD";

    public function __construct() {
        $db = Database::getInstance();
        $this->agendamentoModel = new Agendamento($db);
    }

    /**
     * Verifica se o Token Bearer enviado é válido.
     * Versão corrigida para compatibilidade com Apache/Nginx e Headers.
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
            // Fallback caso tenha enviado apenas o token sem "Bearer"
            $token = $authHeader;
        }

        return $token === $this->chaveAPI;
    }

    /**
     * Lista os agendamentos (GET)
     */
    public function getAgendamentos($pagina = 0) {
        // Verifica autenticação
        if (!$this->buscaChaveAPI()) {
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'Chave de API inválida ou ausente.']);
            exit;
        }

        // Lógica de paginação igual ao UsuarioController
        $registros_por_pagina = $pagina === 0 ? 200 : 10; // Padrão 10 se paginado, 200 se "todos"
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        // Chama o método paginacao do Model Agendamento
        // Nota: O método paginacao() no Model retorna um array com 'data', 'total', etc.
        $dados = $this->agendamentoModel->paginacao($pagina, $registros_por_pagina);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => $dados['data'], // Retorna apenas o array de dados
            'meta' => [
                'total' => $dados['total'],
                'pagina_atual' => $dados['pagina_atual'],
                'ultima_pagina' => $dados['ultima_pagina']
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Cria um novo agendamento (POST)
     */
    public function salvarAgendamento() {
        // Verifica autenticação (Recomendado proteger a criação também)
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
        if (empty($input['id_usuario']) || empty($input['id_profissional']) || empty($input['data_agendamento'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Campos obrigatórios: id_usuario, id_profissional, data_agendamento.']);
            exit;
        }

        // Tenta inserir
        try {
            $novoId = $this->agendamentoModel->inserirAgendamento(
                (int)$input['id_usuario'],
                (int)$input['id_profissional'],
                $input['data_agendamento'], // Formato 'Y-m-d H:i:s'
                $input['status_consulta'] ?? 'pendente' // Padrão 'pendente'
            );

            if ($novoId) {
                http_response_code(201); // Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Agendamento criado com sucesso.',
                    'id_agendamento' => $novoId
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao salvar agendamento no banco de dados.'
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