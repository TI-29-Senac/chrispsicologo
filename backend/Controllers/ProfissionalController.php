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
        View::render('profissional/create');
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

        $usuarioExistente = $this->usuario->buscarUsuarioPorId($id_usuario);
        if (!$usuarioExistente) {
            Redirect::redirecionarComMensagem("profissionais/criar", "error", "O ID de usuário informado não existe.");
            return;
        }

        $id_profissional = $this->profissional->inserirProfissional(
            $id_usuario,
            $especialidade,
            $valor_consulta,
            $sinal_consulta
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

    public function atualizarProfissionais($id) {
        $profissional = $this->profissional->buscarProfissionalPorId((int)$id);
        
        if (!$profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "error", "Profissional não encontrado para atualização.");
            return;
        }

        $sucesso_usuario = $this->usuario->atualizarUsuario(
            (int)$_POST['id_usuario'],
            $_POST['nome_usuario'],
            $_POST['email_usuario'],
            $_POST['senha_usuario'] ?? null,
            'profissional',
            $profissional->cpf ?? '',
            $_POST['status_usuario'] ?? 'ativo'
        );

        // --- CORREÇÃO APLICADA AQUI ---
        // Converte os valores do formulário para float antes de passar para o Model.
        $sucesso_profissional = $this->profissional->atualizarProfissional(
            (int)$id,
            $_POST['especialidade'],
            (float)($_POST['valor_consulta'] ?? 0), 
            (float)($_POST['sinal_consulta'] ?? 0)
        );

        if ($sucesso_usuario && $sucesso_profissional) {
            Redirect::redirecionarComMensagem("profissionais/listar", "success", "Profissional atualizado com sucesso!");
        } else {
            Redirect::redirecionarComMensagem("profissionais/editar/{$id}", "error", "Erro ao atualizar profissional.");
        }
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
}