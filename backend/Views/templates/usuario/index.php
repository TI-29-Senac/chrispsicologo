<div class="w3-main" style="margin-left:300px;margin-top:43px;">
  <div class="w3-container w3-padding-32">
    <h2 style="color: #5D6D68;">📋 Lista de Usuários</h2>

    <?php if (!empty($usuarios)): ?>
      <div class="w3-responsive">
        <table class="w3-table-all w3-card-4 w3-hoverable w3-white" style="border-radius: 8px; overflow: hidden;">
          <thead style="background-color: #5D6D68; color: white;">
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Email</th>
              <th>Tipo</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($usuarios as $usuario): ?>
              <tr>
                <td><?= htmlspecialchars($usuario->id_usuario) ?></td>
                <td><?= htmlspecialchars($usuario->nome_usuario) ?></td>
                <td><?= htmlspecialchars($usuario->email_usuario) ?></td>
                <td><?= htmlspecialchars(ucfirst($usuario->tipo_usuario)) ?></td>
                <td>
                  <?php if ($usuario->status_usuario === 'ativo'): ?>
                    <span class="w3-tag w3-green w3-round">Ativo</span>
                  <?php else: ?>
                    <span class="w3-tag w3-red w3-round">Inativo</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="/backend/usuario/editar/<?= $usuario->id_usuario ?>" class="w3-button w3-tiny w3-blue w3-round" style="margin-right: 5px;">Editar</a>
                  <a href="/backend/usuario/excluir/<?= $usuario->id_usuario ?>" class="w3-button w3-tiny w3-red w3-round">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="w3-text-grey">Nenhum usuário encontrado.</p>
    <?php endif; ?>
  </div>
</div>