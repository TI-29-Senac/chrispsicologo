<?php
require_once __DIR__ . '/../Models/Profissionais.php';
require_once __DIR__ . '/../Models/Usuario.php';
require_once __DIR__ . '/../Database/Database.php';

require_once __DIR__ . '/../Models/Pagamento.php';
require_once __DIR__ . '/../Models/Avaliacao.php';
require_once __DIR__ . '/../Models/Agendamento.php';


$usuario = new Usuario($db);
$profissional = new Profissional($db);
$pagamento = new Pagamento($db);
$avaliacao = new Avaliacao($db);
$agendamento = new Agendamento($db);

/*
// DELETAR PROFISSIONAL E USUÁRIO
$id = $profissional->deletarProfissional(14);
$resultado = $usuario->deletarUsuario(22);

if ($resultadoProf > 0) {
    $resultadoUser = $usuario->deletarUsuario($idUsuario);
    if ($resultadoUser > 0) {
        echo "Usuário e Profissional deletados com sucesso!";
    } else {
        echo "Profissional deletado, mas não foi possível deletar o usuário: $resultadoUser";
    }
} elseif ($resultadoProf === 0) {
    echo "Nenhum profissional encontrado para deletar.";
} else {
    echo "Erro ao deletar profissional: $resultadoProf";
}



// INSERIR USUARIO E PROFISSIONAL
$idNovoUsuario = $usuario->inserirUsuario("Teste", "teste@teste.com", "123456", "cliente", "ativo");
if ($idNovoUsuario) {
    echo "\nUsuário inserido com ID: $idNovoUsuario";
    $idNovoProfissional = $profissional->inserirProfissional($idNovoUsuario, "Psicoterapia");
    echo "\nProfissional inserido com ID: $idNovoProfissional";
}

// ATUALIZAR TIPO DE USUÁRIO E ESPECIALIDADE DO PROFISSIONAL
$usuario->atualizarTipoUsuario($idNovoUsuario, "profissional");
$profissional->atualizarProfissional($idNovoUsuario, "Psicoterapia Avançada");

// BUSCAR PROFISSIONAL
$todos = $profissional->buscarProfissionais();
print_r($todos);

// BUSCAR PROFISSIONAIS POR ESPECIALIDADE
$porEspecialidade = $profissional->buscarProfissionaisPorEspecialidade("Psicoterapia Avançada");
print_r($porEspecialidade);
*/


// ===== CRIAR USUÁRIOS =====
$idCliente = $usuario->inserirUsuario("Cliente Teste", "cliente@test.com", "123456", "cliente", 1);
$idUsuarioProf = $usuario->inserirUsuario("Profissional Teste", "prof@test.com", "123456", "profissional", 1);

// ===== CRIAR PROFISSIONAL =====
$idProfissional = $profissional->inserirProfissional($idUsuarioProf, "Psicoterapia");

// ===== TESTE DE AGENDAMENTO =====
$idAgendamento = $agendamento->inserirAgendamento($idCliente, $idProfissional, "2025-09-20 10:00:00");
echo "Agendamento inserido: $idAgendamento\n";

// Atualizar agendamento
$agendamento->atualizarAgendamento($idAgendamento, "2025-09-21 15:00:00", "confirmada");
echo "Agendamento atualizado\n";

// Buscar agendamentos
$listaAgendamentos = $agendamento->buscarAgendamentos();
print_r($listaAgendamentos);

// ===== TESTE DE PAGAMENTO =====
$idPagamento = $pagamento->inserirPagamento($idAgendamento, "200", 50.00, "Pix");
echo "Pagamento inserido: $idPagamento\n";

// Atualizar pagamento
$pagamento->atualizarPagamento($idPagamento, "250", 75.00, "Crédito");
echo "Pagamento atualizado\n";

// Buscar pagamentos
$listaPagamentos = $pagamento->buscarPagamentos();
print_r($listaPagamentos);

// ===== TESTE DE AVALIAÇÃO =====
$idAvaliacao = $avaliacao->inserirAvaliacao($idCliente, $idProfissional, "Ótimo profissional!", 5);
echo "Avaliação inserida: $idAvaliacao\n";

// Atualizar avaliação
$avaliacao->atualizarAvaliacao($idAvaliacao, "Muito bom profissional!", 4);
echo "Avaliação atualizada\n";

// Buscar avaliações
$listaAvaliacoes = $avaliacao->buscarAvaliacoes();
print_r($listaAvaliacoes);

// ===== LIMPAR DADOS =====
/*
$pagamento->deletarPagamento($idPagamento);
$avaliacao->deletarAvaliacao($idAvaliacao);
$agendamento->deletarAgendamento($idAgendamento);
$profissional->deletarProfissional($idProfissional);
$usuario->deletarUsuario($idCliente);
$usuario->deletarUsuario($idUsuarioProf);*/

echo "===== TESTES FINALIZADOS E DADOS LIMPOS =====\n";



?>