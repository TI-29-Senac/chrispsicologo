<?php
use App\Psico\Core\Flash;

$avaliacao = $dados['avaliacao'];
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-star fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Avaliação</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/avaliacoes/atualizar/<?= htmlspecialchars($avaliacao['id_avaliacao']) ?>" method="POST">
                <input type="hidden" name="id_avaliacao" value="<?= htmlspecialchars($avaliacao['id_avaliacao']) ?>">

                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label><b>Cliente (ID)</b></label>
                        <input class="w3-input w3-border" type="text" value="<?= htmlspecialchars($avaliacao['id_cliente']) ?>" disabled>
                    </div>
                    <div class="w3-half">
                        <label><b>Profissional (ID)</b></label>
                        <input class="w3-input w3-border" type="text" value="<?= htmlspecialchars($avaliacao['id_profissional']) ?>" disabled>
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="nota_avaliacao"><b>Nota (1 a 5)</b></label>
                        <input class="w3-input w3-border" type="number" id="nota_avaliacao" name="nota_avaliacao" min="1" max="5" value="<?= htmlspecialchars($avaliacao['nota_avaliacao']) ?>" required>
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-full">
                        <label for="descricao_avaliacao"><b>Comentário</b></label>
                        <textarea class="w3-input w3-border" id="descricao_avaliacao" name="descricao_avaliacao" rows="4" required><?= htmlspecialchars($avaliacao['descricao_avaliacao']) ?></textarea>
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Avaliação</button>
                <a href="/backend/avaliacoes/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>