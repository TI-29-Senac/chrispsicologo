<?php foreach($agendamentos as $item): ?>
    <td><?= $item['id_paciente'] ?></td>
    <td><?= $item['id_profissional'] ?></td>
    <td><?= $item['data_agendamento'] ?></td>
    <td><?= $item['status_consulta'] ?></td>
<?php endforeach; ?>
