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
        $sql = "INSERT INTO pagamento (id_agendamento, valor_consulta, sinal_consulta, tipo_pagamento)
                VALUES (:id_agendamento, :valor_consulta, :sinal_consulta, :tipo_pagamento)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->bindParam(':valor_consulta', $valor_consulta);
        $stmt->bindParam(':sinal_consulta', $sinal_consulta);
        $stmt->bindParam(':tipo_pagamento', $tipo_pagamento);

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * Buscar todos os pagamentos ativos
     */
    public function buscarPagamentos(): array {
        $sql = "SELECT * FROM pagamento WHERE excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Buscar pagamento por ID
     */
    public function buscarPagamentoPorId(int $id): ?array {
        $sql = "SELECT * FROM pagamento WHERE id_pagamento = :id AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
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
    public function atualizarPagamento(int $id_pagamento, float $valor_consulta, float $sinal_consulta, string $tipo_pagamento): bool {
        $dataAtual = date('Y-m-d H:i:s');
        $sql = "UPDATE pagamento 
                SET valor_consulta = :valor_consulta,
                    sinal_consulta = :sinal_consulta,
                    tipo_pagamento = :tipo_pagamento,
                    atualizado_em = :atual
                WHERE id_pagamento = :id_pagamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmt->bindParam(':valor_consulta', $valor_consulta);
        $stmt->bindParam(':sinal_consulta', $sinal_consulta);
        $stmt->bindParam(':tipo_pagamento', $tipo_pagamento);
        $stmt->bindParam(':atual', $dataAtual);

        return $stmt->execute();
    }
}