<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;

class AvaliacaoController {
    public $avaliacao;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->avaliacao = new Avaliacao($this->db);

    }
    // ... (Método index() existente) ...
    
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


    // ... (Restante da classe) ...
}