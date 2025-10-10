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
        $profissionais = $this->profissional->listarProfissionais();

        // --- LÓGICA DE STATS COMPLETA ---
        $totalProfissionais = count($profissionais);
        $profissionaisAtivos = 0;
        $especialidades = []; // Array para guardar as especialidades

        foreach ($profissionais as $profissional) {
            // Conta profissionais ativos
            if (isset($profissional['status_usuario']) && $profissional['status_usuario'] === 'ativo') {
                $profissionaisAtivos++;
            }
            // Coleta todas as especialidades
            if (!empty($profissional['especialidade'])) {
                $especialidades[] = $profissional['especialidade'];
            }
        }

        $profissionaisInativos = $totalProfissionais - $profissionaisAtivos;
        
        // --- CÁLCULO DAS ESPECIALIDADES ÚNICAS ---
        $especialidadesUnicas = count(array_unique($especialidades));

        // --- ARRAY DE STATS ATUALIZADO ---
        $stats = [
            [
                'label' => 'Total de Profissionais',
                'value' => $totalProfissionais,
                'icon' => 'fa-user-md'
            ],
            [
                'label' => 'Profissionais Ativos',
                'value' => $profissionaisAtivos,
                'icon' => 'fa-check-circle'
            ],
            [
                'label' => 'Profissionais Inativos',
                'value' => $profissionaisInativos,
                'icon' => 'fa-times-circle'
            ],
            [
                'label' => 'Especialidades Únicas',
                'value' => $especialidadesUnicas,
                'icon' => 'fa-briefcase' // Ícone mais apropriado
            ]
        ];

        View::render('profissional/index', [
            'profissionais' => $profissionais,
            'stats' => $stats
        ]);
    }
    
    // Demais métodos do controller...
    public function viewCriarProfissionais(){
        View::render("profissional/create");
    }

    public function salvarProfissionais(){
        // Validação dos dados de entrada...
        
        $this->profissional->inserirProfissional(
            (int)$_POST["id_usuario"],
            $_POST["especialidade"],
            $_POST["img_profissional"] ?? '', 
            (float)($_POST["valor_consulta"] ?? 0),
            (float)($_POST["sinal_consulta"] ?? 0)
        );

        Redirect::redirecionarComMensagem("profissionais/listar", "success", "Profissional atualizado com sucesso!");
    }

    public function deletarProfissionais(){
        echo "Deletar Profissionais";
    }
    public function viewEditarProfissionais(){
        echo "Editar Profissionais";
    }
}

