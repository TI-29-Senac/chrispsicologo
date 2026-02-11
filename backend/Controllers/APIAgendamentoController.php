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
        // Garante autenticação via Token
        $payload = Auth::check(); 
        $idUsuarioToken = $payload->sub;

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input) || !is_array($input)) {
            Response::error('JSON inválido.', 400);
            return;
        }

        // Validação básica
        if (empty($input['id_profissional']) || empty($input['data_agendamento'])) {
            Response::error('Campos obrigatórios: id_profissional, data_agendamento.', 400);
            return;
        }

        try {
            // Força o ID do usuário logado
            $status = 'pendente'; 
             
            $novoId = $this->agendamentoModel->inserirAgendamento(
                (int)$idUsuarioToken, // Usa ID do Token
                (int)$input['id_profissional'],
                $input['data_agendamento'], // Verificar formato YYYY-MM-DD HH:MM
                $status
            );

            if ($novoId) {
                Response::success(['message' => 'Agendamento criado com sucesso.', 'id_agendamento' => $novoId], 201);
            } else {
                Response::error('Erro ao salvar no banco.', 500);
            }
        } catch (\Exception $e) {
            Response::error('Erro interno: ' . $e->getMessage(), 500);
        }
    }
    public function buscarMeusAgendamentos() {
        try {
            // Verifica o token JWT
            $payload = Auth::check(); 
            $id_usuario = $payload->sub;

            // Busca os agendamentos do usuário
            $agendamentos = $this->agendamentoModel->buscarAgendamentosPorUsuario((int)$id_usuario);
            
            // Retorna sucesso
            Response::success(['agendamentos' => $agendamentos]);

        } catch (\Exception $e) {
            Response::error($e->getMessage(), 401); 
        }
    }
}
