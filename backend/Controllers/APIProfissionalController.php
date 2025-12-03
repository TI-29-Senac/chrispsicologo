<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\APIAutenticador;

class APIProfissionalController {
    private $profissionalModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->profissionalModel = new Profissional($db);
    }

    public function getProfissionais($pagina = 0) {
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $dados = $this->profissionalModel->paginacao($pagina, $registros_por_pagina);

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode(['status' => 'success', 'data' => $dados['data']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function salvarProfissional() {
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_usuario']) || empty($input['especialidade']) || !isset($input['valor_consulta'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
            exit;
        }

        try {
            $novoId = $this->profissionalModel->inserirProfissional(
                (int)$input['id_usuario'],
                $input['especialidade'],
                (float)$input['valor_consulta'],
                (float)($input['sinal_consulta'] ?? 0),
                (int)($input['publico'] ?? 0),
                $input['sobre'] ?? null,
                (int)($input['ordem_exibicao'] ?? 99),
                null,
                $input['tipos_atendimento'] ?? []
            );

            if ($novoId) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'id_profissional' => $novoId]);
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