<?php
require_once __DIR__ . '/../Models/Profissionais.php';
require_once __DIR__ . '/../Database/Database.php';

$profissional = new Profissional($db);

$id_profissional = 13;

$resultado = $profissional->deletarProfissional($id_profissional);

if ($resultado > 0) {
    echo "Profissional deletado com sucesso!";
} elseif ($resultado === 0) {
    echo "Nenhum profissional encontrado com esse ID.";
} else {
    echo "Erro ao deletar profissional.";
}


?>