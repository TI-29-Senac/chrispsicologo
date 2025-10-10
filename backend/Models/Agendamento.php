<?php
namespace App\Psico\Models;
use PDO;

class Agendamento {
    private PDO $db;
    private $table = 'agendamento';
    public function __construct(PDO $db){
        $this->db = $db;
    }

    public function inserirAgendamento(int $id_usuario, int $id_profissional, string $data_agendamento, string $status_consulta = 'pendente') {
        $sql = "INSERT INTO {$this->table} (id_usuario, id_profissional, data_agendamento, status_consulta)
                VALUES (:id_usuario, :id_profissional, :data_agendamento, :status_consulta)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':data_agendamento', $data_agendamento);
        $stmt->bindParam(':status_consulta', $status_consulta);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function buscarAgendamentos(): array {
        $sql = "
            SELECT 
                a.*,
                paciente.nome_usuario as nome_paciente,
                profissional.nome_usuario as nome_profissional
            FROM {$this->table} a
            JOIN usuario paciente ON a.id_usuario = paciente.id_usuario
            JOIN profissional p ON a.id_profissional = p.id_profissional
            JOIN usuario profissional ON p.id_usuario = profissional.id_usuario
            WHERE a.excluido_em IS NULL
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
      Deletar agendamento
    */
    public function deletarAgendamento(int $id_agendamento): int {
        $sql = "DELETE FROM agendamento WHERE id_agendamento = :id_agendamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /*
      Atualizar agendamento
    */
    public function atualizarAgendamento(int $id_agendamento, string $data_agendamento, string $status_consulta): bool {
        $dataAtual = date('Y-m-d H:i:s');
        $sql = "UPDATE agendamento
                SET data_agendamento = :data_agendamento,
                    status_consulta = :status_consulta,
                    atualizado_em = :atual
                WHERE id_agendamento = :id_agendamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->bindParam(':data_agendamento', $data_agendamento);
        $stmt->bindParam(':status_consulta', $status_consulta);
        $stmt->bindParam(':atual', $dataAtual);

        return $stmt->execute();
    }

    public function paginacao(int $pagina = 1, int $por_pagina = 5): array {
        $offset = ($pagina - 1) * $por_pagina;

        $totalQuery = "SELECT COUNT(*) FROM {$this->table} a WHERE a.excluido_em IS NULL";
        $totalStmt = $this->db->query($totalQuery);
        $total_de_registros = $totalStmt->fetchColumn();

        $dataQuery = "
            SELECT a.*, paciente.nome_usuario as nome_paciente, prof_usuario.nome_usuario as nome_profissional
            FROM {$this->table} a
            JOIN usuario paciente ON a.id_usuario = paciente.id_usuario
            JOIN profissional p ON a.id_profissional = p.id_profissional
            JOIN usuario prof_usuario ON p.id_usuario = prof_usuario.id_usuario
            WHERE a.excluido_em IS NULL
            ORDER BY a.data_agendamento DESC
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
}
