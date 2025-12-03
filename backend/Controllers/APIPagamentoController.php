<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Pagamento;
use App\Psico\Database\Database;
use App\Psico\Core\APIAutenticador;

class APIPagamentoController {
    private $pagamentoModel;
    // Mantenha a mesma chave de API

    public function __construct() {
        $db = Database::getInstance();
        $this->pagamentoModel = new Pagamento($db);
    }

    public function getPagamentos($pagina = 0) {
        // Validação de segurança
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
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

    public function salvarPagamento() {
        // Validação de segurança
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