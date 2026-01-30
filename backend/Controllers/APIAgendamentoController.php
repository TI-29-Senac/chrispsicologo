<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;
use App\Psico\Core\Auth;
use App\Psico\Core\Response;

class APIAgendamentoController {
    private $agendamentoModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->agendamentoModel = new Agendamento($db);
    }

    public function getAgendamentos($pagina = 0) {
        // A autenticação já é garantida pelo middleware check() em Rotas.php
        // se esta rota estiver dentro do grupo protegido.
        // Se quisermos garantir payload aqui: $payload = Auth::check();

        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $dados = $this->agendamentoModel->paginacao($pagina, $registros_por_pagina);

        Response::success([
            'data' => $dados['data'],
            'meta' => [
                'total' => $dados['total'],
                'pagina_atual' => $dados['pagina_atual'],
                'ultima_pagina' => $dados['ultima_pagina']
            ]
        ]);
    }

    public function salvarAgendamento() {
        // A autenticação já é garantida pelo middleware check() em Rotas.php
        $payload = Auth::check(); // Obtém dados do usuário logado (opcional, para validação extra)

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input) || !is_array($input)) {
            Response::error('JSON inválido.', 400);
        }

        if (empty($input['id_usuario']) || empty($input['id_profissional']) || empty($input['data_agendamento'])) {
            Response::error('Campos obrigatórios: id_usuario, id_profissional, data_agendamento.', 400);
        }

        try {
            // Conversão de status (opcional: garantir que status_consulta seja válido)
            $status = $input['status_consulta'] ?? 'pendente';
             
            $novoId = $this->agendamentoModel->inserirAgendamento(
                (int)$input['id_usuario'],
                (int)$input['id_profissional'],
                $input['data_agendamento'],
                $status
            );

            if ($novoId) {
                Response::success(['message' => 'Agendamento criado.', 'id_agendamento' => $novoId], 201);
            } else {
                Response::error('Erro ao salvar no banco.', 500);
            }
        } catch (\Exception $e) {
            Response::error('Erro interno: ' . $e->getMessage(), 500);
        }
    }
}
