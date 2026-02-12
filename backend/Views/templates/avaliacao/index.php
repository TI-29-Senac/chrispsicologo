<div class="w3-container">
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m8 l9">
            <h2 style="color: #5D6D68;">⭐ Lista de Avaliações</h2>
        </div>
        <div class="w3-col m4 l3 w3-right-align">
            <a href="/backend/avaliacoes/criar" class="w3-button" style="background-color: #5D6D68 !important; color: white; border-radius: 8px;">+ Adicionar Avaliação</a>
        </div>
    </div>

    <div class="w3-card w3-round-large w3-margin-bottom" style="padding: 16px;">
        <form action="/backend/avaliacoes/listar" method="GET">
            <div class="w3-row-padding">
                <div class="w3-col m3">
                    <label>Cliente</label>
                    <input class="w3-input w3-border w3-round" type="text" name="cliente" placeholder="Buscar cliente..." value="<?= htmlspecialchars($_GET['cliente'] ?? '') ?>">
                </div>
                <div class="w3-col m3">
                    <label>Profissional</label>
                    <input class="w3-input w3-border w3-round" type="text" name="profissional" placeholder="Buscar profissional..." value="<?= htmlspecialchars($_GET['profissional'] ?? '') ?>">
                </div>
                <div class="w3-col m3">
                    <label>Nota</label>
                    <select class="w3-select w3-border w3-round" name="nota">
                        <option value="">Todas</option>
                        <option value="1" <?= ($_GET['nota'] ?? '') == '1' ? 'selected' : '' ?>>1 Estrela</option>
                        <option value="2" <?= ($_GET['nota'] ?? '') == '2' ? 'selected' : '' ?>>2 Estrelas</option>
                        <option value="3" <?= ($_GET['nota'] ?? '') == '3' ? 'selected' : '' ?>>3 Estrelas</option>
                        <option value="4" <?= ($_GET['nota'] ?? '') == '4' ? 'selected' : '' ?>>4 Estrelas</option>
                        <option value="5" <?= ($_GET['nota'] ?? '') == '5' ? 'selected' : '' ?>>5 Estrelas</option>
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
                    <th>Cliente</th>
                    <th>Profissional</th>
                    <th>Nota</th>
                    <th>Comentário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($avaliacoes)): ?>
                    <?php foreach($avaliacoes as $avaliacao): ?>
                    <tr>
                        <td><?= htmlspecialchars($avaliacao['id_avaliacao']) ?></td>
                        <td><?= htmlspecialchars($avaliacao['nome_cliente']) ?></td>
                        <td><?= htmlspecialchars($avaliacao['nome_profissional']) ?></td>
                        <td><?= str_repeat('⭐', $avaliacao['nota_avaliacao']) . str_repeat('☆', 5 - $avaliacao['nota_avaliacao']) ?></td>
                        <td><?= htmlspecialchars(substr($avaliacao['descricao_avaliacao'], 0, 50)) . '...' ?></td>
                        <td>
                            <a href="/backend/avaliacoes/editar/<?= $avaliacao['id_avaliacao'] ?>" class="w3-button w3-tiny w3-round" style="background-color: #5D6D68; color: white;">Editar</a>
                            <a href="/backend/avaliacoes/excluir/<?= $avaliacao['id_avaliacao'] ?>" class="w3-button w3-tiny w3-red w3-round">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="w3-center">Nenhuma avaliação encontrada.</td>
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
            if (isset($_GET['profissional'])) $queryString .= '&profissional=' . urlencode($_GET['profissional']);
            if (isset($_GET['nota'])) $queryString .= '&nota=' . urlencode($_GET['nota']);
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