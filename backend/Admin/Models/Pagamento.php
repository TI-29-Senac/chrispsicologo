<?php
class Pagamento{
    private $id_pagamento;
    private $id_agendamento;
    private $valor_consulta;
    private $sinal_consulta;
    private $tipo_pagamento;
    private $status_pagamento;
    private $criado_em;
    private $atualizado_em;
    private $excluido_em;
    private $db;

public function __construct($db){
}

function buscarPagamento(){
    $sql = "SELECT * FROM pagamento WHERE excluido_em IS NULL";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);   
}

function buscarPagamentoPorId($id_pagamento){
    $sql = "SELECT * FROM pagamento WHERE id_pagamento = :id AND excluido_em IS NULL";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);   
}
function buscarPagamentoPorStatus($status){
    $sql = "SELECT * FROM pagamento WHERE status_pagamento = :status AND excluido_em IS NULL";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':status', $status);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);   
}
function buscarPagamentoPorTipo($tipo){
    $sql = "SELECT * FROM pagamento WHERE tipo_pagamento = :tipo AND excluido_em IS NULL";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);   
}
function inserirPagamento(){
    $sql = "INSERT INTO pagamento (valor_consulta, sinal_consulta, tipo_pagamento, :status) 
            VALUES (:valor, :sinal, :tipo, :status";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':sinal', $sinal);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':status', $status);
    if($stmt->execute()){
        return $this->db->lastInsertId();
    }else{
        return false;
    }
}
function atualizarPagamento($id, $id_agendamento, $valor, $tipo, $status){
    $senha = password_hash($senha, PASSWORD_DEFAULT);
    $dataatual = date('Y-m-d H:i:s');
    $sql = "UPDATE pagamento
        SET id_pagamento = :id, 
            id_agendamento = :id_agendamento, 
            valor_consulta = :valor, 
            tipo_pagamento = :tipo, 
            status_pagamento = :status, 
            atualizado_em = :atual 
            WHERE id_pagamento = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':id_agendamento', $id_agendamento);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':atual', $dataatual);
    if($stmt->execute()){
        return true;
    }else{
        return false;
    }
}
function excluirPagamento($id){
    $dataatual = date('Y-m-d H:i:s');
    $sql = "UPDATE pagamento
            SET excluido_em = :atual 
            WHERE id_pagamento = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':atual', $dataatual);
    if($stmt->execute()){
        return true;
    }else{
        return false;
    }
}
}