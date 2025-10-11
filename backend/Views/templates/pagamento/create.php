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
                        <label for="id_agendamento"><b>ID do Agendamento</b></label>
                        <input class="w3-input w3-border" type="number" id="id_agendamento" name="id_agendamento" required>
                    </div>
                    <div class="w3-half">
                        <label for="tipo_pagamento"><b>Tipo de Pagamento</b></label>
                        <select class="w3-select w3-border" id="tipo_pagamento" name="tipo_pagamento" required>
                            <option value="pix">Pix</option>
                            <option value="credito">Cartão de Crédito</option>
                            <option value="debito">Cartão de Débito</option>
                            <option value="dinheiro">Dinheiro</option>
                        </select>
                    </div>
                </div>
                
                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="valor_consulta"><b>Valor Total da Consulta (R$)</b></label>
                        <input class="w3-input w3-border" type="number" step="0.01" id="valor_consulta" name="valor_consulta" placeholder="Ex: 150.00" required>
                    </div>
                    <div class="w3-half">
                        <label for="sinal_consulta"><b>Valor do Sinal/Adiantamento (R$)</b></label>
                        <input class="w3-input w3-border" type="number" step="0.01" id="sinal_consulta" name="sinal_consulta" placeholder="Ex: 50.00" required>
                    </div>
                </div>
    
                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Salvar Pagamento</button>
                <a href="/backend/pagamentos/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>