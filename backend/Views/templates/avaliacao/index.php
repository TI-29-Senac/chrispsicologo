<div class="w3-main" style="margin-left:300px;margin-top:43px;">
  <div class="w3-content" style="max-width:1400px;">
    <div class="w3-container w3-padding-32">
      <h2 style="color: #5D6D68;">⭐ Lista de Avaliações</h2>

      <?php if (!empty($avaliacoes)): ?>
        <div class="w3-responsive">
          <table class="w3-table-all w3-card-4 w3-hoverable w3-white" style="border-radius: 8px; overflow: hidden;">
            <thead style="background-color: #5D6D68; color: white;">
              <tr>
                <th>ID Avaliação</th>
                <th>ID Cliente</th>
                <th>ID Profissional</th>
                <th>Nota</th>
                <th>Comentário</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($avaliacoes as $avaliacao): ?>
              <tr>
                <td><?= htmlspecialchars($avaliacao['id_avaliacao']) ?></td>
                <td><?= htmlspecialchars($avaliacao['id_cliente']) ?></td>
                <td><?= htmlspecialchars($avaliacao['id_profissional']) ?></td>
                <td><?= str_repeat('⭐', (int)$avaliacao['nota_avaliacao']) . str_repeat('☆', 5 - (int)$avaliacao['nota_avaliacao']) ?> (<?= $avaliacao['nota_avaliacao'] ?>/5)</td>
                <td style="max-width: 300px; word-wrap: break-word;"><?= htmlspecialchars($avaliacao['descricao_avaliacao']) ?></td>
                <td>
                    <a href="/backend/avaliacoes/editar?id=<?= $avaliacao['id_avaliacao'] ?>">Editar</a> |
                    <a href="/backend/avaliacoes/excluir?id=<?= $avaliacao['id_avaliacao'] ?>">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="w3-text-grey">Nenhuma avaliação encontrada.</p>
      <?php endif; ?>
    </div>
  </div>
</div>