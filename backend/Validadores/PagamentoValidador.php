<?php
namespace App\Psico\Validadores;

class PagamentoValidador {
    public static function ValidarEntradas($dados){
        $erros = [];
        // ID Agendamento
        if (empty($dados['id_agendamento'])){
            $erros[] = "O campo de ID Agendamento é obrigatório.";
        }
        // Tipo Pagamento
        if (empty($dados['tipo_pagamento'])){
            $erros[] = "O campo tipo de pagamento é obrigatório.";
        }
        return $erros;
    }
}