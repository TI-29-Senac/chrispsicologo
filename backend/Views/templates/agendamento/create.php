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
        <label for="id_usuario"><b>Paciente</b></label>
        <select class="w3-select w3-border" id="id_usuario" name="id_usuario" required>
            <option value="" disabled selected>Selecione o paciente...</option>
            <?php if (!empty($pacientes)): ?>
                <?php foreach ($pacientes as $paciente): ?>
                    <option value="<?= htmlspecialchars($paciente->id_usuario) ?>">
                        <?= htmlspecialchars($paciente->nome_usuario) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>
    <div class="w3-half">
        <label for="id_profissional"><b>Profissional</b></label>
        <select class="w3-select w3-border" id="id_profissional" name="id_profissional" required>
            <option value="" disabled selected>Selecione o profissional...</option>
            <?php if (!empty($profissionais)): ?>
                <?php foreach ($profissionais as $profissional): ?>
                    <option value="<?= htmlspecialchars($profissional->id_profissional) ?>">
                        <?= htmlspecialchars($profissional->nome_usuario) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
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