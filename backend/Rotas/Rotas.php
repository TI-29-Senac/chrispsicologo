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
                "/backend/servico/excluir" => "ServicoController@viewExcluirServicos",
                "/backend/avaliacoes" => "AvaliacaoController@buscarPorProfissional"
            ],
            "POST" => [
                "/backend/usuario/salvar" => "UsuarioController@salvarUsuarios",
                "/backend/usuario/atualizar" => "UsuarioController@atualizarUsuarios",
                "/backend/usuario/deletar" => "UsuarioController@deletarUsuarios",
                "/backend/login" => "UsuarioController@login",

            ]
        ];
    }
}