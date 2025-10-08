<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AvaliacaoValidador;
use App\Psico\Core\Flash;

class AvaliacaoController {
    public $avaliacao;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->avaliacao = new Avaliacao($this->db);
    }
    
    public function viewCriarAvaliacoes(){
        View::render("avaliacao/create");
    }

    public function viewListarAvaliacoes(){
        $dados = $this->avaliacao->buscarAvaliacoes();
        View::render("avaliacao/index",["avaliacoes"=>$dados]);
    }
    
    // GET: Exibir formulário de edição
    public function viewEditarAvaliacoes($id){
        $avaliacao = $this->avaliacao->buscarAvaliacaoPorId((int)$id); 
        if (!$avaliacao) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "Avaliação não encontrada.");
            return;
        }
        View::render("avaliacao/edit", ["avaliacao" => $avaliacao]);
    }

    // GET: Exibir formulário de confirmação de exclusão
    public function viewExcluirAvaliacoes($id){
        $avaliacao = $this->avaliacao->buscarAvaliacaoPorId((int)$id); 
        if (!$avaliacao) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "Avaliação não encontrada.");
            return;
        }
        View::render("avaliacao/delete", ["avaliacao" => $avaliacao]);
    }
    
    // POST: Salvar nova avaliação
    public function salvarAvaliacoes() {
        $dados = $_POST;
        // Assume-se que existe um AvaliacaoValidador::ValidarEntradas($dados)
        
        $id = $this->avaliacao->inserirAvaliacao(
            (int)$dados['id_usuario'],
            (int)$dados['id_profissional'],
            (int)$dados['estrelas'],
            $dados['comentario']
        );

        if ($id) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação criada com sucesso! ID: $id");
        } else {
            Redirect::redirecionarComMensagem("avaliacoes/criar", "error", "Erro ao criar avaliação.");
        }
    }

    // POST: Atualizar avaliação
    public function atualizarAvaliacoes($id){
        $dados = $_POST;
        // Assume-se que existe um AvaliacaoValidador::ValidarEntradas($dados)

        $sucesso = $this->avaliacao->atualizarAvaliacao(
            (int)$id,
            (int)$dados['id_usuario'],
            (int)$dados['id_profissional'],
            (int)$dados['estrelas'],
            $dados['comentario']
        );

        if ($sucesso) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação ID: $id atualizada com sucesso.");
        } else {
            Redirect::redirecionarComMensagem("avaliacoes/editar/{$id}", "error", "Erro ao atualizar avaliação.");
        }
    }

    // POST: Deletar avaliação
    public function deletarAvaliacoes($id){
        $rowCount = $this->avaliacao->deletarAvaliacao((int)$id); 

        if ($rowCount > 0) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação ID: $id excluída com sucesso.");
        } else {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "Erro ao excluir avaliação ID: $id. Ela pode não existir.");
        }
    }
}