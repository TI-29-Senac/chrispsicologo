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
        
        // --- VALIDAÇÕES ADICIONADAS ---

        // Valor da Consulta
        if (!isset($dados['valor_consulta']) || !is_numeric($dados['valor_consulta']) || $dados['valor_consulta'] < 0) {
            $erros[] = "O campo 'Valor da Consulta' é obrigatório e deve ser um número válido.";
        }
        
        // Valor do Sinal
        if (!isset($dados['sinal_consulta']) || !is_numeric($dados['sinal_consulta']) || $dados['sinal_consulta'] < 0) {
            $erros[] = "O campo 'Valor do Sinal' é obrigatório e deve ser um número válido.";
        }
        
        return $erros;
    }
}