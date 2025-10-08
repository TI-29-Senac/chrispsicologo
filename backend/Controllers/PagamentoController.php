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

    public function index(){
        $this->viewListarPagamentos();
    }
    
    public function viewListarPagamentos(){
        $dados = $this->pagamento->buscarPagamentos();
        View::render("pagamento/index",["pagamentos"=>$dados]);
    }


    public function viewCriarPagamentos(){
        View::render("pagamento/create");
    }


    public function viewEditarPagamentos(){
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "ID do pagamento não informado.");
            return;
        }

        $pagamento = $this->pagamento->buscarPagamentoPorId((int)$id); 

        if (!$pagamento) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Pagamento não encontrado.");
            return;
        }

        View::render("pagamento/edit", ["pagamento" => $pagamento]);
    }


    public function viewExcluirPagamentos(){
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "ID do pagamento não informado.");
            return;
        }
        $pagamento = $this->pagamento->buscarPagamentoPorId((int)$id); 

        if (!$pagamento) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Pagamento não encontrado.");
            return;
        }

        View::render("pagamento/delete", ["pagamento" => $pagamento]);
    }

// NOVO MÉTODO PARA EXIBIR O FORMULÁRIO DE EXCLUSÃO MANUAL POR ID
public function viewExcluirManual() {
    View::render("pagamento/excluir_manual");
}


public function salvarPagamentos() {
    $id_agendamento = $_POST['id_agendamento'] ?? null;
    $valor_consulta = $_POST['valor_consulta'] ?? '0';
    $sinal_consulta = $_POST['sinal_consulta'] ?? '0';
    $tipo_pagamento = $_POST['tipo_pagamento'] ?? 'pix';


    $valor_consulta = str_replace('.', '', $valor_consulta); 
    $valor_consulta = str_replace(',', '.', $valor_consulta);
    $valor_consulta = (float)$valor_consulta; 

    // CORREÇÃO: Tratar vírgula como decimal e converter para float (DECIMAL)
    $sinal_consulta = str_replace('.', '', $sinal_consulta); // Remove separador de milhar
    $sinal_consulta = str_replace(',', '.', $sinal_consulta); // Converte vírgula decimal para ponto
    $sinal_consulta = (float)$sinal_consulta; // Converte para float (DECIMAL)

    $id = $this->pagamento->inserirPagamento(
        (int)$id_agendamento,
        $valor_consulta,
        $sinal_consulta,
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

        $id = $_POST['id_pagamento_manual'] ?? $_POST['id_pagamento'] ?? null; 

        if (!$id) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "ID do pagamento não informado para exclusão.");
            return;
        }
        

        $rowCount = $this->pagamento->deletarPagamento((int)$id); 

        if ($rowCount > 0) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "success", "Pagamento ID: $id excluído com sucesso.");
        } else {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Erro ao excluir pagamento ID: $id. Ele pode não existir.");
        }
    }
}