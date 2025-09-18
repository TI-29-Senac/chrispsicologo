<?php 



class Profissional{
    private $id_usuario;
    private $nome_profissional;
    private $especialidade_profissional;
    private $status_profissional;
    private $criado_em;
    private $atualizado_em;
    private $excluido_em;
    private $db;

    public function __construct($db){
        $this->db = $db;
    }


}