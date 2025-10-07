<div>Excluir Avaliação #<?= $avaliacao['id_avaliacao'] ?></div>
<p>Tem certeza que deseja excluir a avaliação abaixo? Esta ação não pode ser desfeita.</p>

<ul>
    <li><strong>ID Avaliação:</strong> <?= $avaliacao['id_avaliacao'] ?></li>
    <li><strong>ID Cliente:</strong> <?= $avaliacao['id_cliente'] ?></li>
    <li><strong>ID Profissional:</strong> <?= $avaliacao['id_profissional'] ?></li>
    <li><strong>Nota:</strong> <?= $avaliacao['nota_avaliacao'] ?>/5</li>
    <li><strong>Comentário:</strong> <?= htmlspecialchars($avaliacao['descricao_avaliacao']) ?></li>
</ul>

<form action="/backend/avaliacoes/deletar" method="POST">
    <input type="hidden" name="id_avaliacao" value="<?= $avaliacao['id_avaliacao'] ?>">
    <button type="submit">Confirmar Exclusão</button>
</form>