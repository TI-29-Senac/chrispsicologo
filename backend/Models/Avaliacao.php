<?php
namespace App\Psico\Models;
use PDO;
 
class Avaliacao {
    private PDO $db;
    private $table = 'avaliacao';
 
    public function __construct(PDO $db){
        $this->db = $db;
    }
 
    public function inserirAvaliacao(int $id_cliente, int $id_profissional, string $descricao, int $nota) {
        $sql = "INSERT INTO {$this->table} (id_cliente, id_profissional, descricao_avaliacao, nota_avaliacao)
                VALUES (:id_cliente, :id_profissional, :descricao, :nota)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':nota', $nota, PDO::PARAM_INT);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }
 
    public function buscarAvaliacoesPorProfissional(int $id_profissional): array {
        $sql = "
            SELECT
                a.descricao_avaliacao,
                a.nota_avaliacao,
                u.nome_usuario AS cliente
            FROM {$this->table} a
            JOIN usuario u ON a.id_cliente = u.id_usuario
            WHERE a.id_profissional = :id_profissional
            AND a.excluido_em IS NULL  -- Adicione esta linha
            ORDER BY a.criado_em DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletarAvaliacao(int $id_avaliacao): bool {
        $sql = "UPDATE {$this->table} SET excluido_em = NOW() WHERE id_avaliacao = :id_avaliacao"; 
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
        return $stmt->execute();
    }
 
    public function buscarAvaliacaoPorId(int $id_avaliacao): ?array {
        $sql = "SELECT 
                    a.id_avaliacao,
                    a.id_cliente,
                    a.id_profissional,
                    a.descricao_avaliacao,
                    a.nota_avaliacao,
                    a.criado_em,
                    u.nome_usuario AS cliente
                FROM {$this->table} a
                JOIN usuario u ON a.id_cliente = u.id_usuario
                WHERE a.id_avaliacao = :id_avaliacao
                LIMIT 1"; 
     
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
        $stmt->execute();
        $avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);
        return $avaliacao ?: null;
    }

    public function atualizarAvaliacao(int $id_avaliacao, string $descricao, int $nota): bool {
        $sql = "UPDATE {$this->table}
                SET 
                    descricao_avaliacao = :descricao,
                    nota_avaliacao = :nota,
                    atualizado_em = NOW()
                WHERE id_avaliacao = :id_avaliacao"; 
     
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':nota', $nota, PDO::PARAM_INT);
        $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function paginacao(int $pagina = 1, int $por_pagina = 5): array {
        $offset = ($pagina - 1) * $por_pagina;

        $totalQuery = "SELECT COUNT(*) FROM {$this->table}";
        $totalStmt = $this->db->query($totalQuery);
        $total_de_registros = $totalStmt->fetchColumn();

        $dataQuery = "
            SELECT 
                a.*, 
                u_cliente.nome_usuario as nome_cliente,
                u_prof.nome_usuario as nome_profissional
            FROM {$this->table} a
            JOIN usuario u_cliente ON a.id_cliente = u_cliente.id_usuario
            JOIN profissional p ON a.id_profissional = p.id_profissional
            JOIN usuario u_prof ON p.id_usuario = u_prof.id_usuario
            ORDER BY a.id_avaliacao ASC
            LIMIT :limit OFFSET :offset"; 

        $dataStmt = $this->db->prepare($dataQuery);
        $dataStmt->bindValue(':limit', $por_pagina, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $dados = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $dados,
            'total' => (int) $total_de_registros,
            'por_pagina' => (int) $por_pagina,
            'pagina_atual' => (int) $pagina,
            'ultima_pagina' => (int) ceil($total_de_registros / $por_pagina)
        ];
    }

    public function buscarAvaliacoes(): array {
        $sql = "SELECT * FROM {$this->table}"; // REMOVIDO: WHERE excluido_em IS NULL
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}