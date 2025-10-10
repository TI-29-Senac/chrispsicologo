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
    
    public function viewListarPagamentos()
    {

        $pagamentos = $this->pagamento->buscarTodosPagamentos();


        $faturamentoTotal = 0;
        $totalPagamentos = count($pagamentos);
        $pagamentosPix = 0;

        foreach ($pagamentos as $pagamento) {

            if (isset($pagamento['valor_consulta'])) {
                $faturamentoTotal += (float)$pagamento['valor_consulta']; 
            }
            
            if (isset($pagamento['tipo_pagamento']) && $pagamento['tipo_pagamento'] === 'pix') {
                $pagamentosPix++;
            }
        }
        
        $valorMedio = ($totalPagamentos > 0) ? $faturamentoTotal / $totalPagamentos : 0;

        // --- ARRAY DE STATS ---
        $stats = [
            [
                'label' => 'Faturamento Total',
                'value' => 'R$ ' . number_format($faturamentoTotal, 2, ',', '.'),
                'icon' => 'fa-money'
            ],
            [
                'label' => 'Total de Transações',
                'value' => $totalPagamentos,
                'icon' => 'fa-credit-card'
            ],
            [
                'label' => 'Valor Médio',
                'value' => 'R$ ' . number_format($valorMedio, 2, ',', '.'),
                'icon' => 'fa-calculator'
            ],
            [
                'label' => 'Pagamentos via Pix',
                'value' => $pagamentosPix,
                'icon' => 'fa-qrcode'
            ]
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