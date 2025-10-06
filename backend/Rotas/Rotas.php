<?php

namespace App\Psico\Rotas;

class Rotas {
    public static function get(){
        return [
            "GET" => [
                "/backend/usuarios" => "UsuarioController@index",
                "/backend/usuario/criar" => "UsuarioController@viewCriarUsuarios",
                "/backend/usuario/listar" => "UsuarioController@viewListarUsuarios",
                "/backend/usuario/editar" => "UsuarioController@viewEditarUsuarios",
                "/backend/usuario/excluir" => "UsuarioController@viewExcluirUsuarios",
                "/backend/agendamentos" => "AgendamentoController@index",
                "/backend/agendamentos/criar" => "AgendamentoController@viewCriarAgendamentos",
                "/backend/agendamentos/listar" => "AgendamentoController@viewListarAgendamentos",
                "/backend/agendamentos/editar" => "AgendamentoController@viewEditarAgendamentos",
                "/backend/agendamentos/excluir" => "AgendamentoController@viewExcluirAgendamentos",
                "/backend/avaliacoes" => "AvaliacaoController@buscarPorProfissional"
            ],
            "POST" => [
                "/backend/usuario/salvar" => "UsuarioController@salvarUsuarios",
                "/backend/usuario/atualizar" => "UsuarioController@atualizarUsuarios",
                "/backend/usuario/deletar" => "UsuarioController@deletarUsuarios",
                "/backend/agendamentos/salvar" => "AgendamentoController@salvarAgendamentos",
                "/backend/agendamentos/atualizar" => "AgendamentoController@atualizarAgendamentos",
                "/backend/agendamentos/deletar" => "AgendamentoController@deletarAgendamentos",
                "/backend/login" => "UsuarioController@login",

            ]
        ];
    }
}