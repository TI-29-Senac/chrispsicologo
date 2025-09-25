<?php
namespace App\Psico\Models;

use PDO;

class Usuario {
    private $id_usuario;
    private $nome_usuario;
    private $email_usuario;
    private $senha_usuario;
    private $tipo_usuario;
    private $status_usuario;
    private $criado_em;
    private $atualizado_em;
    private $excluido_em;
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Inserir um novo usuário
     */
    public function inserirUsuario(string $nome, string $email, string $senha, string $tipo, int $status) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (nome_usuario, email_usuario, senha_usuario, tipo_usuario, status_usuario)
                VALUES (:nome, :email, :senha, :tipo, :status)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Atualizar o tipo de usuário
     */
    public function atualizarTipoUsuario(int $id, string $tipo) {
        $dataAtual = date('Y-m-d H:i:s');

        $sql = "UPDATE usuario 
                SET tipo_usuario = :tipo, atualizado_em = :atual
                WHERE id_usuario = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':atual', $dataAtual);

        return $stmt->execute();
    }

    /**
     * Deletar usuário
     */
    public function deletarUsuario(int $id) {
        $sql = "DELETE FROM usuario WHERE id_usuario = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount(); // retorna quantidade de linhas afetadas
        }

        return false;
    }

    /**
     * Buscar todos os usuários
     */
    public function buscarUsuarios(): array {
        $sql = "SELECT * FROM usuario";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Buscar usuário por ID
     */
    public function buscarUsuarioPorId(int $id) {
        $sql = "SELECT * FROM usuario WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
