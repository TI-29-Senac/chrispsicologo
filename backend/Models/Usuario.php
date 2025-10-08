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
    private $table = 'usuarios';


    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function listarTodosUsuarios()
    {
        
        $sql = "SELECT u.*, p.id_profissional 
                FROM {$this->table} u
                LEFT JOIN profissionais p ON u.id_usuario = p.id_usuario";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
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
    public function excluirUsuario(int $id_usuario): bool
    {
        $sql = "
            UPDATE usuario 
            SET excluido_em = NOW() 
            WHERE id_usuario = :id_usuario AND excluido_em IS NULL
        ";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Exception $e) {
            // Opcional: Logar o erro
            return false;
        }
    }


    /**
     * Buscar todos os usuários
     */
    public function buscarUsuarios(): array {
        $sql = "SELECT * FROM usuario";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
// Buscar usuario por tipo
    public function listarUsuariosPorTipo(string $tipo): array
{
    $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE tipo_usuario = :tipo");
    $stmt->bindValue(':tipo', $tipo);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
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

    // ... (dentro da classe Usuario)

    /**
     * Buscar usuário por Email
     */
    public function buscarUsuarioPorEmail(string $email) {
        $sql = "SELECT * FROM usuario WHERE email_usuario = :email AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function autenticarUsuario(string $email, string $senha) {
        $usuario = $this->buscarUsuarioPorEmail($email);

        if ($usuario && password_verify($senha, $usuario->senha_usuario)) {
            unset($usuario->senha_usuario);
            return $usuario;
        }

        return false;
    }



    public function atualizarUsuario(int $id, string $nome, string $email, ?string $senha, string $tipo) {
    $dataAtual = date('Y-m-d H:i:s');

    if ($senha) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuario 
                SET nome_usuario = :nome,
                    email_usuario = :email,
                    senha_usuario = :senha,
                    tipo_usuario = :tipo,
                    atualizado_em = :atual
                WHERE id_usuario = :id";
    } else {
        $sql = "UPDATE usuario 
                SET nome_usuario = :nome,
                    email_usuario = :email,
                    tipo_usuario = :tipo,
                    atualizado_em = :atual
                WHERE id_usuario = :id";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':atual', $dataAtual);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($senha) {
        $stmt->bindParam(':senha', $senhaHash);
    }

    return $stmt->execute() ? $stmt->rowCount() : false;
}
}


