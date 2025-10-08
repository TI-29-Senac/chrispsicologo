<?php
namespace App\Psico\Controllers;
 
use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AvaliacaoValidador;
 
class AvaliacaoController {
    public $avaliacao;  
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->avaliacao = new Avaliacao($this->db);
 
    }
 
    // Listar Avaliações (Index)
    public function index(){
        $this->viewListarAvaliacoes();
    }
   
    public function viewListarAvaliacoes(){
        $dados = $this->avaliacao->buscarAvaliacoes();
        View::render("avaliacao/index",["avaliacoes"=>$dados]);
    }
   
    // Criar Avaliação
    public function viewCriarAvaliacoes(){
        View::render("avaliacao/create");
    }
 
    public function viewEditarAvaliacoes($id) // <-- CORREÇÃO: Recebe o $id da rota
{
    // Não precisamos mais do $_GET
    
    // Validação para garantir que o ID é válido
    if (!$id || !is_numeric($id)) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação inválido.");
        return;
    }

    // Busca a avaliação no banco de dados
    $avaliacao = $this->avaliacao->buscarAvaliacaoPorId((int)$id); 

    if (!$avaliacao) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "Avaliação com ID {$id} não encontrada.");
        return;
    }

    // Se tudo estiver certo, renderiza a view de edição
    View::render("avaliacao/edit", ["avaliacao" => $avaliacao]);
}

// Excluir Avaliação (carrega a página de confirmação de exclusão)
public function viewExcluirAvaliacoes($id) // <-- CORREÇÃO: Recebe o $id da rota
{
    // Não precisamos mais do $_GET

    if (!$id || !is_numeric($id)) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação inválido.");
        return;
    }

    $avaliacao = $this->avaliacao->buscarAvaliacaoPorId((int)$id); 

    if (!$avaliacao) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "Avaliação com ID {$id} não encontrada.");
        return;
    }

    View::render("avaliacao/delete", ["avaliacao" => $avaliacao]);
}
 
    // Salvar Avaliação (POST)
public function salvarAvaliacoes()
{
    $erros = AvaliacaoValidador::ValidarEntradas($_POST);
 
    if (!empty($erros)) {
        Redirect::redirecionarComMensagem("avaliacoes/criar", "error", implode("<br>", $erros));
        return;
    }
 
    $id_cliente = $_POST['id_cliente'] ?? null;
    $id_profissional = $_POST['id_profissional'] ?? null;
    $nota_avaliacao = $_POST['nota_avaliacao'] ?? 1;
    $descricao_avaliacao = $_POST['descricao_avaliacao'] ?? '';
 
    $id = $this->avaliacao->inserirAvaliacao(
        (int)$id_cliente,
        (int)$id_profissional,
        $descricao_avaliacao,
        (int)$nota_avaliacao
    );
 
    if ($id) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação registrada com sucesso! ID: $id");
    } else {
        Redirect::redirecionarComMensagem("avaliacoes/criar", "error", "Ocorreu um erro ao registrar a avaliação no banco de dados.");
    }
}
    // ... outros métodos ...

    // Em App/Psico/Controllers/AvaliacaoController.php

// AvaliacaoController.php

public function atualizarAvaliacoes($id)
{
    if (!$id) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação não foi recebido pela rota.");
        return;
    }

    $erros = AvaliacaoValidador::ValidarEntradas($_POST);
    if (!empty($erros)) {
        // CORREÇÃO na URL de redirecionamento de erro
        Redirect::redirecionarComMensagem("avaliacoes/editar/{$id}", "error", implode("<br>", $erros));
        return;
    }

    // Pega apenas os dados necessários
    $descricao_avaliacao = $_POST['descricao_avaliacao'] ?? '';
    $nota_avaliacao = (int)($_POST['nota_avaliacao'] ?? 1);

    // CORREÇÃO AQUI: Chame o método com os 3 parâmetros corretos que o Model espera
    $sucesso = $this->avaliacao->atualizarAvaliacao(
        (int)$id,
        $descricao_avaliacao,
        $nota_avaliacao
    );

    if ($sucesso) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação atualizada com sucesso!");
    } else {
        // CORREÇÃO na URL de redirecionamento de erro
        Redirect::redirecionarComMensagem("avaliacoes/editar/{$id}", "error", "Erro ao atualizar a avaliação no banco de dados.");
    }
}
 
// Deletar Avaliação (POST)
public function deletarAvaliacoes()
{
    $id = $_POST['id'] ?? null;
 
    if (!$id) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação não informado.");
        return;
    }
 
    $sucesso = $this->avaliacao->deletarAvaliacao((int)$id);
 
    if ($sucesso) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação excluída com sucesso!");
    } else {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "Erro ao excluir a avaliação.");
    }
}
    /**
     * API para buscar avaliações por ID de profissional via GET /backend/avaliacoes?id=X
     */
    public function buscarPorProfissional() {
        $id = $_GET['id'] ?? null;
       
        header('Content-Type: application/json');
       
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(["error" => "ID do profissional inválido ou não fornecido."]);
            return;
        }
 
        // Chama o método do Model para buscar as avaliações
        $avaliacoes = $this->avaliacao->buscarAvaliacoesPorProfissional((int)$id);
       
        http_response_code(200);
        echo json_encode($avaliacoes);
        return;
    }
}