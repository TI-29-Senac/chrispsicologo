<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Pagamento;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\PagamentoValidador;

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
    
    // Em backend/Controllers/PagamentoController.php

// Em backend/Controllers/PagamentoController.php

public function viewListarPagamentos() {
        // --- LÓGICA DE PAGINAÇÃO ---
        $pagina = $_GET['pagina'] ?? 1;
        $dadosPaginados = $this->pagamento->paginacao((int)$pagina, 10);

        // --- LÓGICA PARA OS CARDS (CORRIGIDA) ---
        $todosPagamentos = $this->pagamento->buscarTodosPagamentos();
        
        $faturamentoTotal = 0;
        $totalTransacoes = count($todosPagamentos);
        $pagamentosPix = 0;

        foreach ($todosPagamentos as $pagamento) {
            // O faturamento agora é baseado no valor da consulta do profissional
            $faturamentoTotal += (float)($pagamento['valor_consulta'] ?? 0);
            
            if (isset($pagamento['tipo_pagamento']) && strtolower($pagamento['tipo_pagamento']) === 'pix') {
                $pagamentosPix++;
            }
        }
        
        $valorMedio = ($totalTransacoes > 0) ? $faturamentoTotal / $totalTransacoes : 0;

        // --- ARRAY DE STATS ATUALIZADO ---
        $stats = [
            [
                'label' => 'Faturamento Total',
                'value' => 'R$ ' . number_format($faturamentoTotal, 2, ',', '.'),
                'icon' => 'fa-money'
            ],
            [
                'label' => 'Total de Transações',
                'value' => $totalTransacoes,
                'icon' => 'fa-credit-card'
            ],
            [
                'label' => 'Pagamentos via Pix',
                'value' => $pagamentosPix,
                'icon' => 'fa-qrcode'
            ],
            [
                'label' => 'Valor Médio',
                'value' => 'R$ ' . number_format($valorMedio, 2, ',', '.'),
                'icon' => 'fa-calculator'
            ]
        ];

        // --- RENDERIZA A VIEW COM OS DADOS PAGINADOS ---
        View::render("pagamento/index", [
            "pagamentos" => $dadosPaginados['data'],
            "paginacao" => $dadosPaginados,
            "stats" => $stats
        ]);
    }

    public function viewCriarPagamentos(){

        View::render("pagamento/create");
    }

    public function salvarPagamentos() {
        $id_agendamento = $_POST['id_agendamento'] ?? null;
        $tipo_pagamento = $_POST['tipo_pagamento'] ?? 'pix';

        $id = $this->pagamento->inserirPagamento(
            (int)$id_agendamento,
            0,
            0, 
            $tipo_pagamento
        );

        if ($id) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "success", "Pagamento criado com sucesso! ID: $id");
        } else {
            Redirect::redirecionarComMensagem("pagamentos/criar", "error", "Erro ao criar pagamento.");
        }
    }
    

    public function viewEditarPagamentos($id){
        echo "Funcionalidade de Editar Pagamento a ser implementada.";
    }

    public function viewExcluirPagamentos($id){
        echo "Funcionalidade de Excluir Pagamento a ser implementada.";
    }
}   