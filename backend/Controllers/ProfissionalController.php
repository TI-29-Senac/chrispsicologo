<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Models\Usuario;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\ProfissionalValidador;
use App\Psico\Controllers\Admin\AuthenticatedController;
use App\Psico\Core\FileManager;
use PDO;

class ProfissionalController extends AuthenticatedController {
    public $profissional;   
    public $db;
    public $usuario;
    public $fileManager;

    public function __construct(){
        parent::__construct();
        $this->db = Database::getInstance();
        $this->profissional = new Profissional($this->db);
        $this->usuario = new Usuario($this->db);
        $this->fileManager = new FileManager(__DIR__ . '/../../');
    }

public function viewCriarProfissionais(){
    $this->verificarAcesso(['admin', 'profissional']);
    $usuariosDisponiveis = $this->usuario->buscarUsuariosNaoProfissionais();

    View::render('profissional/create', [
        'usuariosDisponiveis' => $usuariosDisponiveis
    ]);
    }

    public function viewListarProfissionais(){
        $this->verificarAcesso(['admin', 'profissional']);
        $pagina = $_GET['pagina'] ?? 1;
        $dadosPaginados = $this->profissional->paginacao((int)$pagina, 10);
        
        $todosProfissionais = $this->profissional->listarProfissionais();
        $totalProfissionais = count($todosProfissionais);
        $profissionaisAtivos = 0;
        $especialidades = [];

        foreach ($todosProfissionais as $profissional) {
            if (isset($profissional->status_usuario) && $profissional->status_usuario === 'ativo') {
                $profissionaisAtivos++;
            }
            if (!empty($profissional->especialidade)) {
                $especialidades[] = $profissional->especialidade;
            }
        }

        $profissionaisInativos = $totalProfissionais - $profissionaisAtivos;
        $especialidadesUnicas = count(array_unique($especialidades));

        $stats = [
            ['label' => 'Total de Profissionais', 'value' => $totalProfissionais, 'icon' => 'fa-user-md'],
            ['label' => 'Profissionais Ativos', 'value' => $profissionaisAtivos, 'icon' => 'fa-check-circle'],
            ['label' => 'Profissionais Inativos', 'value' => $profissionaisInativos, 'icon' => 'fa-times-circle'],
            ['label' => 'Especialidades Únicas', 'value' => $especialidadesUnicas, 'icon' => 'fa-briefcase']
        ];

        View::render('profissional/index', [
            'profissionais' => $dadosPaginados['data'],
            'paginacao' => $dadosPaginados,
            'stats' => $stats
        ]);
    }

    public function viewEditarProfissionais($id) {
        $this->verificarAcesso(['admin']);
        $profissional = $this->profissional->buscarProfissionalPorId((int)$id);
        if (!$profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "error", "Profissional não encontrado.");
            return;
        }
        View::render("profissional/edit", ["usuario" => $profissional]);
    }



    public function viewExcluirProfissionais($id) {
        $this->verificarAcesso(['admin']);
        $profissional = $this->profissional->buscarProfissionalPorId((int)$id);
        if (!$profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "error", "Profissional não encontrado para exclusão.");
            return;
        }
        View::render("profissional/delete", ["usuario" => $profissional]);
    }

    public function deletarProfissionais($id) {
        $profissional = $this->profissional->buscarProfissionalPorId((int)$id);

        if (!$profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "error", "Profissional não encontrado para exclusão.");
            return;
        }
        $sucesso_prof = $this->profissional->deletarProfissional((int)$id);

        if ($sucesso_prof) {
            if (isset($profissional->id_usuario)) {
                $this->usuario->excluirUsuario((int)$profissional->id_usuario);
            }
            Redirect::redirecionarComMensagem("profissionais/listar", "success", "Profissional excluído e usuário inativado com sucesso.");
        } else {
            Redirect::redirecionarComMensagem("profissionais/listar", "error", "Erro ao excluir o registro do profissional.");
        }
    }

    public function salvarProfissionais()
    {
        $erros = ProfissionalValidador::ValidarEntradas($_POST);
        if (!empty($erros)) {
            Redirect::redirecionarComMensagem("profissionais/criar", "error", implode("<br>", $erros));
            return;
        }

        $caminhoImagemSalva = null; // Inicializa como nulo

        // --- Processamento do Upload ---
        if (isset($_FILES['img_profissional']) && $_FILES['img_profissional']['error'] == UPLOAD_ERR_OK) {
            try {
            
                $caminhoImagemSalva = $this->fileManager->salvarArquivo(
                    $_FILES['img_profissional'],
                    'img/profissionais', 
                    ['image/jpeg', 'image/png', 'image/webp'], 
                    2 * 1024 * 1024 
                );
            } catch (\Exception $e) {
                // Se o upload falhar, redireciona com o erro
                Redirect::redirecionarComMensagem("profissionais/criar", "error", "Erro no upload da imagem: " . $e->getMessage());
                return;
            }
        }
        // --- Fim do Processamento do Upload ---

        $id_usuario = (int)$_POST['id_usuario'];
        $especialidade = $_POST['especialidade'];
        $valor_consulta = (float)($_POST['valor_consulta'] ?? 0.0);
        $sinal_consulta = (float)($_POST['sinal_consulta'] ?? 0.0);
        $publico = isset($_POST['publico']) ? 1 : 0;
        $sobre = $_POST['sobre'] ?? null;
        $ordem_exibicao = (int)($_POST['ordem_exibicao'] ?? 99);

        $usuarioExistente = $this->usuario->buscarUsuarioPorId($id_usuario);
        if (!$usuarioExistente) {
            Redirect::redirecionarComMensagem("profissionais/criar", "error", "O ID de usuário informado não existe.");
            return;
        }

        $id_profissional = $this->profissional->inserirProfissional(
            $id_usuario,
            $especialidade,
            $valor_consulta,
            $sinal_consulta,
            $publico,
            $sobre,
            $ordem_exibicao,
            $caminhoImagemSalva 
        );

        if ($id_profissional) {
            $this->usuario->atualizarUsuario(
                $id_usuario,
                $usuarioExistente->nome_usuario,
                $usuarioExistente->email_usuario,
                null,
                'profissional',
                $usuarioExistente->cpf ?? '',
                'ativo'
            );
            Redirect::redirecionarComMensagem("profissionais/listar", "success", "Profissional criado com sucesso!");
        } else {
            // Se falhou ao inserir no DB, tenta remover a imagem que foi salva (se houver)
            if ($caminhoImagemSalva) {
                $this->fileManager->delete($caminhoImagemSalva);
            }
            Redirect::redirecionarComMensagem("profissionais/criar", "error", "Erro ao criar o registro profissional no banco de dados.");
        }
    }

// --- Método atualizarProfissionais ATUALIZADO para UPLOAD ---
    public function atualizarProfissionais($id) {
        $profissional = $this->profissional->buscarProfissionalPorId((int)$id);

        if (!$profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "error", "Profissional não encontrado para atualização.");
            return;
        }

        // --- ATUALIZAÇÃO DO USUÁRIO ---
        // (Validação pode ser adicionada)
        $sucesso_usuario = $this->usuario->atualizarUsuario(
            (int)$_POST['id_usuario'],
            $_POST['nome_usuario'],
            $_POST['email_usuario'],
            $_POST['senha_usuario'] ?? null,
            'profissional',
            $profissional->cpf ?? '', // Mantém CPF
            $_POST['status_usuario'] ?? 'ativo'
        );

        // --- Processamento do Upload da Nova Imagem (se houver) ---
        $caminhoNovaImagem = null;
        $imagemAntiga = $_POST['imagem_atual'] ?? null; // Pega do campo hidden

        if (isset($_FILES['img_profissional']) && $_FILES['img_profissional']['error'] == UPLOAD_ERR_OK) {
             try {
                $caminhoNovaImagem = $this->fileManager->salvarArquivo(
                    $_FILES['img_profissional'],
                    'img/profissionais',
                    ['image/jpeg', 'image/png', 'image/webp'],
                    2 * 1024 * 1024
                );
            } catch (\Exception $e) {
                Redirect::redirecionarComMensagem("profissionais/editar/{$id}", "error", "Erro no upload da nova imagem: " . $e->getMessage());
                return;
            }
        }
 
        $caminhoImagemParaSalvar = $caminhoNovaImagem ?? $imagemAntiga;

        $especialidadeInput = $_POST['especialidade'] ?? '';
        $valor_consulta = (float)($_POST['valor_consulta'] ?? 0);
        $sinal_consulta = (float)($_POST['sinal_consulta'] ?? 0);
        $publico = isset($_POST['publico']) ? 1 : 0;
        $sobre = $_POST['sobre'] ?? null;
        $ordem_exibicao = (int)($_POST['ordem_exibicao'] ?? 99);

        // Processamento da Especialidade (igual ao anterior)
        $especialidadeTrimmed = trim($especialidadeInput);
        $especialidadeProcessed = preg_replace('/(\r\n|\n|\r)+/', ', ', $especialidadeTrimmed);
        $especialidadeProcessed = preg_replace('/[ ,]*,[ ,]*/', ',', $especialidadeProcessed);
        $especialidadeProcessed = trim($especialidadeProcessed, ', ');

        $sucesso_profissional = $this->profissional->atualizarProfissional(
            (int)$id,
            $especialidadeProcessed,
            $valor_consulta,
            $sinal_consulta,
            $publico,
            $sobre,
            $ordem_exibicao,
            $caminhoImagemParaSalvar // <<< Passa o caminho final (novo ou antigo)
        );

        // --- VERIFICAÇÃO E REDIRECIONAMENTO ---
        if ($sucesso_usuario && $sucesso_profissional) {
             // Se a atualização foi bem-sucedida E uma nova imagem foi enviada, deleta a antiga (se existir)
             if ($caminhoNovaImagem && !empty($imagemAntiga) && $imagemAntiga !== $caminhoNovaImagem) {
                 $this->fileManager->delete($imagemAntiga);
             }
            Redirect::redirecionarComMensagem("profissionais/listar", "success", "Profissional atualizado com sucesso!");
        } else {
             // Se a atualização falhou, remove a nova imagem que pode ter sido salva
             if ($caminhoNovaImagem) {
                 $this->fileManager->delete($caminhoNovaImagem);
             }
             $erros = [];
             if (!$sucesso_usuario) $erros[] = "Erro ao atualizar dados do usuário.";
             if (!$sucesso_profissional) $erros[] = "Erro ao atualizar dados do profissional.";
             $mensagemErro = implode(" ", $erros);
             if (empty($mensagemErro)) $mensagemErro = "Erro desconhecido ao atualizar profissional.";

            Redirect::redirecionarComMensagem("profissionais/editar/{$id}", "error", $mensagemErro);
        }
    }

    public function viewMeuPerfilProfissional() {
        // 1. Garante que apenas um profissional logado acesse
        $this->verificarAcesso(['profissional']);

        // 2. Pega o ID do usuário da SESSÃO
        $id_usuario_logado = $_SESSION['usuario_id'] ?? null;

        // 3. Busca os dados de profissional associados a esse usuário
        $profissional = $this->profissional->buscarProfissionalPorUsuarioId((int)$id_usuario_logado);

        // --- INÍCIO DA VERIFICAÇÃO IMPORTANTE ---
        // Se $profissional for falso (não encontrado), redireciona para o dashboard
        // com uma mensagem de erro.
        if (!$profissional) {
            Redirect::redirecionarComMensagem("dashboard", "error", "Perfil profissional não encontrado ou incompleto. Contacte o administrador.");
            return; // Impede a renderização da página
        }
        // --- FIM DA VERIFICAÇÃO IMPORTANTE ---

        // 4. Renderiza a nova view (só chega aqui se $profissional for encontrado)
        View::render("profissional/meu-perfil", ["profissional" => $profissional]);
    }

    public function atualizarMeuPerfilProfissional() {
        // 1. Garante que apenas um profissional logado acesse
        $this->verificarAcesso(['profissional']);

        // 2. Pega o ID do usuário da SESSÃO (Fonte segura)
        $id_usuario_logado = $_SESSION['usuario_id'] ?? null;
        $profissionalAtual = $this->profissional->buscarProfissionalPorUsuarioId((int)$id_usuario_logado);

        // 3. Verifica se o profissional existe
        if (!$profissionalAtual) {
            Redirect::redirecionarComMensagem("dashboard", "error", "Não foi possível atualizar. Perfil profissional não encontrado.");
            return;
        }
        $id_profissional = $profissionalAtual->id_profissional;

        // 4. Processamento do Upload da Nova Imagem (se houver)
        $caminhoNovaImagem = null;
        $imagemAntiga = $profissionalAtual->img_profissional ?? null; 

        if (isset($_FILES['img_profissional']) && $_FILES['img_profissional']['error'] == UPLOAD_ERR_OK) {
             try {
                $caminhoNovaImagem = $this->fileManager->salvarArquivo(
                    $_FILES['img_profissional'],
                    'img/profissionais',
                    ['image/jpeg', 'image/png', 'image/webp'],
                    2 * 1024 * 1024 // 2MB Max
                );
            } catch (\Exception $e) {
                Redirect::redirecionarComMensagem("profissional/meu-perfil", "error", "Erro no upload da nova imagem: " . $e->getMessage());
                return;
            }
        }
 
        // 5. Decide qual caminho de imagem salvar no banco
        $caminhoImagemParaSalvar = $caminhoNovaImagem ?? $imagemAntiga;

        // 6. Pega os dados do formulário
        $valor_consulta = (float)($_POST['valor_consulta'] ?? $profissionalAtual->valor_consulta);
        $sinal_consulta = (float)($_POST['sinal_consulta'] ?? $profissionalAtual->sinal_consulta);
        $sobre = $_POST['sobre'] ?? $profissionalAtual->sobre;
        $especialidade = $_POST['especialidade'] ?? $profissionalAtual->especialidade;

        // 7. Atualiza usando o novo método seguro do Model
        $sucesso_profissional = $this->profissional->atualizarPerfilProfissional(
            (int)$id_profissional,
            $especialidade,
            $valor_consulta,
            $sinal_consulta,
            $sobre,
            $caminhoImagemParaSalvar
        );

        // 8. VERIFICAÇÃO E REDIRECIONAMENTO
        if ($sucesso_profissional) {
             // Se a atualização foi bem-sucedida E uma nova imagem foi enviada, deleta a antiga
             if ($caminhoNovaImagem && !empty($imagemAntiga) && $imagemAntiga !== $caminhoNovaImagem) {
                 $this->fileManager->delete($imagemAntiga);
             }
            Redirect::redirecionarComMensagem("profissional/meu-perfil", "success", "Perfil profissional atualizado com sucesso!");
        } else {
             // Se a atualização falhou, remove a nova imagem que pode ter sido salva
             if ($caminhoNovaImagem) {
                 $this->fileManager->delete($caminhoNovaImagem);
             }
            Redirect::redirecionarComMensagem("profissional/meu-perfil", "error", "Erro ao atualizar dados do profissional.");
        }
    }
    
}