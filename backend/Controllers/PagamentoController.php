<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Pagamento;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;

class PagamentoController {
    public $pagamento;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->pagamento = new Pagamento($this->db);

    }
    // Listar Pagamentos (Index)
    public function index(){
        $this->viewListarPagamentos();
    }
    
    public function viewListarPagamentos(){
        $dados = $this->pagamento->buscarPagamentos();
        var_dump($dados);
        exit;
        View::render("pagamento/index",["pagamentos"=>$dados]);
    }

    // Criar Pagamento
    public function viewCriarPagamentos(){
        View::render("pagamento/create");
    }

    // Editar Pagamento
    public function viewEditarPagamentos(){
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "ID do pagamento não informado.");
            return;
        }
        // Nota: Assumindo que você criará um método buscarPagamentoPorId($id) no seu Model de Pagamento
        $pagamento = $this->pagamento->buscarPagamentoPorId((int)$id); 

        if (!$pagamento) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Pagamento não encontrado.");
            return;
        }

        View::render("pagamento/edit", ["pagamento" => $pagamento]);
    }

    // Excluir Pagamento
    public function viewExcluirPagamentos(){
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "ID do pagamento não informado.");
            return;
        }
        // Nota: Assumindo que você criará um método buscarPagamentoPorId($id) no seu Model de Pagamento
        $pagamento = $this->pagamento->buscarPagamentoPorId((int)$id); 

        if (!$pagamento) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Pagamento não encontrado.");
            return;
        }

        View::render("pagamento/delete", ["pagamento" => $pagamento]);
    }

    // Salvar Pagamento (POST)
    public function salvarPagamentos(){
        $id_agendamento = $_POST['id_agendamento'] ?? null;
        $valor_consulta = $_POST['valor_consulta'] ?? 0.0;
        $sinal_consulta = $_POST['sinal_consulta'] ?? 0.0;
        $tipo_pagamento = $_POST['tipo_pagamento'] ?? 'pix';

        // Aqui você adicionaria a validação

        $id = $this->pagamento->inserirPagamento(
            (int)$id_agendamento,
            (float)$valor_consulta,
            (float)$sinal_consulta,
            $tipo_pagamento
        );

        if ($id) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "success", "Pagamento criado com sucesso! ID: $id");
        } else {
            Redirect::redirecionarComMensagem("pagamentos/criar", "error", "Erro ao criar pagamento.");
        }
    }

    // Atualizar Pagamento (POST)
    public function atualizarPagamentos(){
        // Implementação similar ao salvar, mas chamando Pagamento->atualizarPagamento()
        // ...
        echo "Atualizar Pagamentos";
    }

    // Deletar Pagamento (POST)
    public function deletarPagamentos(){
        // Implementação similar ao salvar, mas chamando Pagamento->deletarPagamento()
        // ...
        echo "Deletar Pagamentos";
    }
}