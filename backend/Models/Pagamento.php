<?php
namespace App\Psico\Models;
use PDO;

class Pagamento {
    private PDO $db;
    private $table = 'pagamento';

    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function inserirPagamento(int $id_agendamento, string $tipo_pagamento) {
        $sql = "INSERT INTO {$this->table} (id_agendamento, tipo_pagamento)
                VALUES (:id_agendamento, :tipo_pagamento)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_pagamento', $tipo_pagamento);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function paginacao(int $pagina = 1, int $por_pagina = 5): array {
        $offset = ($pagina - 1) * $por_pagina;

        $totalQuery = "SELECT COUNT(*) FROM {$this->table}";
        $totalStmt = $this->db->query($totalQuery);
        $total_de_registros = $totalStmt->fetchColumn();

        $dataQuery = "
            SELECT
                p.id_pagamento,
                p.tipo_pagamento,
                p.criado_em as data_pagamento,
                cliente.nome_usuario AS nome_cliente,
                profissional_usuario.nome_usuario AS nome_profissional,
                prof.valor_consulta
            FROM {$this->table} p
            JOIN agendamento a ON p.id_agendamento = a.id_agendamento
            JOIN usuario cliente ON a.id_usuario = cliente.id_usuario
            JOIN profissional prof ON a.id_profissional = prof.id_profissional
            JOIN usuario profissional_usuario ON prof.id_usuario = profissional_usuario.id_usuario
            ORDER BY p.id_pagamento ASC
            LIMIT :limit OFFSET :offset
        ";
        
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

    public function buscarTodosPagamentos(): array {
        $sql = "
            SELECT prof.valor_consulta, p.tipo_pagamento
            FROM {$this->table} p
            JOIN agendamento a ON p.id_agendamento = a.id_agendamento
            JOIN profissional prof ON a.id_profissional = prof.id_profissional
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
public function buscarPagamentoPorId(int $id_pagamento): ?array {
        $sql = "
            SELECT
                p.id_pagamento,
                p.tipo_pagamento,
                p.criado_em as data_pagamento,
                a.id_agendamento,
                prof.valor_consulta
            FROM {$this->table} p
            JOIN agendamento a ON p.id_agendamento = a.id_agendamento
            JOIN profissional prof ON a.id_profissional = prof.id_profissional
            WHERE p.id_pagamento = :id_pagamento
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmt->execute();
        $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);
        return $pagamento ?: null;
    }

    public function atualizarPagamento(int $id_pagamento, string $tipo_pagamento, float $valor_consulta): bool {
        $sqlPagamento = "UPDATE {$this->table}
                         SET tipo_pagamento = :tipo_pagamento,
                             atualizado_em = NOW()
                         WHERE id_pagamento = :id_pagamento";
        $stmtPagamento = $this->db->prepare($sqlPagamento);
        $stmtPagamento->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmtPagamento->bindParam(':tipo_pagamento', $tipo_pagamento);
        $sucessoPagamento = $stmtPagamento->execute();

        $sqlProfissional = "UPDATE profissional prof
                           JOIN agendamento a ON prof.id_profissional = a.id_profissional
                           JOIN pagamento p ON a.id_agendamento = p.id_agendamento
                           SET prof.valor_consulta = :valor_consulta
                           WHERE p.id_pagamento = :id_pagamento";
        $stmtProfissional = $this->db->prepare($sqlProfissional);
        $stmtProfissional->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmtProfissional->bindParam(':valor_consulta', $valor_consulta);
        $sucessoProfissional = $stmtProfissional->execute();

        return $sucessoPagamento && $sucessoProfissional;
    }

    public function deletarPagamento(int $id_pagamento): bool {
        $sql = "DELETE FROM {$this->table} WHERE id_pagamento = :id_pagamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        return $stmt->execute();
    }
}