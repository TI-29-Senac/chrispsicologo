<?php
namespace App\Psico\Validadores;

class ProfissionalValidador {
    public static function ValidarEntradas($dados){
        $erros = [];
        // ID Usuario
        if (empty($dados['id_usuario'])){
            $erros[] = "O campo ID do Usuário é obrigatório.";
        }
        // Especialidade
        if (empty($dados['especialidade'])){
            $erros[] = "O campo especialidade é obrigatório.";
        }
        
        // CORREÇÃO: Deve retornar o array de erros
        return $erros;
    }
}