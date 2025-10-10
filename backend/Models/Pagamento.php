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



    public function paginacao(int $pagina = 1, int $por_pagina = 5): array {
        $offset = ($pagina - 1) * $por_pagina;

        $totalQuery = "SELECT COUNT(*) FROM {$this->table} WHERE excluido_em IS NULL";
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
            WHERE p.excluido_em IS NULL
            ORDER BY p.criado_em DESC
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
            WHERE p.excluido_em IS NULL
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}