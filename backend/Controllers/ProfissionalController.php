<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Models\Usuario;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;
use App\Psico\Validadores\ProfissionalValidador;

class ProfissionalController {
    public $profissional;   
    public $db;
    public $usuario;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->profissional = new Profissional($this->db);
        $this->usuario = new Usuario($this->db);
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

    $id_usuario = (int)$_POST['id_usuario'];
    $especialidade = $_POST['especialidade'];
    $valor_consulta = (float)($_POST['valor_consulta'] ?? 0.0);
    $sinal_consulta = (float)($_POST['sinal_consulta'] ?? 0.0);
    $publico = isset($_POST['publico']) ? 1 : 0;
    $sobre = $_POST['sobre'] ?? null;
    $ordem_exibicao = (int)($_POST['ordem_exibicao'] ?? 6);

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
        $ordem_exibicao
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
        Redirect::redirecionarComMensagem("profissionais/criar", "error", "Erro ao criar o registro profissional.");
    }
}

public function atualizarProfissionais($id) {
        $profissional = $this->profissional->buscarProfissionalPorId((int)$id);

        if (!$profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "error", "Profissional não encontrado para atualização.");
            return;
        }

        // --- ATUALIZAÇÃO DO USUÁRIO ---
        // (Validação do usuário pode ser adicionada aqui se necessário)
        $sucesso_usuario = $this->usuario->atualizarUsuario(
            (int)$_POST['id_usuario'], // Usa o id_usuario vindo do formulário (hidden)
            $_POST['nome_usuario'],
            $_POST['email_usuario'],
            $_POST['senha_usuario'] ?? null, // Senha opcional
            'profissional', // Garante que continua profissional
            $profissional->cpf ?? '', // Mantém CPF original (não editável aqui)
            $_POST['status_usuario'] ?? 'ativo' // Pega status do select
        );

        // --- ATUALIZAÇÃO DO PROFISSIONAL ---
        // (Validação dos dados do profissional pode ser adicionada aqui)
        $especialidadeInput = $_POST['especialidade'] ?? '';
        $valor_consulta = (float)($_POST['valor_consulta'] ?? 0);
        $sinal_consulta = (float)($_POST['sinal_consulta'] ?? 0);
        $publico = isset($_POST['publico']) ? 1 : 0; // Verifica se o checkbox foi marcado
        $sobre = $_POST['sobre'] ?? null;
        $ordem_exibicao = (int)($_POST['ordem_exibicao'] ?? 99);

        // --- Processamento da Especialidade ---
        $especialidadeTrimmed = trim($especialidadeInput);
        // Substitui uma ou mais quebras de linha por ", "
        $especialidadeProcessed = preg_replace('/(\r\n|\n|\r)+/', ', ', $especialidadeTrimmed);
        // Remove espaços extras ao redor das vírgulas e vírgulas duplicadas
        $especialidadeProcessed = preg_replace('/[ ,]*,[ ,]*/', ',', $especialidadeProcessed);
        // Remove vírgulas ou espaços no início/fim que podem ter sobrado
        $especialidadeProcessed = trim($especialidadeProcessed, ', ');
        // --- Fim do Processamento ---

        $sucesso_profissional = $this->profissional->atualizarProfissional(
            (int)$id, // ID do profissional vindo da URL
            $especialidadeProcessed, // <<< USA A VARIÁVEL PROCESSADA
            $valor_consulta,
            $sinal_consulta,
            $publico,
            $sobre,
            $ordem_exibicao
        );

        // --- VERIFICAÇÃO E REDIRECIONAMENTO ---
        if ($sucesso_usuario && $sucesso_profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "success", "Profissional atualizado com sucesso!");
        } else {
             // Monta mensagem de erro mais específica, se possível
             $erros = [];
             if (!$sucesso_usuario) $erros[] = "Erro ao atualizar dados do usuário.";
             if (!$sucesso_profissional) $erros[] = "Erro ao atualizar dados do profissional.";
             $mensagemErro = implode(" ", $erros);
             if (empty($mensagemErro)) $mensagemErro = "Erro desconhecido ao atualizar profissional."; // Fallback

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

}