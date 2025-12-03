<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;
use App\Psico\Core\APIAutenticador;

class APIAgendamentoController {
    private $agendamentoModel;
    // Mantenha a mesma chave ou use variáveis de ambiente para maior segurança

    public function __construct() {
        $db = Database::getInstance();
        $this->agendamentoModel = new Agendamento($db);
    }

    public function getAgendamentos($pagina = 0) {
        // Verifica autenticação
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
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

    public function salvarAgendamento() {
        // Verifica autenticação (Recomendado proteger a criação também)
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
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