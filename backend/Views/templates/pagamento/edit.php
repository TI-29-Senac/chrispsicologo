<?php
use App\Psico\Core\Flash;

$pagamento = $dados['pagamento'];
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-credit-card fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Pagamento</h2>

    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/pagamentos/atualizar/<?= htmlspecialchars($pagamento['id_pagamento']) ?>" method="POST">
                <input type="hidden" name="id_pagamento" value="<?= htmlspecialchars($pagamento['id_pagamento']) ?>">

                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label><b>Agendamento (ID)</b></label>
                        <input class="w3-input w3-border" type="text" value="<?= htmlspecialchars($pagamento['id_agendamento']) ?>" disabled>
                    </div>
                    <div class="w3-half">
                        <label for="tipo_pagamento"><b>Tipo de Pagamento</b></label>
                        <select class="w3-select w3-border" id="tipo_pagamento" name="tipo_pagamento" required>
                            <?php $tipos = ['pix', 'credito', 'debito', 'dinheiro']; ?>
                            <?php foreach($tipos as $tipo): ?>
                                <option value="<?= $tipo ?>" <?= $pagamento['tipo_pagamento'] === $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding w3-section" style="background-color: #A3B8A1 !important;">Atualizar Pagamento</button>
                <a href="/backend/pagamentos/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right w3-section">Cancelar</a>
            </form>
        </div>
    </div>
</div>