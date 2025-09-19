<?php

class Agendamento {
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    // Inserir agendamento
    public function inserirAgendamento($id_paciente, $id_profissional, $data_agendamento, $status_consulta = 'pendente'){
        $sql = "INSERT INTO agendamento (id_paciente, id_profissional, data_agendamento, status_consulta)
                VALUES (:id_paciente, :id_profissional, :data_agendamento, :status_consulta)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_paciente', $id_paciente, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':data_agendamento', $data_agendamento);
        $stmt->bindParam(':status_consulta', $status_consulta);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    // Buscar agendamentos ativos
    public function buscarAgendamentos() {
        $sql = "SELECT * FROM agendamento WHERE excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Deletar agendamento
    public function deletarAgendamento($id_agendamento){
        $sql = "DELETE FROM agendamento WHERE id_agendamento = :id_agendamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Atualizar agendamento
public function atualizarAgendamento($id_agendamento, $data_agendamento, $status_consulta){
    $dataatual = date('Y-m-d H:i:s');
    $sql = "UPDATE agendamento
            SET data_agendamento = :data_agendamento,
                status_consulta = :status_consulta,
                atualizado_em = :atual
            WHERE id_agendamento = :id_agendamento";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
    $stmt->bindParam(':data_agendamento', $data_agendamento);
    $stmt->bindParam(':status_consulta', $status_consulta);
    $stmt->bindParam(':atual', $dataatual);
    return $stmt->execute();
}

}

?>