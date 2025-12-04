<?php
namespace App\Psico\Controllers;

use App\Psico\Database\Database;
use App\Psico\Models\Agendamento;
use App\Psico\Models\Pagamento;
use App\Psico\Models\Usuario;
use App\Psico\Models\Profissional;

class DesktopApiController {
    private $db;

    public function __construct() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Content-Type: application/json; charset=UTF-8");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        $this->db = Database::getInstance();
    }

    // --- USUÁRIOS ---
    public function listarUsuarios() {
        try {
            $model = new Usuario($this->db);
            $dados = $model->buscarTodosUsuarios();
            // Remove senhas do retorno por segurança
            foreach($dados as $k => $v) unset($dados[$k]->senha_usuario);
            echo json_encode(['success' => true, 'data' => $dados]);
        } catch (\Exception $e) { $this->erro($e); }
    }

    public function criarUsuario() {
        $input = $this->getInput();
        try {
            $model = new Usuario($this->db);
            if ($model->buscarUsuarioPorEmail($input['email_usuario'])) {
                $this->erro('Email já cadastrado'); return;
            }
            $model->inserirUsuario(
                $input['nome_usuario'], $input['email_usuario'], 
                $input['senha_usuario'] ?? '123456', 
                $input['tipo_usuario'] ?? 'cliente', 
                $input['cpf'] ?? ''
            );
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { $this->erro($e); }
    }

    public function editarUsuario($id) {
        $input = $this->getInput();
        try {
            $model = new Usuario($this->db);
            $usuarioAtual = $model->buscarUsuarioPorId($id);
            
            if (!$usuarioAtual) {
                $this->erro("Usuário não encontrado.");
                return;
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
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { 
            $this->erro($e); 
        }
    }

    public function excluirUsuario($id) {
        try {
            $model = new Usuario($this->db);
            $model->excluirUsuario($id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { $this->erro($e); }
    }

    // --- AGENDAMENTOS ---
    public function listarAgendamentos() {
        try {
            $model = new Agendamento($this->db);
            echo json_encode(['success' => true, 'data' => $model->buscarAgendamentos()]);
        } catch (\Exception $e) { $this->erro($e); }
    }

    public function criarAgendamento() {
        $input = $this->getInput();
        try {
            $model = new Agendamento($this->db);
            // Formato esperado: YYYY-MM-DD HH:MM:SS
            $model->inserirAgendamento(
                $input['id_usuario'], 
                $input['id_profissional'], 
                $input['data_agendamento']
            );
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { $this->erro($e); }
    }

    public function excluirAgendamento($id) {
        try {
            $model = new Agendamento($this->db);
            $model->deletarAgendamento($id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { $this->erro($e); }
    }

    // --- PAGAMENTOS ---
    public function listarPagamentos() {
        try {
            $model = new Pagamento($this->db);
            echo json_encode(['success' => true, 'data' => $model->buscarTodosPagamentos()]);
        } catch (\Exception $e) { $this->erro($e); }
    }

    public function excluirPagamento($id) {
        try {
            $model = new Pagamento($this->db);
            $model->deletarPagamento($id);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) { $this->erro($e); }
    }

    // Auxiliares
    private function getInput() {
        return json_decode(file_get_contents('php://input'), true);
    }

    private function erro($e) {
        $msg = is_string($e) ? $e : $e->getMessage();
        echo json_encode(['success' => false, 'error' => $msg]);
    }
}