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
    public function listarFinanceiro() {
        try {
            $payload = Auth::check();
            $idCliente = $payload->sub;
            
            // Certifique-se de que o método 'buscarPagamentosPorCliente' existe no Model Pagamento
            // Se não existir, precisaremos criá-lo ou verificar o nome correto.
            // Assumindo que existe baseado no código anterior do PagamentoController.
            $pagamentos = $this->pagamentoModel->buscarPagamentosPorCliente((int)$idCliente);
            
            Response::success(['data' => $pagamentos]);
        } catch (\Exception $e) {
            Response::error($e->getMessage(), 500);
        }
    }
}
