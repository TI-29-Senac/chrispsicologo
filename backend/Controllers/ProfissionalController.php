<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Database\Database;

class ProfissionalController {
    public $profissional;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->profissional = new Profissional($this->db);

    }
    // Index
    public function index(){
        $resultado = $this->profissional->buscarProfissionais();
        return  $resultado;
    }

    // Registrar


    // Login


    // Atualizar


    // Deletar


    // Chamada de API



}