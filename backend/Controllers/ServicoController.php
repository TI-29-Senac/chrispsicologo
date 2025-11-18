<?php
namespace App\Psico\Controllers;

// Modelos e DB
use App\Psico\Models\Servico; // <<< Alterado
use App\Psico\Database\Database;
// Core
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Core\FileManager;
// Validadores
use App\Psico\Validadores\ServicoValidador; // <<< Alterado
// Base
use App\Psico\Controllers\Admin\AuthenticatedController;
use PDO;

class ServicoController extends AuthenticatedController {
    
    public $servico; // <<< Alterado
    public $db;
    public $fileManager;

    public function __construct(){
        parent::__construct(); // Mantém a verificação de login
        $this->db = Database::getInstance();
        $this->servico = new Servico($this->db); // <<< Alterado
        // O __DIR__ sobe 2 níveis (de Controllers/ para backend/) e depois desce para a raiz
        $this->fileManager = new FileManager(dirname(__DIR__, 2) . '/'); 
    }

    /**
     * Lista todos os serviços com paginação e estatísticas.
     */
    public function viewListarServicos(){
        $this->verificarAcesso(['admin']); // Apenas admin pode gerenciar serviços
        
        $pagina = $_GET['pagina'] ?? 1;
        $dadosPaginados = $this->servico->paginacao((int)$pagina, 10);
        
        $todosServicos = $this->servico->buscarTodosServicos();
        $totalServicos = $dadosPaginados['total']; // Mais eficiente
        $servicosAtivos = 0;

        foreach ($todosServicos as $servico) {
            if (isset($servico->ativo) && $servico->ativo == 1) {
                $servicosAtivos++;
            }
        }

        $servicosInativos = $totalServicos - $servicosAtivos;

        $stats = [
            ['label' => 'Total de Serviços', 'value' => $totalServicos, 'icon' => 'fa-briefcase'],
            ['label' => 'Serviços Ativos', 'value' => $servicosAtivos, 'icon' => 'fa-check-circle'],
            ['label' => 'Serviços Inativos', 'value' => $servicosInativos, 'icon' => 'fa-times-circle']
        ];

        View::render('servico/index', [
            'servicos' => $dadosPaginados['data'], // <<< Alterado
            'paginacao' => $dadosPaginados,
            'stats' => $stats
        ]);
    }

    /**
     * Exibe o formulário de criação de serviço.
     */
    public function viewCriarServicos(){
        $this->verificarAcesso(['admin']);
        // Não precisa de dados extras (como usuários ou tipos)
        View::render('servico/create', []); // <<< Alterado
    }

    /**
     * Salva um novo serviço no banco de dados.
     */
    public function salvarServicos() {
        $this->verificarAcesso(['admin']);

        // Validação (usando o novo ServicoValidador)
        $erros = ServicoValidador::ValidarEntradas($_POST, false); // false = não é update
        if (!empty($erros)) {
            Redirect::redirecionarComMensagem("servicos/criar", "error", implode("<br>", $erros));
            return;
        }

        // Validação do Ícone (obrigatório na criação)
        if (!isset($_FILES['icone_path']) || $_FILES['icone_path']['error'] != UPLOAD_ERR_OK) {
             Redirect::redirecionarComMensagem("servicos/criar", "error", "O campo 'Ícone SVG/Imagem' é obrigatório.");
             return;
        }

        $caminhoIconeSalvo = null; 

        // --- Processamento do Upload ---
        try {
            $caminhoIconeSalvo = $this->fileManager->salvarArquivo(
                $_FILES['icone_path'],
                'img/icons', // Salva na pasta de ícones
                ['image/svg+xml', 'image/png', 'image/jpeg', 'image/webp'], // Permite SVG
                1 * 1024 * 1024 // Limite de 1MB para ícones
            );
        } catch (\Exception $e) {
            Redirect::redirecionarComMensagem("servicos/criar", "error", "Erro no upload do ícone: " . $e->getMessage());
            return;
        }
        
        // --- Fim do Processamento do Upload ---

        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $ativo = isset($_POST['ativo']) ? 1 : 0; // Checkbox

        $id_servico = $this->servico->inserirServico(
            $titulo,
            $descricao,
            $caminhoIconeSalvo, // Caminho relativo salvo (ex: 'img/icons/hash.svg')
            $ativo
        );

        if ($id_servico) {
            Redirect::redirecionarComMensagem("servicos/listar", "success", "Serviço criado com sucesso!");
        } else {
            // Se falhou ao inserir no DB, remove o ícone que foi salvo
            if ($caminhoIconeSalvo) {
                $this->fileManager->delete($caminhoIconeSalvo);
            }
            Redirect::redirecionarComMensagem("servicos/criar", "error", "Erro ao criar o serviço no banco de dados.");
        }
    }

    /**
     * Exibe o formulário de edição de serviço.
     */
    public function viewEditarServicos($id) {
        $this->verificarAcesso(['admin']);
        $servico = $this->servico->buscarServicoPorId((int)$id);
        if (!$servico) {
            Redirect::redirecionarComMensagem("servicos/listar", "error", "Serviço não encontrado.");
            return;
        }

        View::render("servico/edit", [
            "servico" => $servico // <<< Alterado
        ]);
    }

    /**
     * Atualiza um serviço existente no banco de dados.
     */
    public function atualizarServicos($id) {
        $this->verificarAcesso(['admin']);
        
        $servicoAtual = $this->servico->buscarServicoPorId((int)$id);
        if (!$servicoAtual) {
            Redirect::redirecionarComMensagem("servicos/listar", "error", "Serviço não encontrado para atualização.");
            return;
        }

        // Validação
        $erros = ServicoValidador::ValidarEntradas($_POST, true); // true = é update
        if (!empty($erros)) {
            Redirect::redirecionarComMensagem("servicos/editar/{$id}", "error", implode("<br>", $erros));
            return;
        }

        // --- Processamento do Upload (se houver novo ícone) ---
        $caminhoNovoIcone = null;
        $iconeAntigo = $_POST['icone_atual'] ?? $servicoAtual->icone_path; 

        if (isset($_FILES['icone_path']) && $_FILES['icone_path']['error'] == UPLOAD_ERR_OK) {
             try {
                $caminhoNovoIcone = $this->fileManager->salvarArquivo(
                    $_FILES['icone_path'],
                    'img/icons',
                    ['image/svg+xml', 'image/png', 'image/jpeg', 'image/webp'],
                    1 * 1024 * 1024 // 1MB Max
                );
            } catch (\Exception $e) {
                Redirect::redirecionarComMensagem("servicos/editar/{$id}", "error", "Erro no upload do novo ícone: " . $e->getMessage());
                return;
            }
        }
 
        $caminhoParaSalvar = $caminhoNovoIcone ?? $iconeAntigo;

        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $ativo = isset($_POST['ativo']) ? 1 : 0;

        $sucesso = $this->servico->atualizarServico(
            (int)$id,
            $titulo,
            $descricao,
            $caminhoParaSalvar,
            $ativo
        );

        if ($sucesso) {
             // Se atualizou E um novo ícone foi enviado, deleta o antigo
             if ($caminhoNovoIcone && !empty($iconeAntigo) && $iconeAntigo !== $caminhoNovoIcone) {
                 $this->fileManager->delete($iconeAntigo);
             }
            Redirect::redirecionarComMensagem("servicos/listar", "success", "Serviço atualizado com sucesso!");
        } else {
             // Se falhou, deleta o novo ícone (se foi salvo)
             if ($caminhoNovoIcone) {
                 $this->fileManager->delete($caminhoNovoIcone);
             }
            Redirect::redirecionarComMensagem("servicos/editar/{$id}", "error", "Erro ao atualizar o serviço.");
        }
    }

    /**
     * Exibe a página de confirmação de exclusão.
     */
    public function viewExcluirServicos($id) {
        $this->verificarAcesso(['admin']);
        $servico = $this->servico->buscarServicoPorId((int)$id);
        if (!$servico) {
            Redirect::redirecionarComMensagem("servicos/listar", "error", "Serviço não encontrado para exclusão.");
            return;
        }
        View::render("servico/delete", ["servico" => $servico]); // <<< Alterado
    }

    /**
     * Deleta (soft delete) um serviço.
     */
    public function deletarServicos($id) {
        $this->verificarAcesso(['admin']);
        
        $servico = $this->servico->buscarServicoPorId((int)$id);
        if (!$servico) {
            Redirect::redirecionarComMensagem("servicos/listar", "error", "Serviço não encontrado.");
            return;
        }
        
        // Executa o soft delete
        $sucesso = $this->servico->deletarServico((int)$id);

        if ($sucesso) {
            Redirect::redirecionarComMensagem("servicos/listar", "success", "Serviço movido para a lixeira (excluído).");
        } else {
            Redirect::redirecionarComMensagem("servicos/listar", "error", "Erro ao excluir o serviço.");
        }
    }
}