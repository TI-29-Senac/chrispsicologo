<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;
use App\Psico\Core\APIAutenticador;

class APIAgendamentoController {
    private $agendamentoModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->agendamentoModel = new Agendamento($db);
    }

    public function getAgendamentos($pagina = 0) {
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $dados = $this->agendamentoModel->paginacao($pagina, $registros_por_pagina);

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

    public function salvarAgendamento() {
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input) || !is_array($input)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'JSON inválido.']);
            exit;
        }

        if (empty($input['id_usuario']) || empty($input['id_profissional']) || empty($input['data_agendamento'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Campos obrigatórios: id_usuario, id_profissional, data_agendamento.']);
            exit;
        }

        try {
            $novoId = $this->agendamentoModel->inserirAgendamento(
                (int)$input['id_usuario'],
                (int)$input['id_profissional'],
                $input['data_agendamento'],
                $input['status_consulta'] ?? 'pendente'
            );

            if ($novoId) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'message' => 'Agendamento criado.', 'id_agendamento' => $novoId]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar no banco.']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erro interno: ' . $e->getMessage()]);
        }
        exit;
    }
}