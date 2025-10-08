<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AgendamentoValidador;

class AgendamentoController {
    public $agendamento;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->agendamento = new Agendamento($this->db);
    }

    // LISTAR AGENDAMENTOS
    public function viewListarAgendamentos() {
        $dados = $this->agendamento->buscarAgendamentos();
        View::render("agendamento/index",["agendamentos" => $dados]);
    }
    
    // CRIAR AGENDAMENTOS (VIEW)
    public function viewCriarAgendamentos() {
        View::render("agendamento/create");
    }

    // SALVAR AGENDAMENTO (POST)
    public function salvarAgendamentos() {
        $erros = AgendamentoValidador::ValidarEntradas($_POST);
        if(!empty($erros)){ 
            Redirect::redirecionarComMensagem("agendamentos/criar","error",implode("<br>",$erros));
            return;
        }

        $id = $this->agendamento->inserirAgendamento(
            (int)$_POST["id_usuario"],
            (int)$_POST["id_profissional"],
            $_POST["data_agendamento"],
            "Pendente" // Status padrão
        );

        if ($id) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "success", "Agendamento criado com sucesso. ID: $id");
        } else {
            Redirect::redirecionarComMensagem("agendamentos/criar", "error", "Erro ao criar agendamento.");
        }
    }
    
    // EDITAR AGENDAMENTO (VIEW)
    public function viewEditarAgendamentos($id) {
        if (!$id || !is_numeric($id)) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "ID do agendamento inválido.");
            return;
        }
        
        $agendamento = $this->agendamento->buscarAgendamentoPorId((int)$id);

        if (!$agendamento) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Agendamento com ID {$id} não encontrado.");
            return;
        }

        View::render("agendamento/edit", ["agendamento" => $agendamento]);
    } 

    // ATUALIZAR AGENDAMENTO (POST)
    public function atualizarAgendamentos($id) {
    if (!$id || !is_numeric($id)) {
        Redirect::redirecionarComMensagem("agendamentos/listar", "error", "ID do agendamento inválido.");
        return;
    }

    $erros = AgendamentoValidador::ValidarEntradas($_POST);
    if(!empty($erros)){ 
        Redirect::redirecionarComMensagem("agendamentos/editar/{$id}","error",implode("<br>",$erros));
        return;
    }

    // --- A CORREÇÃO É AQUI ---
    // 1. Pega a data do formulário (Ex: "2025-10-08T11:45")
    $data_do_formulario = $_POST['data_agendamento'];
    
    // 2. Converte para o formato que o banco de dados entende (Ex: "2025-10-08 11:45:00")
    $data_formatada_para_db = date('Y-m-d H:i:s', strtotime($data_do_formulario));
    // -------------------------

    $sucesso = $this->agendamento->atualizarAgendamento(
        (int)$id,
        (int)$_POST['id_usuario'],
        (int)$_POST['id_profissional'],
        $data_formatada_para_db // 3. Usa a data já formatada
    );

    if ($sucesso) {
        Redirect::redirecionarComMensagem("agendamentos/listar", "success", "Agendamento atualizado com sucesso!");
    } else {
        Redirect::redirecionarComMensagem("agendamentos/editar/{$id}", "error", "Erro ao atualizar o agendamento.");
    }
}

    // EXCLUIR AGENDAMENTO (VIEW)
    public function viewExcluirAgendamentos($id) {
        if (!$id || !is_numeric($id)) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "ID do agendamento inválido.");
            return;
        }
        
        $agendamento = $this->agendamento->buscarAgendamentoPorId((int)$id);

        if (!$agendamento) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Agendamento com ID {$id} não encontrado.");
            return;
        }

        View::render("agendamento/delete", ["agendamento" => $agendamento]);
    }

    // DELETAR AGENDAMENTO (POST)
    public function deletarAgendamentos($id) {
        if (!$id || !is_numeric($id)) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "ID do agendamento inválido.");
            return;
        }

        $sucesso = $this->agendamento->deletarAgendamento((int)$id);

        if ($sucesso) {
            Redirect::redirecionarComMensagem("agendamentos/listar", "success", "Agendamento excluído com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("agendamentos/listar", "error", "Erro ao excluir o agendamento.");
        }
    }
}