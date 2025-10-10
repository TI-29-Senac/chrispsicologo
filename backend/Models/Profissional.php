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

public function paginacao(int $pagina = 1, int $por_pagina = 10): array {
        $offset = ($pagina - 1) * $por_pagina;

        $totalQuery = "SELECT COUNT(*) FROM {$this->table} p WHERE p.excluido_em IS NULL";
        $totalStmt = $this->db->query($totalQuery);
        $total_de_registros = $totalStmt->fetchColumn();

        $dataQuery = "
            SELECT p.*, u.nome_usuario, u.email_usuario, u.status_usuario, u.tipo_usuario
            FROM {$this->table} p
            JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE p.excluido_em IS NULL
            ORDER BY u.nome_usuario ASC
            LIMIT :limit OFFSET :offset";
        
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

 public function listarProfissionais(): array {
        $sql = "
            SELECT p.*, u.nome_usuario, u.email_usuario, u.status_usuario, u.tipo_usuario
            FROM {$this->table} p
            JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE p.excluido_em IS NULL
            ORDER BY u.nome_usuario ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
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