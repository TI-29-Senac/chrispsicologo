<?php
namespace App\Psico\Controllers;

use App\Psico\Database\Database;
use App\Psico\Models\Agendamento;
use App\Psico\Models\Pagamento;
use App\Psico\Models\Usuario;
use App\Psico\Models\Profissional;
use App\Psico\Core\Response;

class DesktopApiController {
    private $db;



    public function __construct() {

        // O Response helper já define o Content-Type, mas para OPTIONS é bom manter
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        $this->db = Database::getInstance();
    }

    public function login() {

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

        $input = $this->getInput();

        if (empty($input['email']) || empty($input['senha'])) {
            Response::error("Email e senha são obrigatórios."); // Usando Helper
            return; // O helper dá exit, mas o return satisfaz a IDE
        }

        try {
            $model = new Usuario($this->db);
            $usuario = $model->autenticarUsuario($input['email'], $input['senha']);

            if ($usuario) {
                unset($usuario->senha_usuario);
                
                // GERAÇÃO DO TOKEN JWT
                // Supondo que $usuario->id_usuario e $usuario->tipo_usuario existam
                $token = \App\Psico\Core\Auth::generate($usuario->id_usuario, $usuario->tipo_usuario);
                $refreshToken = \App\Psico\Core\Auth::generateRefreshToken($usuario->id_usuario);

                Response::success([
                    'usuario' => $usuario,
                    'token' => $token,
                    'refresh_token' => $refreshToken
                ]);
            } else {
                Response::error("Email ou senha incorretos.");
            }
        } catch (\Exception $e) { 
            Response::error($e->getMessage(), 500);
        }
    }

    public function refreshToken() {
        $this->setCors();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

        $input = $this->getInput();
        $refreshToken = $input['refresh_token'] ?? null;

        if (!$refreshToken) {
            Response::error("Refresh token não fornecido.", 400);
        }

        $userId = \App\Psico\Core\Auth::verifyRefreshToken($refreshToken);

        if (!$userId) {
            Response::error("Refresh token inválido ou expirado.", 401);
        }

        try {
            // Revoga o atual (Rotação)
            \App\Psico\Core\Auth::revokeRefreshToken($refreshToken);

            // Busca dados do usuário para gerar novo token (precisamos da role)
            $model = new Usuario($this->db);
            $usuario = $model->buscarUsuarioPorId($userId);

            if (!$usuario) {
                Response::error("Usuário não encontrado.", 404);
            }

            // Gera novos tokens
            $newToken = \App\Psico\Core\Auth::generate($usuario->id_usuario, $usuario->tipo_usuario);
            $newRefreshToken = \App\Psico\Core\Auth::generateRefreshToken($usuario->id_usuario);

            Response::success([
                'token' => $newToken,
                'refresh_token' => $newRefreshToken
            ]);

        } catch (\Exception $e) {
            Response::error("Erro ao renovar token: " . $e->getMessage(), 500);
        }
    }

    public function logout() {
        $this->setCors();
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { http_response_code(200); exit(); }

        $input = $this->getInput();
        $refreshToken = $input['refresh_token'] ?? null;

        if ($refreshToken) {
            \App\Psico\Core\Auth::revokeRefreshToken($refreshToken);
        }

        Response::success(['message' => 'Logout realizado com sucesso.']);
    }

    // --- USUÁRIOS ---
    public function listarUsuarios() {
        try {
            $model = new Usuario($this->db);
            $dados = $model->buscarTodosUsuarios();
            foreach($dados as $k => $v) unset($dados[$k]->senha_usuario);
            Response::success(['data' => $dados]);
        } catch (\Exception $e) { Response::error($e->getMessage(), 500); }
    }

    public function criarUsuario() {
        $input = $this->getInput();
        try {
            $model = new Usuario($this->db);
            if ($model->buscarUsuarioPorEmail($input['email_usuario'])) {
                Response::error('Email já cadastrado');
            }
            $model->inserirUsuario(
                $input['nome_usuario'], $input['email_usuario'], 
                $input['senha_usuario'] ?? '123456', 
                $input['tipo_usuario'] ?? 'cliente', 
                $input['cpf'] ?? ''
            );
            Response::success();
        } catch (\Exception $e) { Response::error($e->getMessage(), 500); }
    }

    public function editarUsuario($id) {
        $input = $this->getInput();
        try {
            $model = new Usuario($this->db);
            $usuarioAtual = $model->buscarUsuarioPorId($id);
            
            if (!$usuarioAtual) {
                Response::error("Usuário não encontrado.", 404);
            }
            $novaSenha = !empty($input['senha_usuario']) ? $input['senha_usuario'] : null;

            $model->atualizarUsuario(
                $id,
                $input['nome_usuario'] ?? $usuarioAtual->nome_usuario,
                $input['email_usuario'] ?? $usuarioAtual->email_usuario,
                $novaSenha, 
                $input['tipo_usuario'] ?? $usuarioAtual->tipo_usuario,
                $input['cpf'] ?? $usuarioAtual->cpf_usuario,
                $input['status_usuario'] ?? $usuarioAtual->status_usuario 
            );
            Response::success();
        } catch (\Exception $e) { 
             Response::error($e->getMessage(), 500); 
        }
    }

    public function excluirUsuario($id) {
        try {
            $model = new Usuario($this->db);
            $model->excluirUsuario($id);
            Response::success();
        } catch (\Exception $e) { Response::error($e->getMessage(), 500); }
    }

    // --- AGENDAMENTOS ---
    public function listarAgendamentos() {
        try {
            $model = new Agendamento($this->db);
            Response::success(['data' => $model->buscarAgendamentos()]);
        } catch (\Exception $e) { Response::error($e->getMessage(), 500); }
    }

    public function criarAgendamento() {
        $input = $this->getInput();
        try {
            $model = new Agendamento($this->db);
            $model->inserirAgendamento(
                $input['id_usuario'], 
                $input['id_profissional'], 
                $input['data_agendamento']
            );
            Response::success();
        } catch (\Exception $e) { Response::error($e->getMessage(), 500); }
    }

    public function excluirAgendamento($id) {
        try {
            $model = new Agendamento($this->db);
            $model->deletarAgendamento($id);
            Response::success();
        } catch (\Exception $e) { Response::error($e->getMessage(), 500); }
    }

    // --- PAGAMENTOS ---
    public function listarPagamentos() {
        try {
            $model = new Pagamento($this->db);
            Response::success(['data' => $model->buscarTodosPagamentos()]);
        } catch (\Exception $e) { Response::error($e->getMessage(), 500); }
    }

    public function excluirPagamento($id) {
        try {
            $model = new Pagamento($this->db);
            $model->deletarPagamento($id);
            Response::success();
        } catch (\Exception $e) { Response::error($e->getMessage(), 500); }
    }

    // Auxiliares
    private function getInput() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    // Método erro removido pois agora usamos Response::error
}