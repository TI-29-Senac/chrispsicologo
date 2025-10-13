<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Pagamento;
use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\PagamentoValidador;

class PagamentoController {
    public $pagamento;   
    public $db;
    public $agendamento;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->pagamento = new Pagamento($this->db);
        $this->agendamento = new Agendamento($this->db);
    }

    public function index(){
        $this->viewListarPagamentos();
    }
    
    public function viewListarPagamentos() {
        $pagina = $_GET['pagina'] ?? 1;
        $dadosPaginados = $this->pagamento->paginacao((int)$pagina, 10);

        $todosPagamentos = $this->pagamento->buscarTodosPagamentos();
        
        $faturamentoTotal = 0;
        $totalTransacoes = count($todosPagamentos);
        $pagamentosPix = 0;

        foreach ($todosPagamentos as $pagamento) {
            $faturamentoTotal += (float)($pagamento['valor_consulta'] ?? 0);
            
            if (isset($pagamento['tipo_pagamento']) && strtolower($pagamento['tipo_pagamento']) === 'pix') {
                $pagamentosPix++;
            }
        }
        
        $valorMedio = ($totalTransacoes > 0) ? $faturamentoTotal / $totalTransacoes : 0;

        $stats = [
            ['label' => 'Faturamento Total', 'value' => 'R$ ' . number_format($faturamentoTotal, 2, ',', '.'), 'icon' => 'fa-money'],
            ['label' => 'Total de Transações', 'value' => $totalTransacoes, 'icon' => 'fa-credit-card'],
            ['label' => 'Pagamentos via Pix', 'value' => $pagamentosPix, 'icon' => 'fa-qrcode'],
            ['label' => 'Valor Médio', 'value' => 'R$ ' . number_format($valorMedio, 2, ',', '.'), 'icon' => 'fa-calculator']
        ];

        View::render("pagamento/index", [
            "pagamentos" => $dadosPaginados['data'],
            "paginacao" => $dadosPaginados,
            "stats" => $stats
        ]);
    }

    public function viewCriarPagamentos(){
        $agendamentos = $this->agendamento->buscarTodosAgendamentos();
        View::render("pagamento/create", ["agendamentos" => $agendamentos]);
    }

public function salvarPagamentos() {
        $erros = PagamentoValidador::ValidarEntradas($_POST);
        if (!empty($erros)) {
            Redirect::redirecionarComMensagem("pagamentos/criar", "error", implode("<br>", $erros));
            return;
        }

        $id_agendamento = $_POST['id_agendamento'] ?? null;
        $tipo_pagamento = $_POST['tipo_pagamento'] ?? 'pix';

        $id = $this->pagamento->inserirPagamento(
            (int)$id_agendamento,
            $tipo_pagamento
        );

        if ($id) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "success", "Pagamento criado com sucesso! ID: $id");
        } else {
            Redirect::redirecionarComMensagem("pagamentos/criar", "error", "Erro ao criar pagamento.");
        }
    }
    
public function viewEditarPagamentos($id){
        $pagamento = $this->pagamento->buscarPagamentoPorId((int)$id);
        if (!$pagamento) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Pagamento não encontrado.");
            return;
        }
        View::render("pagamento/edit", ["pagamento" => $pagamento]);
    }

    public function atualizarPagamento($id)
    {
        $sucesso = $this->pagamento->atualizarPagamento(
            (int)$id,
            $_POST['tipo_pagamento'] ?? 'pix',
            (float)($_POST['valor_consulta'] ?? 0)
        );

        if ($sucesso) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "success", "Pagamento atualizado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("pagamentos/editar/{id}", "error", "Erro ao atualizar pagamento.");
        }
    }

    public function viewExcluirPagamentos($id){
        $pagamento = $this->pagamento->buscarPagamentoPorId((int)$id);
        if (!$pagamento) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Pagamento não encontrado.");
            return;
        }
        View::render("pagamento/delete", ["pagamento" => $pagamento]);
    }

    public function deletarPagamento($id)
    {
        $sucesso = $this->pagamento->deletarPagamento((int)$id);

        if ($sucesso) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "success", "Pagamento excluído com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Erro ao excluir pagamento.");
        }
    }
}