<?php
use App\Psico\Core\Flash;
 
$usuario = $dados['usuario'];
?>
 
<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-trash fa-fw w3-margin-right w3-xxlarge w3-text-red"></i>Excluir Usuário</h2>
   
    <?= Flash::getFlash() ?>
 
    <div class="w3-container">
        <p class="w3-large">Você tem certeza que deseja excluir o usuário abaixo?</p>
       
        <div class="w3-panel w3-border w3-pale-red w3-round-large">
             <p><strong>ID:</strong> <?= htmlspecialchars($usuario->id_usuario ?? '') ?></p>
             <p><strong>Nome:</strong> <?= htmlspecialchars($usuario->nome_usuario ?? '') ?></p>
             <p><strong>Email:</strong> <?= htmlspecialchars($usuario->email_usuario ?? '') ?></p>
        </div>
       
        <p><strong>Atenção:</strong> Esta ação não pode ser desfeita!</p>
 
        <form action="/backend/usuario/deletar/<?= htmlspecialchars($usuario->id_usuario) ?>" method="POST">
             <button type="submit" class="w3-button w3-red w3-padding">Sim, Excluir</button>
             <a href="/backend/usuario/listar" class="w3-button w3-light-grey w3-padding">Cancelar</a>
        </form>
    </div>
</div>