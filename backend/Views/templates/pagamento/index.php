<div class="w3-container">
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m8 l9">
            <h2 style="color: #5D6D68;">💰 Lista de Pagamentos</h2>
        </div>
        <div class="w3-col m4 l3 w3-right-align">
            <a href="/backend/pagamentos/criar" class="w3-button" style="background-color: #5D6D68 !important; color: white; border-radius: 8px;">+ Adicionar Pagamento</a>
        </div>
    </div>

    <div class="w3-card w3-round-large w3-margin-bottom" style="padding: 16px;">
        <form action="/backend/pagamentos/listar" method="GET">
            <div class="w3-row-padding">
                <div class="w3-col m4">
                    <label>Nome do Cliente</label>
                    <input class="w3-input w3-border w3-round" type="text" name="cliente" placeholder="Buscar por nome..." value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>">
                </div>
                <div class="w3-col m3">
                    <label>Tipo de Pagamento</label>
                    <select class="w3-select w3-border w3-round" name="tipo">
                        <option value="">Todos</option>
                        <option value="Pix" <?= ($_GET['tipo'] ?? '') == 'Pix' ? 'selected' : '' ?>>Pix</option>
                        <option value="Crédito" <?= ($_GET['tipo'] ?? '') == 'Crédito' ? 'selected' : '' ?>>Crédito</option>
                        <option value="Débito" <?= ($_GET['tipo'] ?? '') == 'Débito' ? 'selected' : '' ?>>Débito</option>
                    </select>
                </div>
                <div class="w3-col m3">
                    <label>Data</label>
                    <input class="w3-input w3-border w3-round" type="date" name="data" value="<?= htmlspecialchars($_GET['data'] ?? '') ?>">
                </div>
                <div class="w3-col m2">
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
            <?php
            $queryString = '';
            if (isset($_GET['cliente'])) $queryString .= '&cliente=' . urlencode($_GET['cliente']);
            if (isset($_GET['tipo'])) $queryString .= '&tipo=' . urlencode($_GET['tipo']);
            if (isset($_GET['data'])) $queryString .= '&data=' . urlencode($_GET['data']);
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