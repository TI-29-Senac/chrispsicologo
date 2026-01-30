<?php

namespace App\Psico\Controllers;

use App\Psico\Models\Usuario;
use App\Psico\Database\Database;
use App\Psico\Core\Auth;
use App\Psico\Core\Response;

class APIUsuarioController {
    private $usuarioModel;

    public function __construct(){
        $db = Database::getInstance();
        $this->usuarioModel = new Usuario($db);
    }

    public function getUsuarios($pagina = 0){
        // Auth::check() já é chamado pelo Middleware na Rotas.php, 
        // mas pode ser chamado aqui também para garantir o payload do usuário se necessário.
        // Como o middleware já barra, aqui assumimos autenticado ou re-verificamos.
        // Se quisermos o ID do usuário: $payload = Auth::check();
        
        $registros_por_pagina = $pagina === 0 ? 200 : 10;
        $pagina = $pagina === 0 ? 1 : (int)$pagina;

        $resultado = $this->usuarioModel->paginacaoAPI($pagina, $registros_por_pagina);
        
        if (isset($resultado['data']) && is_array($resultado['data'])) {
            foreach($resultado['data'] as &$usuario){
                unset($usuario['senha_usuario']);
            }
            unset($usuario); // Quebra referência
        }

        Response::success($resultado['data']);
    }

    public function buscarPorId($id) {
        $usuario = $this->usuarioModel->buscarUsuarioPorId((int)$id);
        if ($usuario) {
            unset($usuario->senha_usuario);
            Response::success($usuario);
        } else {
            Response::error('Usuário não encontrado.', 404);
        }
    }

    public function salvarUsuario(){
        // Nota: Se esta rota for pública para registro, remova a proteção no Rotas.php ou aqui.
        // Assumindo criação via Admin/Auth por enquanto.
        
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['nome_usuario']) || empty($input['email_usuario']) || empty($input['senha_usuario'])) {
            Response::error('Parâmetros obrigatórios faltando (nome, email, senha).', 400);
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
                Response::success(['message' => 'Usuário criado.', 'id_usuario' => $novoId], 201);
            } else {
                Response::error("Falha na inserção do banco de dados.", 500);
            }
        } catch (\Exception $e) {
            // Verifica duplicidade (código 23000 usualmente)
            if (strpos($e->getMessage(), 'Duplicate') !== false || $e->getCode() == 23000) {
                 Response::error('Email já cadastrado.', 409);
            }
            Response::error($e->getMessage(), 500);
        }
    }

    public function deletarUsuario($id){
        // Exemplo de verificação de permissão: Apenas admin
        $payload = Auth::check();
        if ($payload->role !== 'admin') {
            Response::error('Acesso negado. Apenas administradores.', 403);
        }

        if ($this->usuarioModel->excluirUsuario((int)$id)) {
            Response::success(['message' => 'Usuário excluído com sucesso.']);
        } else {
            Response::error('Erro ao excluir usuário ou usuário não encontrado.', 500);
        }
    }
}
