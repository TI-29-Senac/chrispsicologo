<?php 
use App\Psico\Core\Flash;
 
$profissional = $dados['usuario']; 
?>
 
<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-trash fa-fw w3-margin-right w3-xxlarge w3-text-red"></i>Excluir Profissional</h2>
   
    <?= Flash::getFlash() ?>
 
    <div class="w3-container">
        <p class="w3-large">Você tem certeza que deseja excluir o registro do profissional abaixo?</p>
       
        <div class="w3-panel w3-border w3-pale-red w3-round-large">
             <p><strong>ID Profissional:</strong> <?= htmlspecialchars($profissional->id_profissional ?? '') ?></p>
             <p><strong>Nome:</strong> <?= htmlspecialchars($profissional->nome_usuario ?? '') ?></p>
             <p><strong>Especialidade:</strong> <?= htmlspecialchars($profissional->especialidade ?? '') ?></p>
        </div>
       
        <p><strong>Atenção:</strong> Esta ação marcará o profissional como excluído (soft delete) e não poderá ser desfeita facilmente!</p>
 
        <form action="/backend/profissionais/deletar/<?= htmlspecialchars($profissional->id_profissional) ?>" method="POST" onsubmit="return confirm('Você tem certeza que deseja excluir este profissional?');">
             <button type="submit" class="w3-button w3-red w3-padding">Sim, Excluir Profissional</button>
             <a href="/backend/profissionais/listar" class="w3-button w3-light-grey w3-padding">Cancelar</a>
        </form>
    </div>
</div>