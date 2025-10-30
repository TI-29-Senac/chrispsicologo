<?php
namespace App\Psico\Controllers;

use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
use PDO;

class PublicProfissionalController {

    public $profissional;   
    public $db;

    // Construtor público, NÃO chama o AuthenticatedController
    public function __construct(){
        $this->db = Database::getInstance();
        $this->profissional = new Profissional($this->db);
    }

    /**
     * Método público para a lista da página 'profissionais.html'
     */
     public function listarPublico() {
        header('Content-Type: application/json');
        try {
            $profissionais = $this->profissional->listarProfissionaisPublicos();
            http_response_code(200);
            echo json_encode($profissionais);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno ao buscar profissionais.', 'details' => $e->getMessage()]);
        }
    }

    /**
     * Método público para a página 'agendamento-detalhe.html'
     */
    public function detalhePublico($id) {
        header('Content-Type: application/json');
        try {
            $profissional = $this->profissional->buscarProfissionalPublicoPorId((int)$id);

            if (!$profissional) {
                http_response_code(404);
                echo json_encode(['error' => 'Profissional não encontrado ou não está disponível.']);
                return;
            }

            http_response_code(200);
            echo json_encode($profissional);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno ao buscar detalhes do profissional.', 'details' => $e->getMessage()]);
        }
    }
    
    /**
     * Método público para o carrossel do 'index.html'
     */
    public function getCarrosselCardsHtml() {
        $idsProfissionaisCarrossel = [6, 7, 8, 9, 10];
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

        foreach ($profissionaisParaCarrossel as $profissional) {
            $especialidadeExibida = 'Clínica Geral'; 
            if (!empty($profissional->especialidade)) {
                $especialidadesString = trim($profissional->especialidade);
                $partes = explode(',', $especialidadesString, 2);
                $primeiraEspecialidade = $partes[0] ?? '';
                $primeiraEspecialidadeLimpa = trim($primeiraEspecialidade);
                if ($primeiraEspecialidadeLimpa !== '') {
                    $especialidadeExibida = $primeiraEspecialidadeLimpa;
                }
            }

            $nomeBase = explode(' ', $profissional->nome_usuario)[0];
            $fotoUrlPadrao = "/img/profissionais/" . strtolower($nomeBase) . ".png";
            $fotoFinal = (!empty($profissional->img_profissional)) ? "/" . ltrim($profissional->img_profissional, '/') : $fotoUrlPadrao;

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

        $htmlCompleto = $htmlCards;
        header('Content-Type: text/html; charset=utf-8');
        echo $htmlCompleto;
        exit;
    }
}