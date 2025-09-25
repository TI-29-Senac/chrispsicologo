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
    // Index
    public function index(){
        $resultado = $this->avaliacao->buscarAvaliacoes();
        return  $resultado;
    }

    // Registrar


    // Login


    // Atualizar


    // Deletar


    // Chamada de API



}