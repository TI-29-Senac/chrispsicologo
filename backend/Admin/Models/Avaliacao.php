<?php

class Avaliacao {
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    // Inserir avaliação
    public function inserirAvaliacao($id_cliente, $id_profissional, $descricao_avaliacao, $nota_avaliacao){
        $sql = "INSERT INTO avaliacao (id_cliente, id_profissional, descricao_avaliacao, nota_avaliacao)
                VALUES (:id_cliente, :id_profissional, :descricao_avaliacao, :nota_avaliacao)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
        $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);
        $stmt->bindParam(':descricao_avaliacao', $descricao_avaliacao);
        $stmt->bindParam(':nota_avaliacao', $nota_avaliacao, PDO::PARAM_INT);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    // Buscar avaliações
    public function buscarAvaliacoes() {
        $sql = "SELECT * FROM avaliacao WHERE excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Deletar avaliação
    public function deletarAvaliacao($id_avaliacao){
        $sql = "DELETE FROM avaliacao WHERE id_avaliacao = :id_avaliacao";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    // Atualizar avaliação
public function atualizarAvaliacao($id_avaliacao, $descricao_avaliacao, $nota_avaliacao){
    $dataatual = date('Y-m-d H:i:s');
    $sql = "UPDATE avaliacao
            SET descricao_avaliacao = :descricao_avaliacao,
                nota_avaliacao = :nota_avaliacao,
                atualizado_em = :atual
            WHERE id_avaliacao = :id_avaliacao";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
    $stmt->bindParam(':descricao_avaliacao', $descricao_avaliacao);
    $stmt->bindParam(':nota_avaliacao', $nota_avaliacao, PDO::PARAM_INT);
    $stmt->bindParam(':atual', $dataatual);
    return $stmt->execute();
}

}

?>