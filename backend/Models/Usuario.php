<?php
namespace App\Psico\Models;

use PDO;

class Usuario {
    private $db;
    private $table = 'usuario'; // CORREÇÃO: Nome da tabela em minúsculas

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
        $sql = "SELECT * FROM {$this->table} WHERE excluido_em IS NULL";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function buscarUsuarioPorId(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_usuario = :id AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function buscarUsuarioPorEmail(string $email) {
        $sql = "SELECT * FROM {$this->table} WHERE email_usuario = :email AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function atualizarUsuario(int $id, string $nome, string $email, ?string $senha, string $tipo, string $cpf) {
        $set = "nome_usuario = :nome, email_usuario = :email, tipo_usuario = :tipo, cpf = :cpf, atualizado_em = NOW()";
        if ($senha) {
            $set .= ", senha_usuario = :senha";
        }
        $sql = "UPDATE {$this->table} SET {$set} WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':cpf', $cpf);
        if ($senha) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt->bindParam(':senha', $senhaHash);
        }
        return $stmt->execute();
    }

    public function excluirUsuario(int $id_usuario): bool {
        $sql = "UPDATE {$this->table} SET excluido_em = NOW() WHERE id_usuario = :id_usuario";
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
}