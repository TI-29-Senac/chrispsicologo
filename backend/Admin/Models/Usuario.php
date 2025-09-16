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
    // Construtor, inicializa a classe e ou atributos 
    public function __construct($db){
        $this->db = $db;
    }
    // Método de buscar todos os usuários READ
    function buscarUsuarios(){
        $sql = "SELECT * FROM Usuario WHERE excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);   
    }
    // Método de buscar usuário por email READ
    function buscarUsuariosPorEMail($email){
        $sql = "SELECT * FROM Usuario WHERE email_usuario = :email AND excluido_em IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);   
    }
    // Método de inserir usuário CREATE 
    function inserirUsuario($nome, $email, $senha, $tipo){
        $senha = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Usuario (nome_usuario, email_usuario, senha_usuario, tipo_usuario) 
                VALUES (:nome, :email, :senha, :tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':tipo', $tipo);
        if($stmt->execute()){
            return $this->db->lastInsertId();
    }else{
            return false;
        }
    }
    // Método de atualizar usuário UPDATE
    function atualizarUsuario($id, $nome, $email, $senha, $tipo, $status){
        $senha = password_hash($senha, PASSWORD_DEFAULT);
        $dataatual = date('Y-m-d H:i:s');
        $sql = "UPDATE Usuario 
                SET nome_usuario = :nome, 
                email_usuario = :email, 
                senha_usuario = :senha, 
                tipo_usuario = :tipo,  
                atualizado_em = :atual 
                WHERE id_usuario = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':atual', $dataatual);
        if($stmt->execute()){
            return true;
    }else{
            return false;
        }
    }
    // Método de excluir usuário DELETE
    function excluirUsuario($id){
        $dataatual = date('Y-m-d H:i:s');
        $sql = "UPDATE Usuario 
                SET excluido_em = :atual 
                WHERE id_usuario = :id";
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