<div>Registrar Nova Avaliação</div>
<form action="/backend/avaliacoes/salvar" method="POST">
    <label for="id_cliente">ID do Cliente:</label>
    <input type="number" id="id_cliente" name="id_cliente" required><br><br>

    <label for="id_profissional">ID do Profissional Avaliado:</label>
    <input type="number" id="id_profissional" name="id_profissional" required><br><br>

    <label for="nota_avaliacao">Nota (1 a 5):</label>
    <input type="number" id="nota_avaliacao" name="nota_avaliacao" min="1" max="5" required><br><br>

    <label for="descricao_avaliacao">Comentário/Descrição:</label>
    <textarea id="descricao_avaliacao" name="descricao_avaliacao" rows="4" required></textarea><br><br>

    <button type="submit" value="Salvar">Salvar Avaliação</button>
</form>