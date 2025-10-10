<?php
namespace App\Psico\Models;
use PDO;


class Profissional {
    private $id_profissional;
    private $id_usuario;
    private $nome_profissional;
    private $especialidade_profissional;
    private $tipo_profissional;
    private $status_profissional;
    private $criado_em;
    private $atualizado_em;
    private $excluido_em;
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Buscar todos os profissionais ativos
     */
    public function buscarProfissionais(): array {
        $sql = "
            SELECT 
                p.id_profissional,
                p.id_usuario,
                u.nome_usuario,
                u.status_usuario,
                p.especialidade,
                p.criado_em,
                p.atualizado_em,
                p.excluido_em
            FROM 
                profissional p
            INNER JOIN 
                usuario u ON p.id_usuario = u.id_usuario
            WHERE 
                p.excluido_em IS NULL
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar profissionais por especialidade
     */
    public function buscarProfissionaisPorEspecialidade(string $especialidade): array {
        $sql = "SELECT * FROM profissional WHERE especialidade = :especialidade AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':especialidade', $especialidade);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserir novo profissional
     */
public function inserirProfissional(int $id_usuario, string $especialidade) {
    $sql = "INSERT INTO profissional (id_usuario, especialidade) VALUES (:id_usuario, :especialidade)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':especialidade', $especialidade);

    if ($stmt->execute()) {
        return $this->db->lastInsertId();
    }
    return false;
}

// Em backend/Models/Profissional.php

public function listarProfissionaisComStatus(): array
{
    $sql = "SELECT p.*, u.status_usuario 
            FROM profissional p
            JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE u.excluido_em IS NULL";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

/**
 * Buscar um profissional pelo ID
 */
 public function buscarProfissionalPorId(int $id_profissional) { // Removido o tipo de retorno :?array
        $sql = "
            SELECT 
                p.id_profissional,
                p.id_usuario,
                p.especialidade,
                u.nome_usuario,
                u.email_usuario,
                u.tipo_usuario,
                u.status_usuario
            FROM 
                profissional p
            INNER JOIN 
                usuario u ON p.id_usuario = u.id_usuario
            WHERE 
                p.id_profissional = :id_profissional
                AND p.excluido_em IS NULL
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->execute();

        // CORREÇÃO: Alterado de FETCH_ASSOC para FETCH_OBJ
        $profissional = $stmt->fetch(PDO::FETCH_OBJ);

        return $profissional ?: null;
    }

    /**
     * Atualizar especialidade do profissional
     */
    public function atualizarProfissional(int $id_usuario, string $especialidade): bool {
        $dataAtual = date('Y-m-d H:i:s');

        $sql = "UPDATE profissional 
                SET especialidade = :especialidade,
                    atualizado_em = :atual
                WHERE id_usuario = :id_usuario";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':especialidade', $especialidade);
        $stmt->bindParam(':atual', $dataAtual);

        return $stmt->execute();
    }

    /**
     * Deletar profissional
     */
    public function deletarProfissional(int $id_profissional) {
        $sql = "DELETE FROM profissional WHERE id_profissional = :id_profissional";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount();
        }

        return false;
    }
}
