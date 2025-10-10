<div class="w3-container w3-padding-32">
  <h2 style="color: #5D6D68;">📅 Lista de Agendamentos</h2>

  <?php if (!empty($agendamentos)): ?>
    <div class="w3-responsive">
      <table class="w3-table-all w3-card-4 w3-hoverable w3-white" style="border-radius: 8px; overflow: hidden;">
        <thead style="background-color: #5D6D68; color: white;">
          <tr>
            <th>ID Agend.</th>
            <th>Paciente</th>
            <th>Profissional</th>
            <th>Data</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($agendamentos as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['id_agendamento']) ?></td>
            <td><?= htmlspecialchars($item['nome_paciente']) ?></td>
            <td><?= htmlspecialchars($item['nome_profissional']) ?></td>
            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($item['data_agendamento']))) ?></td>
            <td>
              <span class="w3-tag w3-round <?= $item['status_consulta'] === 'confirmada' ? 'w3-green' : ($item['status_consulta'] === 'cancelada' ? 'w3-red' : 'w3-blue-grey') ?>">
                <?= htmlspecialchars(ucfirst($item['status_consulta'])) ?>
              </span>
            </td>
            <td>
              <a href="/backend/agendamentos/editar/<?= $item['id_agendamento'] ?>" class="w3-button w3-tiny w3-round" style="background-color: #5D6D68; color: white;"><i class="fa fa-pencil"></i> Editar</a>
              <a href="/backend/agendamentos/excluir/<?= $item['id_agendamento'] ?>" class="w3-button w3-tiny w3-red w3-round"><i class="fa fa-trash"></i> Excluir</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p class="w3-text-grey">Nenhum agendamento encontrado.</p>
  <?php endif; ?>
</div>