<?php
namespace App\Psico\Validadores;

class PagamentoValidador {
    public static function ValidarEntradas($dados){
        $erros = [];
        // ID Agendamento
        if (empty($dados['id_agendamento'])){
            $erros[] = "O campo de ID Agendamento é obrigatório.";
        }
        // Valor Consulta
        if (empty($dados['valor_consulta'])){
            $erros[] = "O campo valor da consulta é obrigatório.";
        }
        // Sinal Consulta
        if (empty($dados['sinal_consulta'])){
            $erros[] = "O campo sinal da consulta é obrigatório.";
        }
        // Tipo Pagamento
        if (empty($dados['tipo_pagamento'])){
            $erros[] = "O campo tipo de pagamento é obrigatório.";
        }
        return $erros;
    }
}