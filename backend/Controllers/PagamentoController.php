<?php
namespace App\Psico\Controllers;

use App\Psico\Controllers\Admin\AuthenticatedController;
use App\Psico\Models\Pagamento;
use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\PagamentoValidador;
use Piggly\Pix\Exceptions\InvalidPixKeyException;
use Piggly\Pix\StaticPayload;

class PagamentoController extends AuthenticatedController{
    public $pagamento;   
    public $db;
    public $agendamento;
    public function __construct(){
        parent::__construct();
        $this->db = Database::getInstance();
        $this->pagamento = new Pagamento($this->db);
        $this->agendamento = new Agendamento($this->db);
    }

    public function index(){
        $this->viewListarPagamentos();
    }
    
    public function viewListarPagamentos() {
        $this->verificarAcesso(['admin', 'recepcionista']);
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
        $this->verificarAcesso(['admin', 'recepcionista']);
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
        $this->verificarAcesso(['admin', 'recepcionista']);
        $pagamento = $this->pagamento->buscarPagamentoPorId((int)$id);
            if (!$pagamento) {
                Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Pagamento não encontrado.");
                return;
            }
        View::render("pagamento/edit", ["pagamento" => $pagamento]);
    }

    public function atualizarPagamento($id){
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
        $this->verificarAcesso(['admin']);
        $pagamento = $this->pagamento->buscarPagamentoPorId((int)$id);
        if (!$pagamento) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Pagamento não encontrado.");
            return;
        }
        View::render("pagamento/delete", ["pagamento" => $pagamento]);
    }

    public function deletarPagamento($id){
        $sucesso = $this->pagamento->deletarPagamento((int)$id);

        if ($sucesso) {
            Redirect::redirecionarComMensagem("pagamentos/listar", "success", "Pagamento excluído com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("pagamentos/listar", "error", "Erro ao excluir pagamento.");
        }
    }

    public function gerarPix()
    {
        // Pega os dados enviados pelo aplicativo desktop (ex: valor)
        $input = json_decode(file_get_contents('php://input'), true);
        $valor = $input['valor'] ?? 1.00; // Pega o valor ou usa 1.00 como padrão

        try {
            // --- CONFIGURAÇÃO DO PIX ---
            // Substitua pelos seus dados reais.
            // A chave PIX pode ser CPF, CNPJ, E-mail, Telefone ou Chave Aleatória.
            $chavePix      = 'seu-email@provedor.com'; // <-- IMPORTANTE: TROQUE PELA SUA CHAVE PIX
            $nomeRecebedor = 'Chris Psicologia';       // Nome que aparecerá para quem paga
            $cidade        = 'SAO PAULO';              // Cidade do recebedor
            $txid          = 'AGENDAMENTO123';         // Um identificador único para a transação

            // Cria a estrutura do payload do PIX - Agora com a classe importada
            $payload = (new StaticPayload())
                ->setAmount($valor)
                ->setPixKey('email', $chavePix)
                ->setDescription('Pagamento de consulta')
                ->setMerchantName($nomeRecebedor)
                ->setMerchantCity($cidade)
                ;

            // Gera o código "copia e cola"
            $copiaECola = $payload->getPixCode();

            // Gera a imagem do QR Code em formato Base64
            // CORREÇÃO: O método getQRCode() já retorna a imagem em Base64 por padrão.
            $qrCodeBase64 = $payload->getQRCode();

            // Envia a resposta em formato JSON para o aplicativo desktop
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'copiaECola' => $copiaECola,
                'qrCodeBase64' => $qrCodeBase64
            ]);

        } catch (InvalidPixKeyException $e) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'message' => 'Chave PIX configurada é inválida.']);
        } catch (\Exception $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['success' => false, 'message' => 'Erro ao gerar o PIX: ' . $e->getMessage()]);
        }
    }

    public function listarFinanceiroCliente() {
        try {
            $payload = \App\Psico\Core\Auth::check();
            $idCliente = $payload->sub;
            $pagamentos = $this->pagamento->buscarPagamentosPorCliente((int)$idCliente);
            \App\Psico\Core\Response::success(['data' => $pagamentos]);
        } catch (\Exception $e) {
            \App\Psico\Core\Response::error($e->getMessage(), 500);
        }
    }
}