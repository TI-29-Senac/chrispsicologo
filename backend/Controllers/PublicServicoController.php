<?php
namespace App\Psico\Controllers;

// Modelos e DB
use App\Psico\Models\Servico; // <<< Alterado para o Model de Serviço
use App\Psico\Database\Database;
use PDO;

class PublicServicoController {

    public $servicoModel; // <<< Alterado
    public $db;

    // Construtor público, NÃO chama o AuthenticatedController
    public function __construct(){
        $this->db = Database::getInstance();
        $this->servicoModel = new Servico($this->db); // <<< Alterado
    }

    /**
     * Método público para a lista da página 'servicos.html'.
     * Esta é a API chamada pela rota /api/servicos/listar
     */
     public function listarServicosPublicos() {
        header('Content-Type: application/json');
        try {
            // Busca os serviços ativos do Model
            $servicos = $this->servicoModel->buscarTodosAtivos();

            // Ajusta o caminho do ícone para o frontend (adiciona a barra '/')
            $data = array_map(function($servico) {
                //
                $servico->icone_path = '/' . ltrim($servico->icone_path ?? '', '/'); 
                return $servico;
            }, $servicos);

            http_response_code(200);
            // Retorna um JSON padronizado para o JavaScript
            echo json_encode(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            http_response_code(500);
            error_log("Erro API listarServicosPublicos: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro interno ao buscar serviços.']);
        }
    }

    /*
     * Nota: 
     * Diferente do PublicProfissionalController,
     * não precisamos de um 'detalhePublico' ou 'getCarrosselCardsHtml' aqui,
     * pois a página servicos.html não tem uma página de detalhe
     * e o carrossel de serviços da index.html é alimentado pelo ImagemController
     * (como visto em Rotas.php).
     */
}