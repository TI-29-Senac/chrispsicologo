<?php

namespace App\Psico\Rotas;

class Rotas {
    public static function get(){
        return [
            "GET" => [
                // NOVAS ROTAS GET PARA USUARIOS
                "/usuarios" => "UsuarioController@index",
                "/usuario/criar" => "UsuarioController@viewCriarUsuarios",
                "/usuario/listar" => "UsuarioController@viewListarUsuarios",
                "/usuario/editar/{id}" => "UsuarioController@viewEditarUsuarios",
                "/usuario/excluir/{id}" => "UsuarioController@viewExcluirUsuarios",
                
                // NOVAS ROTAS GET PARA AGENDAMENTOS
                "/agendamentos" => "AgendamentoController@index",
                "/agendamentos/criar" => "AgendamentoController@viewCriarAgendamentos",
                "/agendamentos/listar" => "AgendamentoController@viewListarAgendamentos",
                "/agendamentos/editar/{id}" => "AgendamentoController@viewEditarAgendamentos",
                "/agendamentos/excluir/{id}" => "AgendamentoController@viewExcluirAgendamentos",
                "/avaliacoes" => "AvaliacaoController@buscarPorProfissional",
                "/usuario/relatorio/{id}/{data1}/{data2}" => "UsuarioController@relatorioUsuarios",
            
                // NOVAS ROTAS GET PARA PAGAMENTOS
                "/pagamentos/criar" => "PagamentoController@viewCriarPagamentos",
                "/pagamentos/listar" => "PagamentoController@viewListarPagamentos",
                "/pagamentos/editar/{id}" => "PagamentoController@viewEditarPagamentos",
                "/pagamentos/excluir/{id}" => "PagamentoController@viewExcluirPagamentos",

                // NOVAS ROTAS GET PARA AVALIAÇÕES (CRIAÇÃO/EDIÇÃO)
                "/avaliacoes/criar" => "AvaliacaoController@viewCriarAvaliacoes",
                "/avaliacoes/listar" => "AvaliacaoController@viewListarAvaliacoes",
                "/avaliacoes/editar/{id}" => "AvaliacaoController@viewEditarAvaliacoes",
                "/avaliacoes/excluir/{id}" => "AvaliacaoController@viewExcluirAvaliacoes",

                // NOVAS ROTAS GET PARA PROFISSIONAIS
                "/profissionais" => "ProfissionalController@index",
                "/profissionais/criar" => "ProfissionalController@viewCriarProfissionais",
                "/profissionais/listar" => "ProfissionalController@viewListarProfissionais",
                "/profissionais/editar/{id}" => "ProfissionalController@viewEditarProfissionais",
                "/profissionais/excluir/{id}" => "ProfissionalController@viewExcluirProfissionais",
            ],
            "POST" => [
                // NOVAS ROTAS POST PARA USUARIOS
                "/usuario/salvar" => "UsuarioController@salvarUsuarios",
                "/usuario/atualizar/{id}" => "UsuarioController@atualizarUsuarios",
                "/usuario/deletar/{id}" => "UsuarioController@deletarUsuarios",
                
                // NOVAS ROTAS POST PARA AGENDAMENTOS
                "/agendamentos/salvar" => "AgendamentoController@salvarAgendamentos",
                "/agendamentos/atualizar/{id}" => "AgendamentoController@atualizarAgendamentos",
                "/agendamentos/deletar/{id}" => "AgendamentoController@deletarAgendamentos",

                // NOVAS ROTAS POST PARA PAGAMENTOS
                "/pagamentos/salvar" => "PagamentoController@salvarPagamentos",
                "/pagamentos/atualizar/{id}" =>"PagamentoController@atualizarPagamento",
                "/pagamentos/deletar/{id}" =>"PagamentoController@deletarPagamento",
                
                // NOVAS ROTAS POST PARA AVALIACAOS
                "/avaliacoes/salvar" => "AvaliacaoController@salvarAvaliacoes",
                "/avaliacoes/atualizar/{id}" => "AvaliacaoController@atualizarAvaliacoes",
                "/avaliacoes/deletar/{id}" => "AvaliacaoController@deletarAvaliacoes",

                // NOVAS ROTAS POST PARA PROFISSIONAIS
                "/profissionais/salvar" => "ProfissionalController@salvarProfissionais",
                "/profissionais/atualizar/{id}" => "ProfissionalController@atualizarProfissionais",
                "/profissionais/deletar/{id}" => "ProfissionalController@deletarProfissionais",

                // LOGIN
                "/login" => "UsuarioController@login",
            ],
        ];
    }
}