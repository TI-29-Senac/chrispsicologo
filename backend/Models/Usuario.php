<?php
namespace App\Psico\Models;

use PDO;

class Usuario {
    private $db;
    private $table = 'usuario';

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function inserirUsuario(string $nome, string $email, string $senha, string $tipo, string $cpf) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (nome_usuario, email_usuario, senha_usuario, tipo_usuario, cpf)
                VALUES (:nome, :email, :senha, :tipo, :cpf)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':cpf', $cpf);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function buscarUsuarios(): array {
        $sql = "SELECT * FROM {$this->table}"; 
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function buscarUsuarioPorId(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function buscarUsuarioPorEmail(string $email) {
        $sql = "SELECT * FROM {$this->table} WHERE email_usuario = :email"; 
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

public function atualizarUsuario(int $id, string $nome, string $email, ?string $senha, string $tipo, string $cpf, string $status) {
        $set = "nome_usuario = :nome, email_usuario = :email, tipo_usuario = :tipo, cpf = :cpf, status_usuario = :status, atualizado_em = NOW()";
        if (!empty($senha)) {
            $set .= ", senha_usuario = :senha";
        }
        $sql = "UPDATE {$this->table} SET {$set} WHERE id_usuario = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':status', $status);
        
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt->bindParam(':senha', $senhaHash);
        }
        
        return $stmt->execute();
    }

    public function excluirUsuario(int $id_usuario): bool {
        $sql = "UPDATE {$this->table} SET status_usuario = 'inativo', atualizado_em = NOW(), excluido_em = NOW() WHERE id_usuario = :id_usuario";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function autenticarUsuario(string $email, string $senha) {
        $usuario = $this->buscarUsuarioPorEmail($email);
        if ($usuario && password_verify($senha, $usuario->senha_usuario)) {
            unset($usuario->senha_usuario);
            return $usuario;
        }
        return false;
    }

    public function paginacao(int $pagina = 1, int $por_pagina = 5): array{
        $offset = ($pagina - 1) * $por_pagina;
        
        $totalQuery = "SELECT COUNT(*) FROM {$this->table}"; 
        $totalStmt = $this->db->query($totalQuery);
        $total_de_registros = $totalStmt->fetchColumn();

        $dataQuery = "SELECT * FROM {$this->table} ORDER BY id_usuario ASC LIMIT :limit OFFSET :offset"; 
        $dataStmt = $this->db->prepare($dataQuery);
        $dataStmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $dados = $dataStmt->fetchAll(PDO::FETCH_OBJ);

        return [
            'data' => $dados,
            'total' => (int) $total_de_registros,
            'por_pagina' => (int) $por_pagina,
            'pagina_atual' => (int) $pagina,
            'ultima_pagina' => (int) ceil($total_de_registros / $por_pagina)
        ];
    }

    public function buscarTodosUsuarios(): array {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function buscarUsuariosNaoProfissionais(): array {
        $sql = "SELECT id_usuario, nome_usuario FROM {$this->table} 
                WHERE tipo_usuario != 'profissional' 
                AND id_usuario NOT IN (SELECT id_usuario FROM profissional)";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    
}