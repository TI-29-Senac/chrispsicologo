<?php

namespace App\Psico\Rotas;

use Bramus\Router\Router;
use App\Psico\Core\Auth;

class Rotas {
    public static function register(Router $router){
        $router->setNamespace('App\Psico\Controllers');

        // --- MIDDLEWARE DE AUTENTICAÇÃO API ---
        
        // Protege todas as rotas /api/*
        $router->before('GET|POST|PUT|DELETE', '/api/.*', function() {
            // Lista de rotas públicas que não precisam de token
            $publicRoutes = [
                '/backend/api/desktop/login',
                '/backend/api/contato/enviar',
                '/backend/api/imagens/.*' // Imagens geralmente são públicas
            ];

            $currentUri = $_SERVER['REQUEST_URI'];
            
            // Se a URI atual corresponder a alguma rota pública, pula a verificação
            foreach ($publicRoutes as $route) {
                if (preg_match('#^' . $route . '$#', $currentUri)) {
                    return;
                }
            }

            // Para todas as outras rotas /api/, exige Token JWT
            Auth::check();
        });


        // --- GET ---
        
        // USUARIOS
        $router->get('/usuarios', 'UsuarioController@meuPerfilApi');
        $router->get('/usuario/criar', 'UsuarioController@viewCriarUsuarios');
        $router->get('/usuario/listar', 'UsuarioController@viewListarUsuarios');
        $router->get('/usuario/editar/{id}', 'UsuarioController@viewEditarUsuarios');
        $router->get('/usuario/excluir/{id}', 'UsuarioController@viewExcluirUsuarios');
        
        // API (Protegidas pelo Middleware acima)
        $router->get('/usuarios/{id}', 'APIUsuarioController@buscarPorId'); 
        $router->get('/api/usuarios', 'APIUsuarioController@getUsuarios');
        $router->get('/api/usuarios/{pagina}', 'APIUsuarioController@getUsuarios');
        
        // Rota específica do cliente web (usa sessão PHP ainda, não JWT)
        $router->get('/api/cliente/meus-agendamentos', 'AgendamentoController@buscarMeusAgendamentosApi');


        // AGENDAMENTOS
        $router->get('/agendamentos', 'AgendamentoController@index');
        $router->get('/agendamentos/listar', 'AgendamentoController@viewListarAgendamentos');
        $router->get('/api/agendamentos', 'APIAgendamentoController@getAgendamentos');
        $router->get('/agendamentos/disponibilidade/{id}/{data}', 'PublicAgendamentoController@buscarDisponibilidade');
        $router->get('/agendamentos/detalhe-pagamento/{id}', 'PublicAgendamentoController@getDetalhesPagamento');

        // PROFISSIONAIS
        $router->get('/profissionais/listar-publico', 'PublicProfissionalController@listarPublico');
        $router->get('/profissionais/detalhe/{id}', 'PublicProfissionalController@detalhePublico');
        $router->get('/avaliacoes', 'PublicProfissionalController@buscarAvaliacoes');
        $router->get('/api/profissionais', 'APIProfissionalController@getProfissionais');

        // IMAGENS
        $router->get('/api/imagens/quem-somos', 'ImagemController@listarQuemSomos');
        $router->get('/api/imagens/servicos', 'ImagemController@listarServicos');

        // GERAL
        $router->get('/logout', 'UsuarioController@logout');
        $router->get('/dashboard', 'UsuarioController@dashboard');
        $router->get('/meu-perfil', 'UsuarioController@viewMeuPerfil');

        // --- POST ---

        // USUARIOS
        $router->post('/api/usuarios/salvar', 'APIUsuarioController@salvarUsuario');
        $router->post('/usuario/salvar', 'UsuarioController@salvarUsuarios');
        $router->post('/usuario/atualizar/{id}', 'UsuarioController@atualizarUsuarios');
        $router->post('/usuarios/excluir/{id}', 'APIUsuarioController@deletarUsuario');
        
        // Rota específica do cliente web (perfil)
        $router->post('/api/cliente/atualizar-perfil', 'UsuarioController@atualizarMeuPerfil');
        $router->post('/api/cliente/avaliar', 'AvaliacaoController@salvarAvaliacao'); // Assumindo controller

        // Login
        $router->post('/login', 'UsuarioController@login');
        $router->post('/api/desktop/login', 'DesktopApiController@login');
        
        // Senha
        $router->post('/recuperar-senha/solicitar', 'UsuarioController@solicitarRecuperacaoSenha');
        $router->post('/recuperar-senha/processar', 'UsuarioController@processarRedefinicaoSenha');

        // AGENDAMENTOS
        $router->post('/agendamentos/salvar', 'PublicAgendamentoController@salvarAgendamentos'); 
        $router->post('/api/agendamentos/salvar', 'APIAgendamentoController@salvarAgendamento');
        $router->post('/agendamentos/deletar/{id}', 'AgendamentoController@deletarAgendamentos');

        // PAGAMENTOS
        $router->post('/pagamentos/salvar', 'PagamentoController@salvarPagamentos');
        $router->post('/api/pagamentos/salvar', 'APIPagamentoController@salvarPagamento');

        // CONTATO
        $router->post('/enviar-contato', 'ContatoController@processarFormulario');
        $router->post('/api/contato/enviar', 'APIContatoController@enviarMensagem');
        
        // Rotas extras manuais que estavam no index.php
        $router->post('/gerar-pix', 'ApiController@gerarPix');
    }
}