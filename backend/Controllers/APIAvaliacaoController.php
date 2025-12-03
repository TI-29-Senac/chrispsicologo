<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;
use App\Psico\Core\APIAutenticador;

class APIAvaliacaoController {
    private $avaliacaoModel;
    // Mantenha a mesma chave de API para consistência

    public function __construct() {
        $db = Database::getInstance();
        $this->avaliacaoModel = new Avaliacao($db);
    }

    public function getAvaliacoes($pagina = 0) {
        // Validação de segurança
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        $registros_por_pagina = $pagina === 0 ? 200 : 10; // 200 para "todos" ou 10 por página
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        // Chama o método paginacao do Model Avaliacao
        $dados = $this->avaliacaoModel->paginacao($pagina, $registros_por_pagina);

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
     * Cria uma nova avaliação (POST)
     * URL: /api/avaliacoes/salvar
     */
    public function salvarAvaliacao() {
        // Validação de segurança
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        header('Content-Type: application/json');
        
        // Lê o JSON do corpo da requisição
        $input = json_decode(file_get_contents('php://input'), true);

        // Validação básica se recebeu algo
        if (empty($input) || !is_array($input)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Nenhum dado recebido ou formato inválido.']);
            exit;
        }

        // Verifica campos obrigatórios
        $erros = [];
        if (empty($input['id_cliente'])) {
            $erros[] = "O campo 'id_cliente' é obrigatório.";
        }
        if (empty($input['id_profissional'])) {
            $erros[] = "O campo 'id_profissional' é obrigatório.";
        }
        if (!isset($input['nota_avaliacao']) || !is_numeric($input['nota_avaliacao'])) {
            $erros[] = "O campo 'nota_avaliacao' é obrigatório e deve ser numérico.";
        } elseif ($input['nota_avaliacao'] < 1 || $input['nota_avaliacao'] > 5) {
            $erros[] = "A nota deve ser entre 1 e 5.";
        }
        if (empty($input['descricao_avaliacao'])) {
            $erros[] = "O campo 'descricao_avaliacao' é obrigatório.";
        }

        if (!empty($erros)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Erro de validação.', 'errors' => $erros]);
            exit;
        }

        // Tenta inserir
        try {
            // Verifica se o cliente já avaliou este profissional (opcional, mas recomendado)
            $avaliacaoExistente = $this->avaliacaoModel->buscarAvaliacaoPorClienteEProfissional(
                (int)$input['id_cliente'], 
                (int)$input['id_profissional']
            );

            if ($avaliacaoExistente) {
                http_response_code(409); // Conflict
                echo json_encode(['status' => 'error', 'message' => 'Este cliente já avaliou este profissional.']);
                exit;
            }

            $novoId = $this->avaliacaoModel->inserirAvaliacao(
                (int)$input['id_cliente'],
                (int)$input['id_profissional'],
                $input['descricao_avaliacao'],
                (int)$input['nota_avaliacao']
            );

            if ($novoId) {
                http_response_code(201); // Created
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Avaliação registrada com sucesso.',
                    'id_avaliacao' => $novoId
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao salvar avaliação no banco de dados.'
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