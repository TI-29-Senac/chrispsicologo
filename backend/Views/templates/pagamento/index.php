<?php 
if (!empty($pagamentos)): 
?>
<table>
    <thead>
        <tr>
            <th>ID Pagamento</th>
            <th>ID Agendamento</th>
            <th>Valor Total</th>
            <th>Sinal</th>
            <th>Tipo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($pagamentos as $pagamento): ?>
        <tr>
            <td><?= $pagamento['id_pagamento'] ?></td>
            <td><?= $pagamento['id_agendamento'] ?></td>
            <td>R$<?= number_format((float)$pagamento['valor_consulta'], 2, ',', '.'); ?></td>
            <td>R$<?= number_format((float)$pagamento['sinal_consulta'], 2, ',', '.'); ?></td>
            <td><?= ucfirst($pagamento['tipo_pagamento']) ?></td>
            <td>
                <a href="/backend/pagamentos/editar?id=<?= $pagamento['id_pagamento'] ?>">Editar</a> |
                <a href="/backend/pagamentos/excluir?id=<?= $pagamento['id_pagamento'] ?>">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>Nenhum pagamento encontrado.</p>
<?php endif; ?>
