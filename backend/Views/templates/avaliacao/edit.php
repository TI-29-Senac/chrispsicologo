<div>Editar Avaliação #<?= $avaliacao['id_avaliacao'] ?></div>

<form action="/backend/avaliacoes/atualizar/<?php echo $avaliacao['id_avaliacao']; ?>" method="POST">
    
    <input type="hidden" name="id_avaliacao" value="<?php echo $avaliacao['id_avaliacao'] ?>">
    
    <label for="id_cliente">ID do Cliente:</label>
    <input type="number" id="id_cliente" name="id_cliente" value="<?= $avaliacao['id_cliente'] ?>" readonly><br><br>

    <label for="id_profissional">ID do Profissional Avaliado:</label>
    <input type="number" id="id_profissional" name="id_profissional" value="<?= $avaliacao['id_profissional'] ?>" readonly><br><br>

    <label for="nota_avaliacao">Nota (1 a 5):</label>
    <input type="number" id="nota_avaliacao" name="nota_avaliacao" min="1" max="5" value="<?= $avaliacao['nota_avaliacao'] ?>" required><br><br>

    <label for="descricao_avaliacao">Comentário/Descrição:</label>
    <textarea id="descricao_avaliacao" name="descricao_avaliacao" rows="4" required><?= htmlspecialchars($avaliacao['descricao_avaliacao']) ?></textarea><br><br>

    <button type="submit">Atualizar Avaliação</button>
</form>