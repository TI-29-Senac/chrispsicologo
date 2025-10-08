<div>Excluir Pagamento #<?= $pagamento['id_pagamento'] ?></div>
<p>Tem certeza que deseja excluir o pagamento abaixo? Esta ação não pode ser desfeita.</p>

<ul>
    <li><strong>ID Pagamento:</strong> <?= $pagamento['id_pagamento'] ?></li>
    <li><strong>ID Agendamento:</strong> <?= $pagamento['id_agendamento'] ?></li>
    <li><strong>Valor Total:</strong> R$<?= number_format($pagamento['valor_consulta'], 2, ',', '.') ?></li>
    <li><strong>Tipo:</strong> <?= ucfirst($pagamento['tipo_pagamento']) ?></li>
</ul>

<form action="/backend/pagamentos/deletar" method="POST">
    <input type="hidden" name="id_pagamento" value="<?= $pagamento['id_pagamento'] ?>">
    <button type="submit">Confirmar Exclusão</button>
</form>