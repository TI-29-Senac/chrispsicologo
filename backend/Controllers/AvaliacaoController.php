<?php
namespace App\Psico\Controllers;

use App\Psico\Controllers\Admin\AdminController;
use App\Psico\Core\View;
use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\AvaliacaoValidador;
 
class AvaliacaoController extends AdminController{
    public $avaliacao;
    public $agendamento; // Inject Agendamento Model
    public $db;
 
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->avaliacao = new Avaliacao($this->db);
        $this->agendamento = new \App\Psico\Models\Agendamento($this->db);
    }
 
    public function viewListarAvaliacoes()
    {
        $pagina = $_GET['pagina'] ?? 1;

        $filtros = [
            'nome_cliente' => $_GET['cliente'] ?? null,
            'nome_profissional' => $_GET['profissional'] ?? null,
            'nota_avaliacao' => $_GET['nota'] ?? null,
        ];

        $dadosPaginados = $this->avaliacao->buscarComFiltros($filtros, (int)$pagina, 10);

        $todasAvaliacoes = $this->avaliacao->buscarAvaliacoes();
        $totalAvaliacoes = count($todasAvaliacoes);
        $somaNotas = 0;
        $avaliacoes5Estrelas = 0;

        foreach ($todasAvaliacoes as $avaliacao) {
            $somaNotas += $avaliacao['nota_avaliacao'];
            if ($avaliacao['nota_avaliacao'] == 5) {
                $avaliacoes5Estrelas++;
            }
        }
        
        $notaMedia = ($totalAvaliacoes > 0) ? round($somaNotas / $totalAvaliacoes, 1) : 0;

        $stats = [
            ['label' => 'Total de Avaliações', 'value' => $totalAvaliacoes, 'icon' => 'fa-comments-o'],
            ['label' => 'Nota Média', 'value' => $notaMedia . ' / 5', 'icon' => 'fa-star-half-o'],
            ['label' => 'Avaliações 5 Estrelas', 'value' => $avaliacoes5Estrelas, 'icon' => 'fa-star'],
            ['label' => 'A Melhorar', 'value' => $totalAvaliacoes - $avaliacoes5Estrelas, 'icon' => 'fa-thumbs-o-down']
        ];

        View::render("avaliacao/index", [
            "avaliacoes" => $dadosPaginados['data'],
            "paginacao" => $dadosPaginados,
            "stats" => $stats
        ]);
    }

    public function viewCriarAvaliacoes(){
        View::render("avaliacao/create");
    }
 
    public function viewEditarAvaliacoes($id){
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
 
    public function viewExcluirAvaliacoes($id){
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
    
    public function atualizarAvaliacoes($id)
    {
        if (!$id) {
            Redirect::redirecionarComMensagem("avaliacoes/listar", "error", "ID da avaliação não informado.");
            return;
        }
    
        $erros = AvaliacaoValidador::ValidarEntradas($_POST);
        if (!empty($erros)) {
            Redirect::redirecionarComMensagem("avaliacoes/editar/{$id}", "error", implode("<br>", $erros));
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
            Redirect::redirecionarComMensagem("avaliacoes/editar/{$id}", "error", "Erro ao atualizar a avaliação.");
        }
    }
    
    public function deletarAvaliacoes($id)
    {
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

    public function salvarAvaliacaoCliente()
    {
        header('Content-Type: application/json');

        // 1. Verificar se o cliente está logado
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || ($_SESSION['usuario_tipo'] ?? '') !== 'cliente') {
            http_response_code(401); // Não autorizado
            echo json_encode(['success' => false, 'message' => 'Acesso não autorizado. Faça login como cliente.']);
            return;
        }
        $id_cliente = $_SESSION['usuario_id'];

        // 2. Obter dados do POST
        $id_profissional = $_POST['id_profissional'] ?? null;
        $nota_avaliacao = $_POST['nota_avaliacao'] ?? null;
        $descricao_avaliacao = $_POST['descricao_avaliacao'] ?? '';
        // Opcional: Pegar id_agendamento se quiser marcar como avaliado
        // $id_agendamento = $_POST['id_agendamento'] ?? null;

        // 3. Validar os dados
        $dadosParaValidar = [
            'id_cliente' => $id_cliente, // Adiciona o id_cliente para validação
            'id_profissional' => $id_profissional,
            'nota_avaliacao' => $nota_avaliacao,
            'descricao_avaliacao' => $descricao_avaliacao
        ];
        $erros = AvaliacaoValidador::ValidarEntradas($dadosParaValidar);

        if (!empty($erros)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => implode("\n", $erros)]);
            return;
        // 3.5. Verificar se o cliente tem agendamento CONCLUÍDO com este profissional
        if (!$this->agendamento->verificarAgendamentoConcluido((int)$id_cliente, (int)$id_profissional)) {
            http_response_code(403); // Forbidden
            echo json_encode(['success' => false, 'message' => 'Você só pode avaliar profissionais com quem já teve uma consulta concluída.']);
            return;
        }}

        
        $avaliacaoExistente = $this->avaliacao->buscarAvaliacaoPorClienteEProfissional($id_cliente, (int)$id_profissional);
        if ($avaliacaoExistente) {
             http_response_code(409); // Conflict
             echo json_encode(['success' => false, 'message' => 'Você já avaliou este profissional.']);
             return;
        }

        // Nota: O método buscarAvaliacaoPorClienteEProfissional precisaria ser criado no Model.

        // 4. Inserir no banco de dados
        try {
            $id_inserido = $this->avaliacao->inserirAvaliacao(
                (int)$id_cliente,
                (int)$id_profissional,
                $descricao_avaliacao,
                (int)$nota_avaliacao
            );

            if ($id_inserido) {
                // Opcional: Marcar o agendamento como avaliado no banco, se necessário
                // if ($id_agendamento) { /* Lógica para atualizar agendamento */ }

                http_response_code(201); // Created
                echo json_encode(['success' => true, 'message' => 'Avaliação enviada com sucesso! Obrigado pelo seu feedback.']);
            } else {
                throw new \Exception("Erro desconhecido ao salvar avaliação no banco de dados.");
            }
        } catch (\PDOException $e) {
            error_log("Erro PDO ao salvar avaliação cliente: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Erro interno [DB] ao salvar sua avaliação. Tente novamente mais tarde.']);
        } catch (\Exception $e) {
            error_log("Erro geral ao salvar avaliação cliente: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}