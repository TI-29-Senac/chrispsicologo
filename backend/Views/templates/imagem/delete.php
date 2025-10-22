<?php
use App\Psico\Core\Flash;
$imagem = $dados['imagem'];
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-trash fa-fw w3-margin-right w3-xxlarge w3-text-red"></i>Excluir Imagem</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <p class="w3-large">Você tem certeza que deseja excluir a imagem abaixo?</p>

        <div class="w3-panel w3-border w3-pale-red w3-round-large w3-center">
             <?php if (!empty($imagem->url_imagem)): ?>
                <img src="/<?= htmlspecialchars($imagem->url_imagem) ?>" alt="Imagem a ser excluída" style="max-width: 250px; height: auto; margin: 10px auto;">
             <?php else: ?>
                <p><i>Imagem sem arquivo associado.</i></p>
             <?php endif; ?>
             <p><strong>ID Imagem:</strong> <?= htmlspecialchars($imagem->id_imagem ?? '') ?></p>
             <p><strong>Página:</strong> <?= htmlspecialchars($imagem->nome_pagina ?? 'N/A') ?></p>
             <p><strong>Seção:</strong> <?= htmlspecialchars($imagem->nome_secao ?? 'N/A') ?></p>
             <p><strong>Ordem:</strong> <?= htmlspecialchars($imagem->ordem ?? '') ?></p>
        </div>

        <p><strong>Atenção:</strong> Esta ação removerá a imagem do site e do servidor permanentemente!</p>

        <form action="/backend/imagens/deletar/<?= htmlspecialchars($imagem->id_imagem) ?>" method="POST" onsubmit="return confirm('Confirma a exclusão PERMANENTE desta imagem?');">
             <button type="submit" class="w3-button w3-red w3-padding">Sim, Excluir Imagem</button>
             <a href="/backend/imagens/listar" class="w3-button w3-light-grey w3-padding">Cancelar</a>
        </form>
    </div>
</div>