<div class="w3-main" style="margin-left:300px;margin-top:43px;">
  <div class="w3-content" style="max-width:1400px;">
    <div class="w3-container w3-padding-32">
      <h2 style="color: #5D6D68;">👨‍⚕️ Lista de Profissionais</h2>

      <?php if (!empty($profissionais)): ?>
        <div class="w3-responsive">
          <table class="w3-table-all w3-card-4 w3-hoverable w3-white" style="border-radius: 8px; overflow: hidden;">
            <thead style="background-color: #5D6D68; color: white;">
              <tr>
                <th>ID Profissional</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Especialidade</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($profissionais as $profissional): ?>
              <tr>
                <td><?= htmlspecialchars($profissional['id_profissional']) ?></td>
                <td><?= htmlspecialchars($profissional['nome_usuario']) ?></td>
                <td><?= htmlspecialchars($profissional['email_usuario']) ?></td>
                <td><?= htmlspecialchars($profissional['especialidade']) ?></td>
                <td>
                  <span class="w3-tag <?= $profissional['status_usuario'] === 'ativo' ? 'w3-green' : 'w3-red' ?> w3-round">
                    <?= ucfirst($profissional['status_usuario']) ?>
                  </span>
                </td>
                <td>
                  <a href="/backend/profissionais/editar/<?= $profissional['id_profissional'] ?>" class="w3-button w3-tiny w3-round" style="background-color: #5D6D68; color: white;"><i class="fa fa-pencil"></i> Editar</a>
                  <a href="/backend/profissionais/excluir/<?= $profissional['id_profissional'] ?>" class="w3-button w3-tiny w3-red w3-round"><i class="fa fa-trash"></i> Excluir</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="w3-text-grey">Nenhum profissional encontrado.</p>
      <?php endif; ?>
    </div>
  </div>
</div>