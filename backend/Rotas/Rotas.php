<?php

namespace App\Psico\Rotas;

class Rotas {
    public static function get(){
        return [
            "GET" => [
                // --- USUARIOS ---
                "/usuarios" => "UsuarioController@meuPerfilApi",
                "/usuario/criar" => "UsuarioController@viewCriarUsuarios",
                "/usuario/listar" => "UsuarioController@viewListarUsuarios",
                "/usuario/editar/{id}" => "UsuarioController@viewEditarUsuarios",
                "/usuario/excluir/{id}" => "UsuarioController@viewExcluirUsuarios",
                
                // Rotas essenciais para o Desktop buscar dados específicos
                "/usuarios/{id}" => "APIUsuarioController@buscarPorId", 
                "/api/usuarios" => "APIUsuarioController@getUsuarios",
                "/api/usuarios/{pagina}" => "APIUsuarioController@getUsuarios",

                // --- AGENDAMENTOS ---
                "/agendamentos" => "AgendamentoController@index",
                "/agendamentos/listar" => "AgendamentoController@viewListarAgendamentos",
                "/api/agendamentos" => "APIAgendamentoController@getAgendamentos",
                "/agendamentos/detalhe-pagamento/{id}" => "PublicAgendamentoController@getDetalhesPagamento",

                // --- PROFISSIONAIS ---
                "/profissionais/listar-publico" => "PublicProfissionalController@listarPublico",
                "/api/profissionais" => "APIProfissionalController@getProfissionais",

                // --- IMAGENS ---
                "/api/imagens/quem-somos" => "ImagemController@listarQuemSomos",
                "/api/imagens/servicos" => "ImagemController@listarServicos",

                // --- GERAL ---
                "/logout" => "UsuarioController@logout",
                "/dashboard" => "UsuarioController@dashboard",
                "/meu-perfil" => "UsuarioController@viewMeuPerfil",
            ],
            
            "POST" => [
                // --- USUARIOS (Ajustado para bater com o log do Desktop) ---
                "/api/usuarios/salvar" => "APIUsuarioController@salvarUsuario", // Corrigido p/ plural conforme log
                "/usuario/salvar" => "UsuarioController@salvarUsuarios",   // Mantido para o Admin Web
                "/usuario/atualizar/{id}" => "UsuarioController@atualizarUsuarios",
                "/usuarios/excluir/{id}" => "APIUsuarioController@deletarUsuario",
                
                // Login
                "/login" => "UsuarioController@login",
                "/api/desktop/login" => "DesktopApiController@login",
                
                // Senha
                "/recuperar-senha/solicitar" => "UsuarioController@solicitarRecuperacaoSenha",
                "/recuperar-senha/processar" => "UsuarioController@processarRedefinicaoSenha",

                // --- AGENDAMENTOS ---
                "/agendamentos/salvar" => "PublicAgendamentoController@salvarAgendamentos", 
                "/api/agendamentos/salvar" => "APIAgendamentoController@salvarAgendamento",
                "/agendamentos/deletar/{id}" => "AgendamentoController@deletarAgendamentos",

                // --- PAGAMENTOS ---
                "/pagamentos/salvar" => "PagamentoController@salvarPagamentos",
                "/api/pagamentos/salvar" => "APIPagamentoController@salvarPagamento",

                // --- CONTATO ---
                "/enviar-contato" => "ContatoController@processarFormulario",
                "/api/contato/enviar" => "APIContatoController@enviarMensagem",
            ],
        ];
    }
}