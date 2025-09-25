<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Usuario;
use App\Psico\Database\Database;

class UsuarioController {
    public $usuario;   
    public $db;
    public function __construct(){
        $this->db = Database::getInstance();
        $this->usuario = new Usuario($this->db);

    }
    // Index
    public function index(){
        $resultado = $this->usuario->buscarUsuarios();
        return  $resultado;
    }

    // Registrar


    // Login


    // Atualizar


    // Deletar


    // Chamada de API



}