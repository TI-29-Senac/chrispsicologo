<?php 
// chrispsicologo/backend/Views/templates/pagamento/excluir_manual.php
?>
<div>Excluir Pagamento por ID</div>

<p>Insira o ID do pagamento que deseja excluir.</p>

<form action="/backend/pagamentos/deletar" method="POST">
    <label for="id_pagamento_manual">ID do Pagamento a Excluir:</label>
    <input type="number" id="id_pagamento_manual" name="id_pagamento_manual" required min="1"><br><br>
    
    <button type="submit" style="background-color: #dc3545; color: white;">Excluir Pagamento</button>
</form>

<p style="margin-top: 20px;">Você pode ver os IDs disponíveis na seção <a href="/backend/pagamentos/listar">Pagamentos/Listar</a>.</p>

<form action="/backend/pagamentos/deletar" method="POST">
    <input type="hidden" name="id_pagamento" value="<?= $pagamento['id_pagamento'] ?>">
    <button type="submit">Confirmar Exclusão</button>
</form>