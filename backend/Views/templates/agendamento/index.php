<div class="w3-container">
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m8 l9">
            <h2 style="color: #5D6D68;">📅 Lista de Agendamentos</h2>
        </div>
        <div class="w3-col m4 l3 w3-right-align">
            <a href="/backend/agendamentos/criar" class="w3-button" style="background-color: #5D6D68 !important; color: white; border-radius: 8px;">+ Agendar Consulta</a>
        </div>
    </div>
  
    <div class="w3-responsive">
        <table class="w3-table-all w3-card-4 w3-hoverable w3-white">
            <thead style="background-color: #5D6D68; color: white;">
                <tr class="w3-light-grey">
                <th>ID</th>
                <th>Paciente</th>
                <th>Profissional</th>
                <th>Data</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($agendamentos)): ?>
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
              <?php else: ?>
                  <tr>
                      <td colspan="6" class="w3-center">Nenhum agendamento encontrado.</td>
                  </tr>
              <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br>
    
    <?php if (isset($paginacao) && $paginacao['ultima_pagina'] > 1): ?>
    <div class="w3-center">
        <div class="w3-bar">
            <?php if ($paginacao['pagina_atual'] > 1): ?>
                <a href="?pagina=<?= $paginacao['pagina_atual'] - 1 ?>" class="w3-button">&laquo;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $paginacao['ultima_pagina']; $i++): ?>
                <a href="?pagina=<?= $i ?>" class="w3-button <?= ($i == $paginacao['pagina_atual']) ? 'w3-green' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($paginacao['pagina_atual'] < $paginacao['ultima_pagina']): ?>
                <a href="?pagina=<?= $paginacao['pagina_atual'] + 1 ?>" class="w3-button">&raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>