<?php 
use App\Psico\Core\Flash;
 
$avaliacao = $dados['avaliacao'];
?>
 
<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-trash fa-fw w3-margin-right w3-xxlarge w3-text-red"></i>Excluir Avaliação</h2>
   
    <?= Flash::getFlash() ?>
 
    <div class="w3-container">
        <p class="w3-large">Você tem certeza que deseja excluir a avaliação abaixo?</p>
       
        <div class="w3-panel w3-border w3-pale-red w3-round-large">
             <p><strong>ID Avaliação:</strong> <?= htmlspecialchars($avaliacao['id_avaliacao'] ?? '') ?></p>
             <p><strong>Nota:</strong> <?= htmlspecialchars($avaliacao['nota_avaliacao'] ?? '') ?>/5</p>
             <p><strong>Comentário:</strong> "<?= htmlspecialchars($avaliacao['descricao_avaliacao'] ?? '') ?>"</p>
        </div>
       
        <p><strong>Atenção:</strong> Esta ação não pode ser desfeita!</p>
 
        <form action="/backend/avaliacoes/deletar/<?= htmlspecialchars($avaliacao['id_avaliacao']) ?>" method="POST">
             <button type="submit" class="w3-button w3-red w3-padding">Sim, Excluir</button>
             <a href="/backend/avaliacoes/listar" class="w3-button w3-light-grey w3-padding">Cancelar</a>
        </form>
    </div>
</div>