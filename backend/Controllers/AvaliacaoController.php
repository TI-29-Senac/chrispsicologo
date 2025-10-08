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
    public function salvarAvaliacoes(){
        $erros = AvaliacaoValidador::ValidarEntradas($_POST);
        if(!empty($erros)){ 
            Redirect::redirecionarComMensagem("avaliacoes/criar","error",implode("<br>",$erros));
        $id_cliente = $_POST['id_cliente'] ?? null;
        $id_profissional = $_POST['id_profissional'] ?? null;
        $nota_avaliacao = $_POST['nota_avaliacao'] ?? 1;
        $descricao_avaliacao = $_POST['descricao_avaliacao'] ?? '';

        // Aqui você adicionaria a validação

        $id = $this->avaliacao->inserirAvaliacao(
            (int)$id_cliente,
            (int)$id_profissional,
            $descricao_avaliacao,
            (int)$nota_avaliacao
        );

        if ($id) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "success", "Avaliação registrada com sucesso! ID: $id");
        } else {
            Redirect::redirecionarComMensagem("avaliacoes/criar", "error", "Erro ao registrar avaliação.");
        }
        }   
    }
    // Atualizar Avaliação (POST)
    public function atualizarAvaliacoes(){
        // Implementação similar ao salvar, mas chamando Avaliacao->atualizarAvaliacao()
        // ...
        echo "Atualizar Avaliações";
    }

    // Deletar Avaliação (POST)
    public function deletarAvaliacoes(){
        // Implementação similar ao salvar, mas chamando Avaliacao->deletarAvaliacao()
        // ...
        echo "Deletar Avaliações";
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