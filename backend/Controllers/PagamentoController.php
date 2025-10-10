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
    
    // Em backend/Controllers/PagamentoController.php

// Em backend/Controllers/PagamentoController.php

public function viewListarPagamentos()
{
    $pagamentos = $this->pagamento->buscarPagamentos();

    $faturamentoTotal = 0;
    $pagos = 0;
    $pendentes = 0;

    foreach ($pagamentos as $pagamento) {
        if ($pagamento['status_pagamento'] === 'pago') {
            $faturamentoTotal += (float)$pagamento['valor_pagamento'];
            $pagos++;
        } elseif ($pagamento['status_pagamento'] === 'pendente') {
            $pendentes++;
        }
    }
    $ticketMedio = $pagos > 0 ? $faturamentoTotal / $pagos : 0;

    $stats = [
        ['titulo' => 'Faturamento Total', 'valor' => 'R$ ' . number_format($faturamentoTotal, 2, ',', '.'), 'icone' => 'fa-money', 'cor' => '#5D6D68'],
        ['titulo' => 'Ticket Médio', 'valor' => 'R$ ' . number_format($ticketMedio, 2, ',', '.'), 'icone' => 'fa-line-chart', 'cor' => '#7C8F88'],
        ['titulo' => 'Pagamentos Concluídos', 'valor' => $pagos, 'icone' => 'fa-check-square-o', 'cor' => '#A3B8A1'],
        ['titulo' => 'Pagamentos Pendentes', 'valor' => $pendentes, 'icone' => 'fa-hourglass-start', 'cor' => '#C5A8A8'],
    ];

    View::render("pagamento/index", [
        "pagamentos" => $pagamentos,
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