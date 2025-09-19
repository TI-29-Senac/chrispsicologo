<?php 

class Profissional{
    private $id_profissional;
    private $id_usuario;
    private $nome_profissional;
    private $especialidade_profissional;
    private $tipo_profissional;
    private $status_profissional;
    private $criado_em;
    private $atualizado_em;
    private $excluido_em;
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

function buscarProfissionais() {
    $sql = "
        SELECT 
            p.id_profissional,
            p.id_usuario,
            u.nome_usuario,
            u.status_usuario,
            p.especialidade,
            p.criado_em,
            p.atualizado_em,
            p.excluido_em
        FROM 
            profissional p
        INNER JOIN 
            usuario u ON p.id_usuario = u.id_usuario
        WHERE 
            p.excluido_em IS NULL
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    function buscarProfissionaisPorEspecialidade($especialidade){
        $sql = "SELECT * FROM profissional WHERE especialidade = :especialidade and excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':especialidade', $especialidade);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Métodos de inserir usuario
function inserirProfissional($id, $especialidade){
    $sql = "INSERT INTO profissional (id_usuario, especialidade) 
            VALUES (:id, :especialidade)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':especialidade', $especialidade);

    if($stmt->execute()){
        return $this->db->lastInsertId();
    } else {
        return false;
    }
}

    // Métodos de atualizar usuario
function atualizarProfissional($id, $especialidade) {
    $dataatual = date('Y-m-d H:i:s');

    $sql = "UPDATE profissional 
            SET especialidade = :especialidade,
                atualizado_em = :atual
            WHERE id_usuario = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':especialidade', $especialidade);
    $stmt->bindParam(':atual', $dataatual);

    return $stmt->execute();
}


    // Métodos de deletar usuario
public function deletarProfissional($id_profissional) {
    $sql = "DELETE FROM profissional WHERE id_profissional = :id_profissional";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id_profissional', $id_profissional, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return $stmt->rowCount();
    } else {
        return false;
    }
}


}
