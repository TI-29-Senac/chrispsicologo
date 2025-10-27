<?php
use App\Psico\Core\Flash;

$agendamento = $dados['agendamento']; 
?>

<div class="w3-container w3-white w3-text-grey w3-card-4" style="padding-bottom: 32px;">
    <h2 class="w3-text-grey w3-padding-16"><i class="fa fa-calendar fa-fw w3-margin-right w3-xxlarge" style="color: #A3B8A1;"></i>Editar Agendamento</h2>
    
    <?= Flash::getFlash() ?>

    <div class="w3-container">
        <div class="w3-row">
            <form action="/backend/agendamentos/atualizar/<?= htmlspecialchars($agendamento['id_agendamento']) ?>" method="POST">
                <input type="hidden" name="id_agendamento" value="<?= htmlspecialchars($agendamento['id_agendamento']) ?>">

                <div class="w3-row-padding">
                    <div class="w3-half">
                        <label><b>Paciente (ID)</b></label>
                        <input class="w3-input w3-border" type="text" value="<?= htmlspecialchars($agendamento['id_usuario']) ?>" disabled> 
                    </div>
                    <div class="w3-half">
                        <label><b>Profissional (ID)</b></label>
                        <input class="w3-input w3-border" type="text" value="<?= htmlspecialchars($agendamento['id_profissional']) ?>" disabled> 
                    </div>
                </div>

                <div class="w3-row-padding w3-section">
                    <div class="w3-half">
                        <label for="data_agendamento"><b>Data e Hora do Agendamento</b></label>
                        <input class="w3-input w3-border" type="datetime-local" id="data_agendamento" name="data_agendamento" value="<?= date('Y-m-d\TH:i', strtotime($agendamento['data_agendamento'])) ?>" required>
                    </div>
                    <div class="w3-half">
                        <label for="status_consulta"><b>Status da Consulta</b></label>
                        <select class="w3-select w3-border" id="status_consulta" name="status_consulta" required>
                            <?php $status_options = ['pendente', 'confirmada', 'cancelada', 'realizada']; ?>
                            <?php foreach ($status_options as $status): ?>
                                <option value="<?= $status ?>" <?= ($agendamento['status_consulta'] === $status) ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="w3-button w3-right w3-padding" style="background-color: #A3B8A1 !important;">Atualizar Agendamento</button>
                <a href="/backend/agendamentos/listar" class="w3-button w3-right w3-padding w3-light-grey w3-margin-right">Cancelar</a>
            </form>
        </div>
    </div>
</div>