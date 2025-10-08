<div>Editar Pagamento #<?= $pagamento['id_pagamento'] ?></div>
<form action="/backend/pagamentos/atualizar" method="POST">
    <input type="hidden" name="id_pagamento" value="<?= $pagamento['id_pagamento'] ?>">
    
    <label for="id_agendamento">ID do Agendamento:</label>
    <input type="number" id="id_agendamento" name="id_agendamento" value="<?= $pagamento['id_agendamento'] ?>" disabled><br><br>

    <label for="valor_consulta">Valor Total da Consulta (R$):</label>
    <input type="number" step="0.01" id="valor_consulta" name="valor_consulta" value="<?= $pagamento['valor_consulta'] ?>" required><br><br>

    <label for="sinal_consulta">Valor do Sinal/Adiantamento (R$):</label>
    <input type="number" step="0.01" id="sinal_consulta" name="sinal_consulta" value="<?= $pagamento['sinal_consulta'] ?>" required><br><br>
    
    <label for="tipo_pagamento">Tipo de Pagamento:</label>
    <select id="tipo_pagamento" name="tipo_pagamento" required>
        <?php $tipos = ['pix', 'credito', 'debito', 'dinheiro']; ?>
        <?php foreach($tipos as $tipo): ?>
            <option value="<?= $tipo ?>" <?= $pagamento['tipo_pagamento'] === $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
        <?php endforeach; ?>
    </select><br><br>
    
    <button type="submit">Atualizar Pagamento</button>
</form>