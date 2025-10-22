<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Models\Usuario;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\ProfissionalValidador;
use App\Psico\Core\FileManager;
use PDO;

class ProfissionalController {
    public $profissional;   
    public $db;
    public $usuario;
    public $fileManager;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->profissional = new Profissional($this->db);
        $this->usuario = new Usuario($this->db);
        $this->fileManager = new FileManager(__DIR__ . '/../../');
    }

public function viewCriarProfissionais()
    {
        $usuariosDisponiveis = $this->usuario->buscarUsuariosNaoProfissionais();

        View::render('profissional/create', [
            'usuariosDisponiveis' => $usuariosDisponiveis
        ]);
    }

    public function viewListarProfissionais()
    {
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
        $profissional = $this->profissional->buscarProfissionalPorId((int)$id);
        if (!$profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "error", "Profissional não encontrado.");
            return;
        }
        View::render("profissional/edit", ["usuario" => $profissional]);
    }



    public function viewExcluirProfissionais($id) {
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
                // Tenta salvar o arquivo na pasta 'img/profissionais/'
                // Tipos permitidos: jpeg, png, webp. Tamanho máximo: 2MB (2 * 1024 * 1024 bytes)
                $caminhoImagemSalva = $this->fileManager->salvarArquivo(
                    $_FILES['img_profissional'],
                    'img/profissionais', // Subdiretório
                    ['image/jpeg', 'image/png', 'image/webp'], // Tipos permitidos
                    2 * 1024 * 1024 // Tamanho máximo (2MB)
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
            $caminhoImagemSalva // <<< Usa o caminho retornado pelo FileManager (ou null)
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
        // --- Fim do Processamento do Upload ---

        // Define qual caminho de imagem será salvo no banco
        // Se uma nova imagem foi enviada, usa o caminho dela. Senão, mantém o caminho antigo.
        $caminhoImagemParaSalvar = $caminhoNovaImagem ?? $imagemAntiga;

        // --- ATUALIZAÇÃO DO PROFISSIONAL ---
        // (Validação pode ser adicionada)
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


     public function listarPublico() {
        header('Content-Type: application/json');
        try {
            // Alterado para usar o novo método com o filtro de visibilidade
            $profissionais = $this->profissional->listarProfissionaisPublicos();

            http_response_code(200);
            echo json_encode($profissionais);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno ao buscar profissionais.', 'details' => $e->getMessage()]);
        }
    }

    // Removido horáriosPublico estático

    public function detalhePublico($id) {
        header('Content-Type: application/json');
        try {
            // Alterado para usar o novo método seguro
            $profissional = $this->profissional->buscarProfissionalPublicoPorId((int)$id);

            if (!$profissional) {
                http_response_code(404);
                echo json_encode(['error' => 'Profissional não encontrado ou não está disponível.']);
                return;
            }

            // Se encontrou, retorna os dados com sucesso
            http_response_code(200);
            echo json_encode($profissional);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno ao buscar detalhes do profissional.', 'details' => $e->getMessage()]);
        }
    }
    
    public function getCarrosselCardsHtml() {
        // Define os IDs corretos
        $idsProfissionaisCarrossel = [6, 7, 8, 9, 10]; // IDs corretos que funcionaram

        $htmlCards = '';
        $profissionaisParaCarrossel = [];

        foreach ($idsProfissionaisCarrossel as $id) {
            $prof = $this->profissional->buscarProfissionalPublicoPorId($id);
            if ($prof) {
                $profissionaisParaCarrossel[] = $prof;
            } else {
                error_log("Aviso: Profissional com ID {$id} não encontrado para o carrossel.");
            }
        }

        // --- Gera o HTML ---
        foreach ($profissionaisParaCarrossel as $profissional) {

            // ===== INÍCIO DA LÓGICA CORRETA PARA PEGAR A PRIMEIRA ESPECIALIDADE =====
            $especialidadeExibida = 'Clínica Geral'; // Define um padrão
            if (!empty($profissional->especialidade)) {
                // Remove espaços em branco do início e fim da string completa
                $especialidadesString = trim($profissional->especialidade);

                // Divide a string na PRIMEIRA vírgula encontrada, limitando a 2 partes
                $partes = explode(',', $especialidadesString, 2);

                // Pega a primeira parte (índice 0) ou uma string vazia se não houver nada
                $primeiraEspecialidade = $partes[0] ?? '';

                // Remove espaços em branco extras da primeira parte
                $primeiraEspecialidadeLimpa = trim($primeiraEspecialidade);

                // Usa a especialidade limpa APENAS se ela não ficou vazia após a limpeza
                if ($primeiraEspecialidadeLimpa !== '') {
                    $especialidadeExibida = $primeiraEspecialidadeLimpa;
                }
            }
            // ===== FIM DA LÓGICA CORRETA PARA PEGAR A PRIMEIRA ESPECIALIDADE =====


            // Define a URL da foto (sem alterações)
            $nomeBase = explode(' ', $profissional->nome_usuario)[0];
            $fotoUrlPadrao = "/img/profissionais/" . strtolower($nomeBase) . ".png";
            $fotoFinal = (!empty($profissional->img_profissional)) ? "/" . ltrim($profissional->img_profissional, '/') : $fotoUrlPadrao;

            // Gera o HTML do card (sem alterações)
            $htmlCards .= '
            <div class="card" data-id-profissional="'.htmlspecialchars($profissional->id_profissional).'">
              <a href="profissionais.html?id='.htmlspecialchars($profissional->id_profissional).'" class="card-link">
                <div class="foto" style="background-image: url(\''.htmlspecialchars($fotoFinal).'\'); background-size: cover; background-position: center;">
                </div>
                <h3>'.htmlspecialchars($profissional->nome_usuario).'</h3>
                <div class="avaliacoes">
                  <h4>Psicólogo(a)</h4>
                  <p>'.htmlspecialchars($especialidadeExibida).'</p> </div>
              </a>
            </div>
            ';
        }

        // Envia apenas o HTML dos 5 cards (sem duplicação no PHP)
        $htmlCompleto = $htmlCards;

        header('Content-Type: text/html; charset=utf-8');
        echo $htmlCompleto;
        exit;
    }
}