<?php
use App\Psico\Core\Flash;
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-money fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Criar Novo Pagamento</h2>
    
    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
 <form action="/backend/pagamentos/salvar" method="POST">
    <div class="w3-row-padding">
        <div class="w3-half">
            <label for="id_agendamento"><b>Selecione o Agendamento</b></label>
            <select class="w3-select w3-border" id="id_agendamento" name="id_agendamento" required>
                <option value="" disabled selected>Escolha um agendamento...</option>
                <?php if (!empty($agendamentos)): ?>
                    <?php foreach ($agendamentos as $ag): ?>
                        <option value="<?= htmlspecialchars($ag['id_agendamento']) ?>">
                            Ag. #<?= htmlspecialchars($ag['id_agendamento']) ?> - 
                            Paciente: <?= htmlspecialchars($ag['nome_paciente']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="w3-half">
            <label for="tipo_pagamento"><b>Tipo de Pagamento</b></label>
            <select class="w3-select w3-border" id="tipo_pagamento" name="tipo_pagamento" required>
                <option value="pix" selected>Pix</option>
                <option value="credito">Cartão de Crédito</option>
                <option value="debito">Cartão de Débito</option>
                <option value="dinheiro">Dinheiro</option>
            </select>
        </div>
    </div>

    <button type="submit" class="w3-button w3-right w3-padding w3-section" style="background-color: #A3B8A1 !important;">Salvar Pagamento</button>
    <a href="/backend/pagamentos/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right w3-section">Cancelar</a>
</form>