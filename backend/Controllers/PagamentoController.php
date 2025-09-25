<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Pagamento;
use App\Psico\Database\Database;

class PagamentoController {
    public $pagamento;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->pagamento = new Pagamento($this->db);

    }
    // Index
    public function index(){
        $resultado = $this->pagamento->buscarPagamento();
        return  $resultado;
    }

    // Registrar


    // Login


    // Atualizar


    // Deletar


    // Chamada de API



}