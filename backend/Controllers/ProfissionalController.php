<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Models\Usuario;
use App\Psico\Database\Database;
use App\Psico\Core\View;
use App\Psico\Core\Redirect;

class ProfissionalController {
    public $profissional;   
    public $db;
    public $usuario;

    public function __construct(){
        $this->db = Database::getInstance();
        $this->profissional = new Profissional($this->db);
        $this->usuario = new Usuario($this->db);
    }

    public function viewListarProfissionais()
    {
        $pagina = $_GET['pagina'] ?? 1;
        $dadosPaginados = $this->profissional->paginacao((int)$pagina, 10);
        
        $todosProfissionais = $this->profissional->listarProfissionais();
        $totalProfissionais = count($todosProfissionais);
        $profissionaisAtivos = 0;
        $especialidades = [];

        // --- CORREÇÃO ESTÁ AQUI ---
        // Trocamos a sintaxe de array '[]' para a sintaxe de objeto '->'
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

    public function viewCriarProfissionais(){
        View::render("profissional/create");
    }

    public function salvarProfissionais(){
        
        $this->profissional->inserirProfissional(
            (int)$_POST["id_usuario"],
            $_POST["especialidade"],
            $_POST["img_profissional"] ?? '', 
            (float)($_POST["valor_consulta"] ?? 0),
            (float)($_POST["sinal_consulta"] ?? 0)
        );

        Redirect::redirecionarComMensagem("profissionais/listar","success","Profissional criado com sucesso!");
    }

    public function deletarProfissionais(){
        echo "Deletar Profissionais";
    }
    public function viewEditarProfissionais(){
        echo "Editar Profissionais";
    }
}

