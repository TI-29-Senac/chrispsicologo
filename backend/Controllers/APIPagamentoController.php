<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Pagamento;
use App\Psico\Database\Database;
use App\Psico\Core\APIAutenticador;

class APIPagamentoController {
    private $pagamentoModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->pagamentoModel = new Pagamento($db);
    }

    public function getPagamentos($pagina = 0) {
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $dados = $this->pagamentoModel->paginacao($pagina, $registros_por_pagina);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(['status' => 'success', 'data' => $dados['data']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function salvarPagamento() {
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_agendamento']) || empty($input['tipo_pagamento'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
            exit;
        }

        try {
            $novoId = $this->pagamentoModel->inserirPagamento(
                (int)$input['id_agendamento'],
                $input['tipo_pagamento']
            );

            if ($novoId) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'id_pagamento' => $novoId]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar.']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}