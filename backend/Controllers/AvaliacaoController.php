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

    public function index(){
        $this->viewListarAvaliacoes();
    }
   
    // Em backend/Controllers/AvaliacaoController.php

// Em backend/Controllers/AvaliacaoController.php

public function viewListarAvaliacoes(){
    $avaliacoes = $this->avaliacao->buscarAvaliacoes();
    
    // Calcula as estatísticas
    $total = count($avaliacoes);
    $somaNotas = 0;
    $notas5 = 0;
    $notasBaixas = 0; // Notas 1 ou 2

    foreach ($avaliacoes as $avaliacao) {
        $nota = (int)$avaliacao['nota_avaliacao'];
        $somaNotas += $nota;
        if ($nota === 5) {
            $notas5++;
        }
        if ($nota <= 2) {
            $notasBaixas++;
        }
    }
    $mediaNotas = ($total > 0) ? number_format($somaNotas / $total, 1) : 0;

    // Define as 4 estatísticas para a página de avaliações
    $stats = [
        ['titulo' => 'Total de Avaliações', 'valor' => $total, 'icone' => 'fa-star', 'cor' => '#5D6D68'],
        ['titulo' => 'Média Geral', 'valor' => $mediaNotas . ' / 5.0', 'icone' => 'fa-star-half-o', 'cor' => '#7C8F88'],
        ['titulo' => 'Avaliações 5 Estrelas', 'valor' => $notas5, 'icone' => 'fa-thumbs-up', 'cor' => '#A3B8A1'],
        ['titulo' => 'Avaliações Baixas', 'valor' => $notasBaixas, 'icone' => 'fa-thumbs-down', 'cor' => '#C5A8A8'],
    ];

    View::render("avaliacao/index", [
        "avaliacoes" => $avaliacoes,
        "stats" => $stats // Envia as estatísticas para a view
    ]);
}
   
    // ... (restante dos métodos create, store, edit, update, delete, buscarPorProfissional)
    // Criar Avaliação
    public function viewCriarAvaliacoes(){
        View::render("avaliacao/create");
    }
 
    // Editar Avaliação
    public function viewEditarAvaliacoes(){
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação não informado.");
            return;
        }
        // Nota: Assumindo que você criará um método buscarAvaliacaoPorId($id) no seu Model de Avaliacao
        $avaliacao = $this->avaliacao->buscarAvaliacaoPorId((int)$id);
 
        if (!$avaliacao) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "Avaliação não encontrada.");
            return;
        }
 
        View::render("avaliacao/edit", ["avaliacao" => $avaliacao]);
    }
 
    // Excluir Avaliação
    public function viewExcluirAvaliacoes(){
        $id = $_GET['id'] ?? null;
        if (!$id) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação não informado.");
            return;
        }
        // Nota: Assumindo que você criará um método buscarAvaliacaoPorId($id) no seu Model de Avaliacao
        $avaliacao = $this->avaliacao->buscarAvaliacaoPorId((int)$id);
 
        if (!$avaliacao) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "Avaliação não encontrada.");
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
 
public function atualizarAvaliacoes()
{
    $id = $_POST['id'] ?? null;
   
    if (!$id) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação não informado.");
        return;
    }
 
    $erros = AvaliacaoValidador::ValidarEntradas($_POST);
    if (!empty($erros)) {
        Redirect::redirecionarComMensagem("avaliacoes/editar?id={$id}", "error", implode("<br>", $erros));
        return;
    }
 
    $dados = [
        'id_cliente' => (int)($_POST['id_cliente'] ?? null),
        'id_profissional' => (int)($_POST['id_profissional'] ?? null),
        'descricao_avaliacao' => $_POST['descricao_avaliacao'] ?? '',
        'nota_avaliacao' => (int)($_POST['nota_avaliacao'] ?? 1)
    ];
 
    $sucesso = $this->avaliacao->atualizarAvaliacao(
        (int)$id,
        $dados['id_cliente'],
        $dados['id_profissional'],
        $dados['descricao_avaliacao'],
        $dados['nota_avaliacao']
    );
 
    if ($sucesso) {
        Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação atualizada com sucesso!");
    } else {
        Redirect::redirecionarComMensagem("avaliacoes/editar?id={$id}", "error", "Erro ao atualizar a avaliação.");
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
 
        $avaliacoes = $this->avaliacao->buscarAvaliacoesPorProfissional((int)$id);
       
        http_response_code(200);
        echo json_encode($avaliacoes);
        return;
    }
}