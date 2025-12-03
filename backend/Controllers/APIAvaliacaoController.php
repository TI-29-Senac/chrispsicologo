<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;
use App\Psico\Core\APIAutenticador;

class APIAvaliacaoController {
    private $avaliacaoModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->avaliacaoModel = new Avaliacao($db);
    }

    public function getAvaliacoes($pagina = 0) {
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $dados = $this->avaliacaoModel->paginacao($pagina, $registros_por_pagina);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(['status' => 'success', 'data' => $dados['data']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function salvarAvaliacao() {
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_cliente']) || empty($input['id_profissional']) || empty($input['nota_avaliacao'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
            exit;
        }

        try {
            $novoId = $this->avaliacaoModel->inserirAvaliacao(
                (int)$input['id_cliente'],
                (int)$input['id_profissional'],
                $input['descricao_avaliacao'] ?? '',
                (int)$input['nota_avaliacao']
            );

            if ($novoId) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'id_avaliacao' => $novoId]);
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