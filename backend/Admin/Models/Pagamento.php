<?php

class Pagamento {
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    // Inserir pagamento
    public function inserirPagamento($id_agendamento, $valor_consulta, $sinal_consulta, $tipo_pagamento){
        $sql = "INSERT INTO pagamento (id_agendamento, valor_consulta, sinal_consulta, tipo_pagamento)
                VALUES (:id_agendamento, :valor_consulta, :sinal_consulta, :tipo_pagamento)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_agendamento', $id_agendamento, PDO::PARAM_INT);
        $stmt->bindParam(':valor_consulta', $valor_consulta);
        $stmt->bindParam(':sinal_consulta', $sinal_consulta);
        $stmt->bindParam(':tipo_pagamento', $tipo_pagamento);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    // Buscar pagamentos (todos ativos)
    public function buscarPagamentos() {
        $sql = "SELECT * FROM pagamento WHERE excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Deletar pagamento
    public function deletarPagamento($id_pagamento){
        $sql = "DELETE FROM pagamento WHERE id_pagamento = :id_pagamento";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pagamento', $id_pagamento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Atualizar pagamento
public function atualizarPagamento($id_pagamento, $valor_consulta, $sinal_consulta, $tipo_pagamento){
    $dataatual = date('Y-m-d H:i:s');
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
    $stmt->bindParam(':atual', $dataatual);
    return $stmt->execute();
}

}



?>