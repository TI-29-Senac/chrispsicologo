<?php
namespace App\Psico\Models;
use PDO;

class Pagamento {
    private PDO $db;
    private $table = 'pagamento'; // Nome da tabela atualizado

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

    public function buscarTodosPagamentos(): array {
        $sql = "
            SELECT
                p.id_pagamento,
                p.tipo_pagamento,
                p.criado_em as data_pagamento,
                cliente.nome_usuario AS nome_cliente,
                profissional_usuario.nome_usuario AS nome_profissional,
                prof.valor_consulta,
                prof.sinal_consulta
            FROM {$this->table} p
            JOIN agendamento a ON p.id_agendamento = a.id_agendamento
            JOIN usuario cliente ON a.id_usuario = cliente.id_usuario
            JOIN profissional prof ON a.id_profissional = prof.id_profissional
            JOIN usuario profissional_usuario ON prof.id_usuario = profissional_usuario.id_usuario
            WHERE p.excluido_em IS NULL
            ORDER BY p.criado_em DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}