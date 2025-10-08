<div>Criar Novo Pagamento</div>
<form action="/backend/pagamentos/salvar" method="POST">
    <label for="id_agendamento">ID do Agendamento:</label>
    <input type="number" id="id_agendamento" name="id_agendamento" required><br><br>

    <label for="valor_consulta">Valor Total da Consulta (R$):</label>
    <input type="number" step="0.01" id="valor_consulta" name="valor_consulta" required><br><br>

    <label for="sinal_consulta">Valor do Sinal/Adiantamento (R$):</label>
    <input type="number" step="0.01" id="sinal_consulta" name="sinal_consulta" required><br><br>
    
    <label for="tipo_pagamento">Tipo de Pagamento:</label>
    <select id="tipo_pagamento" name="tipo_pagamento" required>
        <option value="pix">Pix</option>
        <option value="credito">Cartão de Crédito</option>
        <option value="debito">Cartão de Débito</option>
        <option value="dinheiro">Dinheiro</option>
    </select><br><br>
    
    <button type="submit" value="Salvar">Salvar Pagamento</button>
</form>