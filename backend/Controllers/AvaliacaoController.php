<?php
namespace App\Psico\Controllers;

use App\Psico\Core\View;
use App\Psico\Models\Avaliacao;
use App\Psico\Database\Database;

class AvaliacaoController
{
    public $avaliacao;
    public $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->avaliacao = new Avaliacao($this->db);
    }

    public function viewListarAvaliacoes()
    {
        $avaliacoes = $this->avaliacao->buscarAvaliacoes();
        $totalAvaliacoes = 0;
        $somaNotas = 0;
        $avaliacoes5Estrelas = 0;

        foreach ($avaliacoes as $avaliacao) {
            $totalAvaliacoes++;
            $somaNotas += $avaliacao['nota_avaliacao'];
            if ($avaliacao['nota_avaliacao'] == 5) {
                $avaliacoes5Estrelas++;
            }
        }
        
        $notaMedia = ($totalAvaliacoes > 0) ? round($somaNotas / $totalAvaliacoes, 1) : 0;

        $stats = [
            [
                'label' => 'Total de Avaliações',
                'value' => $totalAvaliacoes,
                'icon' => 'fa-comments-o'
            ],
            [
                'label' => 'Nota Média',
                'value' => $notaMedia . ' / 5',
                'icon' => 'fa-star-half-o'
            ],
            [
                'label' => 'Avaliações 5 Estrelas',
                'value' => $avaliacoes5Estrelas,
                'icon' => 'fa-star'
            ],
            [
                'label' => 'Avaliações a Melhorar',
                'value' => $totalAvaliacoes - $avaliacoes5Estrelas,
                'icon' => 'fa-thumbs-o-down'
            ]
        ];

        View::render("avaliacao/index", [
            "avaliacoes" => $avaliacoes,
            "stats" => $stats
        ]);
    }
}