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

                "/carrossel/cards" => "ProfissionalController@getCarrosselCardsHtml",
                
                // NOVAS ROTAS GET PARA AGENDAMENTOS
                "/agendamentos/disponibilidade/{id_profissional}/{data}" => "AgendamentoController@buscarDisponibilidade",
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
                "/profissionais/listar-publico" => "ProfissionalController@listarPublico",
                "/profissionais/listar-publico" => "ProfissionalController@listarPublico",
                "/profissionais/detalhe/{id}" => "ProfissionalController@detalhePublico",
                
                "/logout" => "UsuarioController@logout",
                "/dashboard" => "UsuarioController@dashboard",
                
                // --- ROTAS PARA GERENCIAMENTO DE IMAGENS ---
                "/imagens/listar" => "ImagemController@viewListarImagens",
                "/imagens/criar" => "ImagemController@viewCriarImagem",
                "/imagens/editar/{id}" => "ImagemController@viewEditarImagem", 
                "/imagens/excluir/{id}" => "ImagemController@viewExcluirImagem", 

                // --- NOVA ROTA API PARA BUSCAR SEÇÕES ---
                "/api/secoes/por-pagina/{id_pagina}" => "ImagemController@buscarSecoesPorPaginaApi", // <<< ADICIONADA

                // --- ROTAS DE API EXISTENTES ---
                "/api/imagens/quem-somos" => "ImagemController@listarQuemSomos",
                "/api/imagens/servicos" => "ImagemController@listarServicos",

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

                // --- ROTAS POST PARA IMAGENS ---
                "/imagens/salvar" => "ImagemController@salvarImagem",
                "/imagens/atualizar/{id}" => "ImagemController@atualizarImagem",
                "/imagens/deletar/{id}" => "ImagemController@deletarImagem",
                "/api/cliente/atualizar-perfil" => "UsuarioController@atualizarMeuPerfil",
                
            ],
        ];
    }
}