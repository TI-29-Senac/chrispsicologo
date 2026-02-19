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
    $input = json_decode(file_get_contents('php://input'), true);
    // Validação básica
    if (empty($input['email_usuario'])) {
        Response::error('Email obrigatório.', 400);
    }
    try {
        // 1. Tenta achar o usuário pelo ID ou Email
        $usuarioExistente = null;
        if (!empty($input['id_usuario'])) {
            $usuarioExistente = $this->usuarioModel->buscarPorId($input['id_usuario']);
        }
        if (!$usuarioExistente && !empty($input['email_usuario'])) {
            $usuarioExistente = $this->usuarioModel->buscarPorEmail($input['email_usuario']);
        }
        if ($usuarioExistente) {
            // --- ATUALIZAR (UPDATE) ---
            // Prepara dados para atualização
            $dados = [
                'nome_usuario' => $input['nome_usuario'],
                'tipo_usuario' => $input['tipo_usuario'] ?? 'cliente',
                'cpf' => $input['cpf'] ?? '',
                'atualizado_em' => date('Y-m-d H:i:s')
            ];
            // O PULO DO GATO: Se vier excluido_em, salvamos ele!
            if (array_key_exists('excluido_em', $input)) {
                $dados['excluido_em'] = $input['excluido_em']; // Pode ser NULL (restaurar) ou DATA (excluir)
            }
            // Se vier senha nova, atualiza
            if (!empty($input['senha_usuario'])) {
                $dados['senha_usuario'] = $input['senha_usuario'];
            }
            // Chama o método de editar do seu Model
            $this->usuarioModel->editarUsuario($usuarioExistente['id_usuario'], $dados);
            Response::success(['message' => 'Usuário atualizado com sucesso.', 'id_usuario' => $usuarioExistente['id_usuario']], 200);
        } else {
            // --- INSERIR (CREATE) ---
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
                Response::error("Falha na inserção.", 500);
            }
        }
    } catch (\Exception $e) {
        // Se der erro de duplicidade
        if (strpos($e->getMessage(), 'Duplicate') !== false || $e->getCode() == 23000) {
             Response::error('Email já cadastrado.', 409);
        }
        Response::error("Erro no servidor: " . $e->getMessage(), 500);
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

    public function getMeuPerfil() {
        $payload = Auth::check();
        $id = $payload->sub;
        $usuario = $this->usuarioModel->buscarUsuarioPorId($id);
        if ($usuario) {
            unset($usuario->senha_usuario);
            Response::success(['data' => $usuario]);
        } else {
            Response::error('Usuário não encontrado', 404);
        }
    }
}
