<?php
namespace App\Psico\Validadores;

class AgendamentoValidador {
    public static function ValidarEntradas($dados){
        $erros = [];
        // Paciente
        if (isset($dados['id_paciente']) && empty($dados['id_paciente'])){
            $erros[] = "O campo paciente é obrigatório.";
        }
        // Profissional
        if (isset($dados['id_profissional']) && empty($dados['id_profissional'])){
            $erros[] = "O campo profissional é obrigatório.";
        }
        // Data do Agendamento
        if (isset($dados['data_agendamento']) && empty($dados['data_agendamento'])){
            $erros[] = "O campo data do agendamento é obrigatório.";
        }
        return $erros;
    }
}