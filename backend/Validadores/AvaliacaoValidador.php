<?php
namespace App\Psico\Validadores;

class AvaliacaoValidador {
    public static function ValidarEntradas($dados){
        $erros = [];
        // ID Cliente
        if (isset($dados['id_cliente']) && empty($dados['id_cliente'])){
            $erros[] = "O campo de ID Cliente é obrigatório.";
        }
        // ID Profissional
        if (isset($dados['id_profissional']) && empty($dados['id_profissional'])){
            $erros[] = "O campo de ID Profissional é obrigatório.";
        }
        // Nota Avaliação
        if (isset($dados['nota_avaliacao']) && (empty($dados['nota_avaliacao']) || $dados['nota_avaliacao'] < 1 || $dados['nota_avaliacao'] > 5)){
            $erros[] = "O campo nota da avaliação é obrigatório e deve estar entre 1 e 5.";
        }
        // Descrição Avaliação
        if (isset($dados['descricao_avaliacao']) && empty($dados['descricao_avaliacao'])){
            $erros[] = "O campo descrição da avaliação é obrigatório.";
        }
        return $erros;
    }
}
