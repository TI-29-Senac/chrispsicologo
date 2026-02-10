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
            // Lista de SUFIXOS de rotas públicas (mais seguro que caminho absoluto)
            $publicSuffixes = [
                '/api/desktop/login',
                '/api/desktop/refresh-token',
                '/api/desktop/logout',
                '/api/contato/enviar',
                '/api/imagens/quem-somos',
                '/api/imagens/servicos'
            ];
            $currentUri = $_SERVER['REQUEST_URI'];
            
            // 1. Verifica se a rota atual TERMINA com algum dos sufixos públicos
            // Isso previne erros se o site estiver em subpastas (ex: /backend/api/...)
            foreach ($publicSuffixes as $suffix) {
                if (strpos($currentUri, $suffix) !== false) {
                    return; // É pública, libera passagem
                }
            }
            // 2. Se não for pública, exige Token JWT
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
        
        // Rota específica do cliente web (perfil)
        $router->get('/api/cliente/meus-agendamentos', 'APIAgendamentoController@buscarMeusAgendamentos');
        $router->get('/api/cliente/meu-perfil', 'APIUsuarioController@getMeuPerfil');
        $router->get('/api/cliente/financeiro', 'APIPagamentoController@listarFinanceiro');
        // AGENDAMENTOS
        $router->get('/agendamentos', 'AgendamentoController@index');
        $router->get('/agendamentos/listar', 'AgendamentoController@viewListarAgendamentos');
        $router->get('/api/agendamentos', 'APIAgendamentoController@getAgendamentos');
        $router->get('/agendamentos/disponibilidade/{id}/{data}', 'PublicAgendamentoController@buscarDisponibilidade');
        $router->get('/agendamentos/detalhe-pagamento/{id}', 'PublicAgendamentoController@getDetalhesPagamento');
        // PROFISSIONAIS (Admin)
        $router->get('/profissionais/listar', 'ProfissionalController@viewListarProfissionais');
        $router->get('/profissionais/criar', 'ProfissionalController@viewCriarProfissionais');
        $router->get('/profissionais/editar/{id}', 'ProfissionalController@viewEditarProfissionais');
        $router->get('/profissionais/excluir/{id}', 'ProfissionalController@viewExcluirProfissionais');
        $router->post('/profissionais/salvar', 'ProfissionalController@salvarProfissionais');
        $router->post('/profissionais/atualizar/{id}', 'ProfissionalController@atualizarProfissionais');
        $router->post('/profissionais/deletar/{id}', 'ProfissionalController@deletarProfissionais');
        // Perfil do Profissional (Logado)
        $router->get('/profissional/meu-perfil', 'ProfissionalController@viewMeuPerfilProfissional');
        $router->post('/profissional/atualizar-meu-perfil', 'ProfissionalController@atualizarMeuPerfilProfissional');
        // PROFISSIONAIS (Público/API)
        $router->get('/profissionais/listar-publico', 'PublicProfissionalController@listarPublico');
        $router->get('/profissionais/detalhe/{id}', 'PublicProfissionalController@detalhePublico');
        $router->get('/avaliacoes', 'PublicProfissionalController@buscarAvaliacoes');
        $router->get('/api/profissionais', 'APIProfissionalController@getProfissionais');
        // AVALIAÇÕES (Admin)
        $router->get('/avaliacoes/listar', 'AvaliacaoController@viewListarAvaliacoes');
        $router->get('/avaliacoes/criar', 'AvaliacaoController@viewCriarAvaliacoes');
        $router->get('/avaliacoes/editar/{id}', 'AvaliacaoController@viewEditarAvaliacoes');
        $router->get('/avaliacoes/excluir/{id}', 'AvaliacaoController@viewExcluirAvaliacoes');
        $router->post('/avaliacoes/salvar', 'AvaliacaoController@salvarAvaliacoes');
        $router->post('/avaliacoes/atualizar/{id}', 'AvaliacaoController@atualizarAvaliacoes');
        $router->post('/avaliacoes/deletar/{id}', 'AvaliacaoController@deletarAvaliacoes');
        // IMAGENS (Admin)
        $router->get('/imagens/listar', 'ImagemController@viewListarImagens');
        $router->get('/imagens/criar', 'ImagemController@viewCriarImagem');
        $router->get('/imagens/editar/{id}', 'ImagemController@viewEditarImagem');
        $router->get('/imagens/excluir/{id}', 'ImagemController@viewExcluirImagem');
        $router->post('/imagens/salvar', 'ImagemController@salvarImagem');
        $router->post('/imagens/atualizar/{id}', 'ImagemController@atualizarImagem');
        $router->post('/imagens/deletar/{id}', 'ImagemController@deletarImagem');
        $router->get('/api/imagens/secoes/{id}', 'ImagemController@buscarSecoesPorPaginaApi');
        
        // IMAGENS (Público/API)
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
        $router->post('/api/cliente/avaliar', 'AvaliacaoController@salvarAvaliacaoCliente');
        // Login
        $router->post('/login', 'UsuarioController@login');
        
        // ROTAS DO DESKTOP (Atualizado)
        $router->post('/api/desktop/login', 'DesktopApiController@login');
        $router->post('/api/desktop/refresh-token', 'DesktopApiController@refreshToken');
        $router->post('/api/desktop/logout', 'DesktopApiController@logout');

        // Rotas de Sincronização Desktop
        // Usuarios
        $router->get('/api/desktop/usuarios/listar', 'DesktopApiController@listarUsuarios');
        $router->post('/api/desktop/usuarios/salvar', 'DesktopApiController@criarUsuario');
        $router->post('/api/desktop/usuarios/editar/{id}', 'DesktopApiController@editarUsuario');
        $router->post('/api/desktop/usuarios/excluir/{id}', 'DesktopApiController@excluirUsuario');
        
        // Agendamentos
        $router->get('/api/desktop/agendamentos/listar', 'DesktopApiController@listarAgendamentos');
        $router->post('/api/desktop/agendamentos/salvar', 'DesktopApiController@criarAgendamento');
        $router->post('/api/desktop/agendamentos/editar/{id}', 'DesktopApiController@editarAgendamento');
        $router->post('/api/desktop/agendamentos/excluir/{id}', 'DesktopApiController@excluirAgendamento');
        
        // Pagamentos
        $router->get('/api/desktop/pagamentos/listar', 'DesktopApiController@listarPagamentos');
        $router->post('/api/desktop/pagamentos/salvar', 'DesktopApiController@criarPagamento');
        $router->post('/api/desktop/pagamentos/editar/{id}', 'DesktopApiController@editarPagamento');
        $router->post('/api/desktop/pagamentos/excluir/{id}', 'DesktopApiController@excluirPagamento');

        
        // Senha
        $router->post('/recuperar-senha/solicitar', 'UsuarioController@solicitarRecuperacaoSenha');
        $router->post('/recuperar-senha/processar', 'UsuarioController@processarRedefinicaoSenha');
        // AGENDAMENTOS
        $router->post('/agendamentos/salvar', 'PublicAgendamentoController@salvarAgendamentos'); 
        $router->post('/api/agendamentos/salvar', 'APIAgendamentoController@salvarAgendamento');
        $router->post('/agendamentos/confirmar-sinal/{id}', 'PublicAgendamentoController@confirmarSinal'); 
        $router->post('/agendamentos/deletar/{id}', 'AgendamentoController@deletarAgendamentos');
        // PAGAMENTOS (Admin)
        $router->get('/pagamentos/listar', 'PagamentoController@viewListarPagamentos');
        $router->get('/pagamentos/criar', 'PagamentoController@viewCriarPagamentos');
        $router->get('/pagamentos/editar/{id}', 'PagamentoController@viewEditarPagamentos');
        $router->get('/pagamentos/excluir/{id}', 'PagamentoController@viewExcluirPagamentos');
        $router->post('/pagamentos/atualizar/{id}', 'PagamentoController@atualizarPagamento');
        $router->post('/pagamentos/deletar/{id}', 'PagamentoController@deletarPagamento');
        // PAGAMENTOS (Salvar)
        $router->post('/pagamentos/salvar', 'PagamentoController@salvarPagamentos');
        $router->post('/api/pagamentos/salvar', 'APIPagamentoController@salvarPagamento');
        // CONTATO
        $router->post('/enviar-contato', 'ContatoController@processarFormulario');
        $router->post('/api/contato/enviar', 'APIContatoController@enviarMensagem');
        
        // Rotas extras manuais que estavam no index.php
        $router->post('/gerar-pix', 'ApiController@gerarPix');
    }
}