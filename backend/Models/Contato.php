<?php
namespace App\Psico\Models;

use PDO;

class Contato {
    private $db;
    private $table = 'contato';

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function inserirContato(string $nome, string $email, string $mensagem) {
        $sql = "INSERT INTO {$this->table} (nome, email, mensagem) VALUES (:nome, :email, :mensagem)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mensagem', $mensagem);
        
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }
}