<?php
use App\Psico\Core\Flash;
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-calendar-plus-o fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Novo Agendamento</h2>
    
    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/agendamentos/salvar" method="POST">
                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label for="id_usuario"><b>ID do Paciente</b></label>
                        <input class="w3-input w3-border" type="text" id="id_usuario" name="id_usuario" required>
                    </div>
                    <div class="w3-half">
                        <label for="id_profissional"><b>ID do Profissional</b></label>
                        <input class="w3-input w3-border" type="text" id="id_profissional" name="id_profissional" required>
                    </div>
                </div>
                
                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="data_agendamento"><b>Data do Agendamento</b></label>
                        <input class="w3-input w3-border" type="datetime-local" id="data_agendamento" name="data_agendamento" required>
                    </div>
                </div>

                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Salvar Agendamento</button>
                <a href="/backend/agendamentos/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>