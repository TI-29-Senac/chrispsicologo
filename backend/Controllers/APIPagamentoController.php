<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Pagamento;
use App\Psico\Database\Database;
use App\Psico\Core\Auth;
use App\Psico\Core\Response;

class APIPagamentoController {
    private $pagamentoModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->pagamentoModel = new Pagamento($db);
    }

    public function getPagamentos($pagina = 0) {
        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $dados = $this->pagamentoModel->paginacao($pagina, $registros_por_pagina);

        Response::success(['data' => $dados['data']]);
    }

    public function salvarPagamento() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_agendamento']) || empty($input['tipo_pagamento'])) {
            Response::error('Dados incompletos (id_agendamento e tipo_pagamento são obrigatórios).', 400);
        }

        try {
            // Nota: O model inserirPagamento provavelmente espera int e string.
            // Validações extras poderiam ser feitas aqui.
            $novoId = $this->pagamentoModel->inserirPagamento(
                (int)$input['id_agendamento'],
                $input['tipo_pagamento']
            );

            if ($novoId) {
                Response::success(['id_pagamento' => $novoId], 201);
            } else {
                Response::error('Erro ao salvar pagamento.', 500);
            }
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
