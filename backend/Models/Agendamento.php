<?php
namespace App\Psico\Models;
use PDO;

class Agendamento {
    private PDO $db;

    public function __construct(PDO $db){
        $this->db = $db;
    }

    /*
      Inserir novo agendamento
    */
    public function inserirAgendamento(int $id_usuario, int $id_profissional, string $data_agendamento, string $status_consulta = 'pendente') {
        $sql = "INSERT INTO agendamento (id_usuario, id_profissional, data_agendamento, status_consulta)
                VALUES (:id_usuario, :id_profissional, :data_agendamento, :status_consulta)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':data_agendamento', $data_agendamento);
        $stmt->bindParam(':status_consulta', $status_consulta);

        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    /*
      Buscar todos os agendamentos ativos
    */
    public function buscarAgendamentos(): array {
        $sql = "SELECT * FROM agendamento WHERE excluido_em IS NULL";
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
    public function atualizarAgendamento(int $id_agendamento, int $id_usuario, int $id_profissional, string $data_agendamento): bool {
        $dataAtual = date('Y-m-d H:i:s');
        $sql = "UPDATE agendamento
                SET id_usuario = :id_usuario,
                    id_profissional = :id_profissional,
                    data_agendamento = :data_agendamento,
                    atualizado_em = :atual
                WHERE id_agendamento = :id_agendamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':data_agendamento', $data_agendamento);
        $stmt->bindParam(':atual', $dataAtual);

        return $stmt->execute();
    }
    // NOVO MÉTODO: Essencial para carregar a página de edição/exclusão
    public function buscarAgendamentoPorId(int $id_agendamento): ?array
    {
        $sql = "SELECT * FROM agendamento WHERE id_agendamento = :id AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id_agendamento, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null; // Retorna o agendamento ou null se não encontrar
    }
    
    // ... (seu método buscarAgendamentos fica aqui) ...

    
    
    // ... (seu método deletarAgendamento fica aqui) ...
}

