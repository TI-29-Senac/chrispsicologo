<?php
namespace App\Psico\Models;
use PDO;

class Profissional {
    private $db;
    private $table = 'profissional'; // CORREÇÃO: Nome da tabela em minúsculas

    public function __construct(PDO $db) {
        $this->db = $db;
    }

 public function inserirProfissional(int $id_usuario, string $especialidade, string $img, float $valor, float $sinal) {
        $sql = "INSERT INTO {$this->table} (id_usuario, especialidade, img_profissional, valor_consulta, sinal_consulta) 
                VALUES (:id_usuario, :especialidade, :img, :valor, :sinal)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':especialidade', $especialidade);
        $stmt->bindParam(':img', $img);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':sinal', $sinal);

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;

    }

public function listarProfissionais(): array {
    $sql = "
        SELECT 
            p.id_profissional,
            p.id_usuario,
            u.nome_usuario,
            u.email_usuario,
            u.tipo_usuario,
            u.status_usuario,
            p.especialidade,
            p.criado_em,
            p.atualizado_em
        FROM 
            profissional p
        INNER JOIN 
            usuario u ON p.id_usuario = u.id_usuario
        WHERE 
            p.excluido_em IS NULL
        ORDER BY 
            u.nome_usuario ASC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Buscar um profissional pelo ID
 */
 public function buscarProfissionalPorId(int $id_profissional) { // Removido o tipo de retorno :?array
        $sql = "
            SELECT 
                p.*,
                u.nome_usuario,
                u.email_usuario,
                u.tipo_usuario,
                u.status_usuario
            FROM 
                {$this->table} p
            JOIN 
                usuario u ON p.id_usuario = u.id_usuario
            WHERE 
                p.id_profissional = :id_profissional
                AND p.excluido_em IS NULL
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}