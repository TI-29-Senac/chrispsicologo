<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use App\Psico\Core\Auth;
use App\Psico\Core\Response;

class APIProfissionalController {
    private $profissionalModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->profissionalModel = new Profissional($db);
    }

    public function getProfissionais($pagina = 0) {
        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $dados = $this->profissionalModel->paginacao($pagina, $registros_por_pagina);

        Response::success(['data' => $dados['data']]);
    }

    public function salvarProfissional() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_usuario']) || empty($input['especialidade']) || !isset($input['valor_consulta'])) {
            Response::error('Dados incompletos (id_usuario, especialidade e valor_consulta são obrigatórios).', 400);
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
                Response::success(['id_profissional' => $novoId], 201);
            } else {
                Response::error('Erro ao salvar profissional.', 500);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
