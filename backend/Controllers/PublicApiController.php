<?php
 
namespace App\Psico\Controllers;
 
use App\Psico\Models\Profissional;
use App\Psico\Database\Database;
 
class PublicApiController{
    private $profissionalModel;
    public function __construct(){
        $db = Database::getInstance();
        $this->profissionalModel = new profissional($db);
    }
public function getProfissionals(){
    $dados = $this -> profissionalModel -> buscarProfissionaisAtivos();
    foreach($dados as &$profissional) {
        $profissional['caminho_imagem'] = '/backend/upload/'  . $profissional['img_profissional'];
    }
    unset($profissional);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $dados
    ], JSON_PRETTY_PRINT  |  JSON_UNESCAPED_SLASHES);
    exit;
}
 
}
 