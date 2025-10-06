<?php
namespace App\Psico\Validadores;

class ProfissionalValidador {
    public static function ValidarEntradas($dados){
        $erros = [];
        // Profissional
        if (isset($dados['id_profissional']) && empty($dados['id_profissional'])){
            $erros[] = "O campo profissional é obrigatório.";
        }
        // ID Usuario
        if (isset($dados['id_usuario']) && empty($dados['id_usuario'])){
            $erros[] = "O campo usuario é obrigatório.";
        }
        // Especialidade
        if (isset($dados['especialidade']) && empty($dados['especialidade'])){
            $erros[] = "O campo especialidade é obrigatório.";
        }
    }
}