<div class="w3-main" style="margin-left:300px;margin-top:43px;">
  <div class="w3-content" style="max-width:1400px;">
    <div class="w3-container w3-padding-32">
      <h2 style="color: #5D6D68;">💰 Lista de Pagamentos</h2>

      <?php if (!empty($pagamentos)): ?>
        <div class="w3-responsive">
          <table class="w3-table-all w3-card-4 w3-hoverable w3-white" style="border-radius: 8px; overflow: hidden;">
            <thead style="background-color: #5D6D68; color: white;">
              <tr>
                <th>ID Pag.</th>
                <th>Cliente</th>
                <th>Profissional</th>
                <th>Valor Total</th>
                <th>Sinal</th>
                <th>Tipo</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($pagamentos as $pagamento): ?>
                <tr>
                  <td><?= htmlspecialchars($pagamento['id_pagamento']) ?></td>
                  <td><?= htmlspecialchars($pagamento['nome_cliente'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($pagamento['nome_profissional'] ?? 'N/A') ?></td>
                  <td>R$<?= number_format((float)($pagamento['valor_consulta'] ?? 0), 2, ',', '.') ?></td>
                  <td>R$<?= number_format((float)($pagamento['sinal_consulta'] ?? 0), 2, ',', '.') ?></td>
                  <td><?= htmlspecialchars(ucfirst($pagamento['tipo_pagamento'])) ?></td>
                  <td>
                    <a href="/backend/pagamentos/editar/<?= $pagamento['id_pagamento'] ?>" class="w3-button w3-tiny w3-round" style="background-color: #5D6D68; color: white;"><i class="fa fa-pencil"></i> Editar</a>
                    <a href="/backend/pagamentos/excluir/<?= $pagamento['id_pagamento'] ?>" class="w3-button w3-tiny w3-red w3-round"><i class="fa fa-trash"></i> Excluir</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="w3-text-grey">Nenhum pagamento encontrado.</p>
      <?php endif; ?>
    </div>
  </div>
</div>