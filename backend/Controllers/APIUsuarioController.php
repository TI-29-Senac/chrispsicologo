<?php
 
namespace App\Psico\Controllers;
 
use App\Psico\Models\Usuario;
use App\Psico\Database\Database;
 
    class APIUsuarioController{
        private $usuarioModel;
        private $chaveAPI = "73C60B2A5B23B2300B235AF6EE616F46167F2B830E78F0A8DDCBDF5C9598BCAD";
        public function __construct(){
            $db = Database::getInstance();
            $this->usuarioModel = new Usuario($db);

        }

    private function buscaChaveAPI(){
        $headers = getallheaders();
        $token = explode(" ", $headers['Authorization'])[1];
        return $token === $this->chaveAPI;
    }

    public function getUsuarios($pagina=0){
        if (!$this->buscaChaveAPI()) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error', 'message' => 'Chave de API inválida.'
            ]);
            exit;
        }
        // Condição ternaria é igual if else
        $registros_por_pagina = $pagina===0 ? 200 : 5;
        $pagina = $pagina===0 ? 1 : (int)$pagina;
        $dados = $this -> usuarioModel -> paginacaoAPI($pagina, $registros_por_pagina);
        foreach($dados['data'] as &$usuario){
            unset($usuario['senha_usuario']);
        }
        unset($usuario);
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => $dados
        ], JSON_PRETTY_PRINT  |  JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function salvarUsuario(){
        header('Content-Type: application/json');
        $usuario = json_decode(file_get_contents('php://input'), true);
        if (empty($usuario) || !is_array($usuario)) {
            echo json_encode(['status' => 'error', 'message' => 'Nenhum item recebido no usuario.']);
            exit;
        }
        $novoPedidoId = $this->usuarioModel->inserirUsuario(
            $usuario["nome_usuario"],
            $usuario["email_usuario"],
            $usuario["senha_usuario"],
            $usuario["tipo_usuario"],
            $usuario["status_usuario"],
        );
        if ($novoPedidoId) {
            http_response_code(201);
            echo json_encode([
                'status' => 'success', 'message' => 'Cadastrado com sucesso.', 'id_pedido' => $novoPedidoId
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error', 'message' => 'Ocorreu um erro ao processar seu pedido. Tente novamente.'
            ]);
        }
        exit;

    
    }
}