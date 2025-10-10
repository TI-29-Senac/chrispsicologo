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
}