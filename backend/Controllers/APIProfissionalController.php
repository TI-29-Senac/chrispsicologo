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
        // Validação de segurança
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        // Usa a paginação do Model que retorna dados + infos de usuário
        $dados = $this->profissionalModel->paginacao($pagina, $registros_por_pagina);

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

    public function salvarProfissional() {
        // Validação de segurança
        if (!APIAutenticador::validar()) {
            APIAutenticador::enviarErroNaoAutorizado();
        }

        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input) || !is_array($input)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Nenhum dado recebido ou formato inválido.']);
            exit;
        }

        // Validação de campos obrigatórios
        $erros = [];
        if (empty($input['id_usuario'])) {
            $erros[] = "O campo 'id_usuario' é obrigatório.";
        }
        if (empty($input['especialidade'])) {
            $erros[] = "O campo 'especialidade' é obrigatório.";
        }
        if (!isset($input['valor_consulta'])) {
            $erros[] = "O campo 'valor_consulta' é obrigatório.";
        }

        if (!empty($erros)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Erro de validação.', 'errors' => $erros]);
            exit;
        }

        try {
            // Prepara os dados opcionais com valores padrão
            $sinal = $input['sinal_consulta'] ?? 0.0;
            $publico = isset($input['publico']) ? (int)$input['publico'] : 0;
            $sobre = $input['sobre'] ?? null;
            $ordem = $input['ordem_exibicao'] ?? 99;
            $img = null; // Upload de imagem via JSON é complexo (base64), mantemos null por padrão
            $tiposIds = $input['tipos_atendimento'] ?? []; // Array de IDs de tipos [1, 2]

            // Insere
            $novoId = $this->profissionalModel->inserirProfissional(
                (int)$input['id_usuario'],
                $input['especialidade'],
                (float)$input['valor_consulta'],
                (float)$sinal,
                $publico,
                $sobre,
                (int)$ordem,
                $img,
                $tiposIds
            );

            if ($novoId) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Profissional cadastrado com sucesso.',
                    'id_profissional' => $novoId
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao salvar profissional no banco de dados.'
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Erro interno: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}