<?php 
use App\Psico\Core\Flash;
 
$agendamento = $dados['agendamento']; 
?>
 
<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-trash fa-fw w3-margin-right w3-xxlarge w3-text-red"></i>Cancelar Agendamento</h2>
    
    <?= Flash::getFlash() ?>
 
    <div class="w3-container">
        <p class="w3-large">Você tem certeza que deseja cancelar o agendamento abaixo?</p>
        
        <div class="w3-panel w3-border w3-pale-red w3-round-large">
             <p><strong>ID Agendamento:</strong> <?= htmlspecialchars($agendamento['id_agendamento'] ?? '') ?></p>
             <p><strong>ID Paciente:</strong> <?= htmlspecialchars($agendamento['id_usuario'] ?? '') ?></p>
             <p><strong>ID Profissional:</strong> <?= htmlspecialchars($agendamento['id_profissional'] ?? '') ?></p>
             <p><strong>Data:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($agendamento['data_agendamento']))) ?></p>
        </div>
        
        <p><strong>Atenção:</strong> Esta ação irá marcar o agendamento como 'cancelado' (soft delete).</p>
 
        <form action="/backend/agendamentos/deletar/<?= htmlspecialchars($agendamento['id_agendamento']) ?>" method="POST">
             <button type="submit" class="w3-button w3-red w3-padding">Sim, Cancelar</button>
             <a href="/backend/agendamentos/listar" class="w3-button w3-light-grey w3-padding">Voltar</a>
        </form>
    </div>
</div>