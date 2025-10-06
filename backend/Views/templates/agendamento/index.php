<?php foreach($agendamentos as $agendameto): ?>
    <p><?= $agendameto->id_paciente ?></p>
    <p><?= $agendameto->id_profissional ?></p>
    <p><?= $agendameto->data_agendameto ?></p>
    <p><?= $agendameto->status_consulta ?></p>
    <?php endforeach; ?>