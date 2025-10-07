<?php

namespace App\Psico\Rotas;

class Rotas {
    public static function get(){
        return [
            "GET" => [
                "/usuarios" => "UsuarioController@index",
                "/usuario/criar" => "UsuarioController@viewCriarUsuarios",
                "/usuario/listar" => "UsuarioController@viewListarUsuarios",
                "/usuario/editar/{id}" => "UsuarioController@viewEditarUsuarios",
                "/usuario/excluir/{id}" => "UsuarioController@viewExcluirUsuarios",
                "/agendamentos" => "AgendamentoController@index",
                "/agendamentos/criar" => "AgendamentoController@viewCriarAgendamentos",
                "/agendamentos/listar" => "AgendamentoController@viewListarAgendamentos",
                "/agendamentos/editar/{id}" => "AgendamentoController@viewEditarAgendamentos",
                "/agendamentos/excluir/{id}" => "AgendamentoController@viewExcluirAgendamentos",
                "/avaliacoes" => "AvaliacaoController@buscarPorProfissional",
                "/usuario/relatorio/{id}/{data1}/{data2}" => "UsuarioController@relatorioUsuarios",
            
                // NOVAS ROTAS GET PARA PAGAMENTO
                "/pagamentos/criar" => "PagamentoController@viewCriarPagamentos",
                "/pagamentos/listar" => "PagamentoController@viewListarPagamentos",
                "/pagamentos/editar/{id}" => "PagamentoController@viewEditarPagamentos",
                "/pagamentos/excluir/{id}" => "PagamentoController@viewExcluirPagamentos",

                // NOVAS ROTAS GET PARA AVALIACAO (CRIAÇÃO/EDIÇÃO)
                "/avaliacoes/criar" => "AvaliacaoController@viewCriarAvaliacoes",
                "/avaliacoes/listar" => "AvaliacaoController@viewListarAvaliacoes",
                "/avaliacoes/editar/{id}" => "AvaliacaoController@viewEditarAvaliacoes",
                "/avaliacoes/excluir/{id}" => "AvaliacaoController@viewExcluirAvaliacoes",

                "/profissionais" => "ProfissionalController@index",
                "/profissionais/criar" => "ProfissionalController@viewCriarProfissionais",
                "/profissionais/listar" => "ProfissionalController@viewListarProfissionais",
                "/profissionais/editar/{id}" => "ProfissionalController@viewEditarProfissionais",
                "/profissionais/excluir/{id}" => "ProfissionalController@viewExcluirProfissionais",
            ],
            "POST" => [
                "/usuario/salvar" => "UsuarioController@salvarUsuarios",
                "/usuario/atualizar/{id}" => "UsuarioController@atualizarUsuarios",
                "/usuario/deletar/{id}" => "UsuarioController@deletarUsuarios",
                "/agendamentos/salvar" => "AgendamentoController@salvarAgendamentos",
                "/agendamentos/atualizar/{id}" => "AgendamentoController@atualizarAgendamentos",
                "/agendamentos/deletar/{id}" => "AgendamentoController@deletarAgendamentos",

                // NOVAS ROTAS POST PARA PAGAMENTO
                "/pagamentos/salvar" => "PagamentoController@salvarPagamentos",
                
                // NOVAS ROTAS POST PARA AVALIACAO
                "/avaliacoes/salvar" => "AvaliacaoController@salvarAvaliacoes",

                "/profissionais/salvar" => "ProfissionalController@salvarProfissionais",
                "/profissionais/atualizar/{id}" => "ProfissionalController@atualizarProfissionais",
                "/profissionais/deletar/{id}" => "ProfissionalController@deletarProfissionais",
                "/login" => "UsuarioController@login",
            ],
        ];
    }
}