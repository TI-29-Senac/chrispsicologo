<?php 
// ti-29-senac/chrispsicologo/chrispsicologo-backend-correto2/backend/Views/templates/servico/delete.php
use App\Psico\Core\Flash;
 
$servico = $dados['servico']; // Variável $servico vinda do Controller
?>
 
<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-trash fa-fw w3-margin-right w3-xxlarge w3-text-red"></i>Excluir Serviço</h2>
   
    <?= Flash::getFlash() ?>
 
    <div class="w3-container">
        <p class="w3-large">Você tem certeza que deseja mover o serviço abaixo para a lixeira?</p>
       
        <div class="w3-panel w3-border w3-pale-red w3-round-large">
             <p><strong>ID:</strong> <?= htmlspecialchars($servico->id ?? '') ?></p>
             <p><strong>Título:</strong> <?= htmlspecialchars($servico->titulo ?? '') ?></p>
        </div>
       
        <p><strong>Atenção:</strong> Esta ação marcará o serviço como 'excluído' (soft delete) e o tornará inativo no site.</p>
 
        <form action="/backend/servicos/deletar/<?= htmlspecialchars($servico->id) ?>" method="POST" onsubmit="return confirm('Você tem certeza que deseja excluir este serviço?');">
             <button type="submit" class="w3-button w3-red w3-padding">Sim, Excluir Serviço</button>
             <a href="/backend/servicos/listar" class="w3-button w3-light-grey w3-padding">Cancelar</a>
        </form>
    </div>
</div>