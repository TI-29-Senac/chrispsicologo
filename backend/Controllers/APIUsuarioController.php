<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Usuario;
use App\Psico\Database\Database;
use App\Psico\Core\APIAutenticador;

class APIUsuarioController {
    private $usuarioModel;

    public function __construct(){
        $db = Database::getInstance();
        $this->usuarioModel = new Usuario($db);
    }

    public function getUsuarios($pagina = 0){
        // Validação Centralizada em uma linha
        if (!APIAutenticador::validar()) {
           APIAutenticador::enviarErroNaoAutorizado();
        }

        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $resultado = $this->usuarioModel->paginacaoAPI($pagina, $registros_por_pagina);
        
        if (isset($resultado['data']) && is_array($resultado['data'])) {
            foreach($resultado['data'] as &$usuario){
                unset($usuario['senha_usuario']);
            }
            unset($usuario);
        }

        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => $resultado['data']
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function salvarUsuario(){
    if (!APIAutenticador::validar()) {
        APIAutenticador::enviarErroNaoAutorizado();
    }

    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['nome_usuario']) || empty($input['email_usuario']) || empty($input['senha_usuario'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Parâmetros obrigatórios faltando no JSON.']);
        exit;
    }

    try {
        $novoId = $this->usuarioModel->inserirUsuario(
            $input["nome_usuario"],
            $input["email_usuario"],
            $input["senha_usuario"],
            $input["tipo_usuario"] ?? 'cliente',
            $input["cpf"] ?? ''
        );

        if ($novoId) {
            echo json_encode(['status' => 'success', 'message' => 'Usuário criado.', 'id_usuario' => $novoId]);
        } else {
            throw new \Exception("Falha na inserção do banco de dados.");
        }
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
}