<?php

use App\Psico\Database\Database;
require __DIR__ . '/../../vendor/autoload.php';
use App\Psico\Models\Usuario;
use App\Psico\Models\Profissional;
use App\Psico\Models\Pagamento;
use App\Psico\Models\Avaliacao;
use App\Psico\Models\Agendamento;

$db = Database::getInstance();

$usuario = new Usuario($db);
$profissional = new Profissional($db);
$pagamento = new Pagamento($db);
$avaliacao = new Avaliacao($db);
$agendamento = new Agendamento($db);

$idCliente = $usuario->inserirUsuario("Cliente Teste", "cliente123@test.com", "123456", "cliente", 1);
$idUsuarioProf = $usuario->inserirUsuario("Profissional Teste", "prof12@test.com", "123456", "profissional", 1);

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
$idPagamento = $pagamento->inserirPagamento($idAgendamento, 200.00, 50.00, "Pix");
echo "Pagamento inserido: $idPagamento\n";

// Atualizar pagamento
$pagamento->atualizarPagamento($idPagamento, 250.00, 75.00, "Crédito");
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
$usuario->deletarUsuario($idUsuarioProf);
*/

echo "===== TESTES FINALIZADOS =====\n";