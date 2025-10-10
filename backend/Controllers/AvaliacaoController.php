<?php
namespace App\Psico\Controllers;
 
use App\Psico\Core\View;
use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AvaliacaoValidador;
 
class AvaliacaoController
{
    public $avaliacao;
    public $db;
 
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->avaliacao = new Avaliacao($this->db);
    }
 
    public function viewListarAvaliacoes()
    {
        $avaliacoes = $this->avaliacao->buscarAvaliacoes();
        $totalAvaliacoes = 0;
        $somaNotas = 0;
        $avaliacoes5Estrelas = 0;
 
        foreach ($avaliacoes as $avaliacao) {
            $totalAvaliacoes++;
            $somaNotas += $avaliacao['nota_avaliacao'];
            if ($avaliacao['nota_avaliacao'] == 5) {
                $avaliacoes5Estrelas++;
            }
        }
       
        $notaMedia = ($totalAvaliacoes > 0) ? round($somaNotas / $totalAvaliacoes, 1) : 0;
 
        $stats = [
            [
                'label' => 'Total de Avaliações',
                'value' => $totalAvaliacoes,
                'icon' => 'fa-comments-o'
            ],
            [
                'label' => 'Nota Média',
                'value' => $notaMedia . ' / 5',
                'icon' => 'fa-star-half-o'
            ],
            [
                'label' => 'Avaliações 5 Estrelas',
                'value' => $avaliacoes5Estrelas,
                'icon' => 'fa-star'
            ],
            [
                'label' => 'Avaliações a Melhorar',
                'value' => $totalAvaliacoes - $avaliacoes5Estrelas,
                'icon' => 'fa-thumbs-o-down'
            ]
        ];
 
        View::render("avaliacao/index", [
            "avaliacoes" => $avaliacoes,
            "stats" => $stats
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
    
    public function atualizarAvaliacao()
    {
        $id = $_POST['id_avaliacao'] ?? null;
    
        if (!$id) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação não informado.");
            return;
        }
    
        $erros = AvaliacaoValidador::ValidarEntradas($_POST);
        if (!empty($erros)) {
            Redirect::redirecionarComMensagem("avaliacoes/editar?id={$id}", "error", implode("<br>", $erros));
            return;
        }
    
        $sucesso = $this->avaliacao->atualizarAvaliacao(
            (int)$id,
            $_POST['descricao_avaliacao'] ?? '',
            (int)($_POST['nota_avaliacao'] ?? 1)
        );
    
        if ($sucesso) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação atualizada com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("avaliacoes/editar?id={$id}", "error", "Erro ao atualizar a avaliação.");
        }
    }
    
    public function deletarAvaliacoes()
    {
        $id = $_POST['id_avaliacao'] ?? null;
    
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