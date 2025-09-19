<?php 



class Usuario{
    private $id_usuario;
    private $nome_usuario;
    private $email_usuario;
    private $senha_usuario;
    private $tipo_usuario;
    private $status_usuario;
    private $criado_em;
    private $atualizado_em;
    private $excluido_em;
    private $db;

    public function __construct($db){
        $this->db = $db;
    }


        function inserirUsuario($nome, $email, $senha, $tipo, $status){
        $senha = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuario (nome_usuario, email_usuario,
         senha_usuario, tipo_usuario, status_usuario)
          VALUES (:nome, :email, :senha, :tipo, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':status', $status);
        if($stmt->execute()){
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }


    function atualizarTipoUsuario($id, $tipo) {
    $dataatual = date('Y-m-d H:i:s');
    $sql = "UPDATE usuario 
            SET tipo_usuario = :tipo,
                atualizado_em = :atual
            WHERE id_usuario = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':atual', $dataatual);

    return $stmt->execute();
}

public function deletarUsuario($id) {
    $sql = "DELETE FROM usuario WHERE id_usuario = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()){
        return $stmt->rowCount();
    } else {
        return false;
    }
}


}