<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Agendamento;
use App\Psico\Database\Database;

class AgendamentoController {
    public $agendamento;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->agendamento = new Agendamento($this->db);

    }
    // Index
    public function index(){
        $resultado = $this->agendamento->buscarAgendamentos();
        return  $resultado;
    }

    // ===== TESTE DE AGENDAMENTO =====

// Atualizar agendamento

// Buscar agendamentos

    // Registrar

    
    // Login


    // Atualizar


    // Deletar


    // Chamada de API



}