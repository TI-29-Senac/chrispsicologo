<div class="w3-container">
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m8 l9">
            <h2 style="color: #5D6D68;">📅 Lista de Agendamentos</h2>
        </div>
        <div class="w3-col m4 l3 w3-right-align">
            <a href="/backend/agendamentos/criar" class="w3-button" style="background-color: #5D6D68 !important; color: white; border-radius: 8px;">+ Agendar Consulta</a>
        </div>
    </div>
  
    <div class="w3-card w3-round-large w3-margin-bottom" style="padding: 16px;">
        <form action="/backend/agendamentos/listar" method="GET">
            <div class="w3-row-padding">
                <div class="w3-col m3">
                    <label>Paciente</label>
                    <input class="w3-input w3-border w3-round" type="text" name="paciente" placeholder="Buscar paciente..." value="<?= htmlspecialchars($_GET['paciente'] ?? '') ?>">
                </div>
                <div class="w3-col m3">
                    <label>Profissional</label>
                    <input class="w3-input w3-border w3-round" type="text" name="profissional" placeholder="Buscar profissional..." value="<?= htmlspecialchars($_GET['profissional'] ?? '') ?>">
                </div>
                <div class="w3-col m3">
                    <label>Status</label>
                    <select class="w3-select w3-border w3-round" name="status">
                        <option value="">Todos</option>
                        <option value="confirmada" <?= ($_GET['status'] ?? '') == 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
                        <option value="cancelada" <?= ($_GET['status'] ?? '') == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        <option value="pendente" <?= ($_GET['status'] ?? '') == 'pendente' ? 'selected' : '' ?>>Pendente</option>
                    </select>
                </div>
                <div class="w3-col m3">
                    <label>&nbsp;</label>
                    <button type="submit" class="w3-button w3-round w3-block" style="background-color: #5D6D68; color: white;">Filtrar</button>
                </div>
            </div>
        </form>
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
            <?php
            $queryString = '';
            if (isset($_GET['paciente'])) $queryString .= '&paciente=' . urlencode($_GET['paciente']);
            if (isset($_GET['profissional'])) $queryString .= '&profissional=' . urlencode($_GET['profissional']);
            if (isset($_GET['status'])) $queryString .= '&status=' . urlencode($_GET['status']);
            ?>

            <?php if ($paginacao['pagina_atual'] > 1): ?>
                <a href="?pagina=<?= $paginacao['pagina_atual'] - 1 . $queryString ?>" class="w3-button">&laquo;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $paginacao['ultima_pagina']; $i++): ?>
                <a href="?pagina=<?= $i . $queryString ?>" class="w3-button <?= ($i == $paginacao['pagina_atual']) ? 'w3-green' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($paginacao['pagina_atual'] < $paginacao['ultima_pagina']): ?>
                <a href="?pagina=<?= $paginacao['pagina_atual'] + 1 . $queryString ?>" class="w3-button">&raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>