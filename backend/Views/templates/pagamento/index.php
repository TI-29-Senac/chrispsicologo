<div class="w3-container">
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m8 l9">
            <h2 style="color: #5D6D68;">💰 Lista de Pagamentos</h2>
        </div>
        <div class="w3-col m4 l3 w3-right-align">
            <a href="/backend/pagamentos/criar" class="w3-button" style="background-color: #5D6D68 !important; color: white; border-radius: 8px;">+ Adicionar Pagamento</a>
        </div>
    </div>

    <div class="w3-responsive">
        <table class="w3-table-all w3-card-4 w3-hoverable w3-white">
            <thead style="background-color: #5D6D68; color: white;">
                <tr class="w3-light-grey">
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Profissional</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pagamentos)): ?>
                    <?php foreach($pagamentos as $pagamento): ?>
                    <tr>
                        <td><?= htmlspecialchars($pagamento['id_pagamento']) ?></td>
                        <td><?= htmlspecialchars($pagamento['nome_cliente']) ?></td>
                        <td><?= htmlspecialchars($pagamento['nome_profissional']) ?></td>
                        <td>R$ <?= htmlspecialchars(number_format($pagamento['valor_consulta'], 2, ',', '.')) ?></td>
                        <td><span class="w3-tag w3-round w3-teal"><?= htmlspecialchars(ucfirst($pagamento['tipo_pagamento'])) ?></span></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($pagamento['data_pagamento']))) ?></td>
                        <td>
                            <a href="/backend/pagamentos/editar/<?= $pagamento['id_pagamento'] ?>" class="w3-button w3-tiny w3-round" style="background-color: #5D6D68; color: white;">Editar</a>
                            <a href="/backend/pagamentos/excluir/<?= $pagamento['id_pagamento'] ?>" class="w3-button w3-tiny w3-red w3-round">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="w3-center">Nenhum pagamento encontrado.</td>
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