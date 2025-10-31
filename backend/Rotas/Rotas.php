<?php

namespace App\Psico\Rotas;

class Rotas {
    public static function get(){
        return [
            "GET" => [
                // --- USUARIOS (Admin & API) ---
                "/usuarios" => "UsuarioController@meuPerfilApi", // API 'minha-conta'
                "/usuario/criar" => "UsuarioController@viewCriarUsuarios",
                "/usuario/listar" => "UsuarioController@viewListarUsuarios",
                "/usuario/editar/{id}" => "UsuarioController@viewEditarUsuarios",
                "/usuario/excluir/{id}" => "UsuarioController@viewExcluirUsuarios",
                "/usuario/relatorio/{id}/{data1}/{data2}" => "UsuarioController@relatorioUsuarios",
                "/recuperar-senha/validar/{token}" => "UsuarioController@validarTokenReset",

                // --- AGENDAMENTOS (Admin & API Pública) ---
                "/agendamentos/disponibilidade/{id_profissional}/{data}" => "PublicAgendamentoController@buscarDisponibilidade",
                "/agendamentos" => "AgendamentoController@index",
                "/agendamentos/criar" => "AgendamentoController@viewCriarAgendamentos",
                "/agendamentos/listar" => "AgendamentoController@viewListarAgendamentos",
                "/agendamentos/editar/{id}" => "AgendamentoController@viewEditarAgendamentos",
                "/agendamentos/excluir/{id}" => "AgendamentoController@viewExcluirAgendamentos",
                "/api/cliente/meus-agendamentos" => "AgendamentoController@buscarAgendamentosPorUsuarioApi",
                "/agendamentos/detalhe-pagamento/{id}" => "AgendamentoController@getDetalhesPagamento", 

                // --- AVALIAÇÕES (Admin & API Pública) ---
                "/avaliacoes" => "AvaliacaoController@buscarPorProfissional", // API pública
                "/avaliacoes/criar" => "AvaliacaoController@viewCriarAvaliacoes",
                "/avaliacoes/listar" => "AvaliacaoController@viewListarAvaliacoes",
                "/avaliacoes/editar/{id}" => "AvaliacaoController@viewEditarAvaliacoes",
                "/avaliacoes/excluir/{id}" => "AvaliacaoController@viewExcluirAvaliacoes",

                // --- PAGAMENTOS (Admin) ---
                "/pagamentos/criar" => "PagamentoController@viewCriarPagamentos",
                "/pagamentos/listar" => "PagamentoController@viewListarPagamentos",
                "/pagamentos/editar/{id}" => "PagamentoController@viewEditarPagamentos",
                "/pagamentos/excluir/{id}" => "PagamentoController@viewExcluirPagamentos",

                // --- PROFISSIONAIS (Admin & API Pública) ---
                "/profissionais" => "ProfissionalController@index",
                "/profissionais/criar" => "ProfissionalController@viewCriarProfissionais",
                "/profissionais/listar" => "ProfissionalController@viewListarProfissionais",
                "/profissionais/editar/{id}" => "ProfissionalController@viewEditarProfissionais",
                "/profissionais/excluir/{id}" => "ProfissionalController@viewExcluirProfissionais",
                "/profissionais/listar-publico" => "PublicProfissionalController@listarPublico",
                "/profissionais/detalhe/{id}" => "PublicProfissionalController@detalhePublico",
                "/carrossel/cards" => "PublicProfissionalController@getCarrosselCardsHtml",

                // --- IMAGENS (Admin & API Pública) ---
                "/imagens/listar" => "ImagemController@viewListarImagens",
                "/imagens/criar" => "ImagemController@viewCriarImagem",
                "/imagens/editar/{id}" => "ImagemController@viewEditarImagem",
                "/imagens/excluir/{id}" => "ImagemController@viewExcluirImagem",
                "/api/secoes/por-pagina/{id_pagina}" => "ImagemController@buscarSecoesPorPaginaApi",
                "/api/imagens/quem-somos" => "ImagemController@listarQuemSomos",
                "/api/imagens/servicos" => "ImagemController@listarServicos",

                // --- GERAL (Admin) ---
                "/logout" => "UsuarioController@logout",
                "/dashboard" => "UsuarioController@dashboard",
                "/meu-perfil" => "UsuarioController@viewMeuPerfil",
                "/usuarios" => "UsuarioController@meuPerfilApi",
            ],
            
            "POST" => [
                // --- USUARIOS ---
                
                "/usuario/salvar" => "UsuarioController@salvarUsuarios", // Usado pelo Admin e Registro público
                "/usuario/atualizar/{id}" => "UsuarioController@atualizarUsuarios",
                "/usuario/deletar/{id}" => "UsuarioController@deletarUsuarios",
                "/api/cliente/atualizar-perfil" => "UsuarioController@atualizarMeuPerfil",
                "/login" => "UsuarioController@login",
                "/recuperar-senha/solicitar" => "UsuarioController@solicitarRecuperacaoSenha",
                "/recuperar-senha/processar" => "UsuarioController@processarRedefinicaoSenha",

                // --- AGENDAMENTOS ---
                // Esta é a rota que o seu formulário está chamando:
                "/agendamentos/salvar" => "PublicAgendamentoController@salvarAgendamentos", 
                "/agendamentos/atualizar/{id}" => "AgendamentoController@atualizarAgendamentos",
                "/agendamentos/deletar/{id}" => "AgendamentoController@deletarAgendamentos",

                // --- PAGAMENTOS ---
                "/pagamentos/salvar" => "PagamentoController@salvarPagamentos",
                "/pagamentos/atualizar/{id}" =>"PagamentoController@atualizarPagamento",
                "/pagamentos/deletar/{id}" =>"PagamentoController@deletarPagamento",
                "/agendamentos/confirmar-sinal/{id}" => "AgendamentoController@confirmarSinal", // Rota p/ pág. pagamento

                // --- AVALIAÇÕES ---
                "/avaliacoes/salvar" => "AvaliacaoController@salvarAvaliacoes",
                "/avaliacoes/atualizar/{id}" => "AvaliacaoController@atualizarAvaliacoes",
                "/avaliacoes/deletar/{id}" => "AvaliacaoController@deletarAvaliacoes",
                "/api/cliente/avaliar" => "AvaliacaoController@salvarAvaliacaoCliente",

                // --- PROFISSIONAIS ---
                "/profissionais/salvar" => "ProfissionalController@salvarProfissionais",
                "/profissionais/atualizar/{id}" => "ProfissionalController@atualizarProfissionais",
                "/profissionais/deletar/{id}" => "ProfissionalController@deletarProfissionais",

                // --- IMAGENS ---
                "/imagens/salvar" => "ImagemController@salvarImagem",
                "/imagens/atualizar/{id}" => "ImagemController@atualizarImagem",
                "/imagens/deletar/{id}" => "ImagemController@deletarImagem",
                
                // --- CONTATO ---
                "/enviar-contato" => "ContatoController@processarFormulario",
            ],
        ];
    }
}