<?php
namespace App\Psico\Models;
use PDO;

class Pagamento {
    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    /**
     * Inserir novo pagamento
     */
    public function inserirPagamento(int $id_agendamento, float $valor_consulta, float $sinal_consulta, string $tipo_pagamento) {
        // CORREÇÃO: Usando a tabela 'pagamento' (singular)
        $sql = "INSERT INTO pagamento (id_agendamento, tipo_pagamento)
                VALUES (:id_agendamento, :tipo_pagamento)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_pagamento', $tipo_pagamento);

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * CORREÇÃO PRINCIPAL: Busca todos os pagamentos com os dados relacionados
     */
    public function buscarTodosPagamentos(): array
    {
        // CORREÇÃO: Usando 'pagamento' (singular) em todos os JOINS
        $sql = "
            SELECT
                p.id_pagamento,
                p.tipo_pagamento,
                u_cliente.nome_usuario AS nome_cliente,
                u_profissional.nome_usuario AS nome_profissional,
                pr.valor_consulta,
                pr.sinal_consulta
            FROM
                pagamento p -- Corrigido
            LEFT JOIN
                agendamento a ON p.id_agendamento = a.id_agendamento
            LEFT JOIN
                profissional pr ON a.id_profissional = pr.id_profissional
            LEFT JOIN
                usuario u_cliente ON a.id_usuario = u_cliente.id_usuario
            LEFT JOIN
                usuario u_profissional ON pr.id_usuario = u_profissional.id_usuario
            WHERE
                p.excluido_em IS NULL
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar todos os pagamentos (versão simples, sem JOINS)
     */
    public function buscarPagamentos(): array {
        // CORREÇÃO: Usando a tabela 'pagamento' (singular)
        $sql = "SELECT * FROM pagamento WHERE excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Buscar pagamento por ID
     */
    public function buscarPagamentoPorId(int $id): ?array {
        // CORREÇÃO: Usando a tabela 'pagamento' (singular)
        $sql = "SELECT * FROM pagamento WHERE id_pagamento = :id AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Deletar pagamento
     */
    public function deletarPagamento(int $id_pagamento): int {

        $sql = "DELETE FROM pagamento WHERE id_pagamento = :id_pagamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Atualizar pagamento
     */
    public function atualizarPagamento(int $id_pagamento, string $tipo_pagamento): bool {
        $dataAtual = date('Y-m-d H:i:s');
        $sql = "UPDATE pagamento 
                SET tipo_pagamento = :tipo_pagamento,
                    atualizado_em = :atual
                WHERE id_pagamento = :id_pagamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_pagamento', $tipo_pagamento);
        $stmt->bindParam(':atual', $dataAtual);

        return $stmt->execute();
    }
}