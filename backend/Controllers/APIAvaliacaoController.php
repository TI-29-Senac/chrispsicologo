<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;
use App\Psico\Core\Auth;
use App\Psico\Core\Response;

class APIAvaliacaoController {
    private $avaliacaoModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->avaliacaoModel = new Avaliacao($db);
    }

    public function getAvaliacoes($pagina = 0) {
        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $dados = $this->avaliacaoModel->paginacao($pagina, $registros_por_pagina);

        Response::success(['data' => $dados['data']]);
    }

    public function salvarAvaliacao() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_cliente']) || empty($input['id_profissional']) || empty($input['nota_avaliacao'])) {
            Response::error('Dados incompletos (id_cliente, id_profissional e nota_avaliacao são obrigatórios).', 400);
        }

        try {
            $novoId = $this->avaliacaoModel->inserirAvaliacao(
                (int)$input['id_cliente'],
                (int)$input['id_profissional'],
                $input['descricao_avaliacao'] ?? '',
                (int)$input['nota_avaliacao']
            );

            if ($novoId) {
                Response::success(['id_avaliacao' => $novoId], 201);
            } else {
                Response::error('Erro ao salvar avaliação.', 500);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
