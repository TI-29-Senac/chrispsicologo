<?php 
// ti-29-senac/chrispsicologo/chrispsicologo-backend-correto2/backend/Views/templates/usuario/delete.php
?>

<div class="w3-main" style="margin-left:300px;margin-top:43px;">
  <div class="w3-container w3-padding-32">
    <h2 style="color: #5D6D68;">🗑️ Excluir Usuário</h2>

    <div class="w3-card-4 w3-white" style="border-radius: 8px; padding: 20px;">
      
      <p>Você tem certeza que deseja excluir permanentemente o usuário abaixo? Esta ação não pode ser desfeita.</p>

      <div class="w3-panel w3-light-grey w3-round-large w3-padding">
        <p><strong>ID:</strong> <?= htmlspecialchars($usuario->id_usuario) ?></p>
        <p><strong>Nome:</strong> <?= htmlspecialchars($usuario->nome_usuario) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($usuario->email_usuario) ?></p>
      </div>

      <form action="/backend/usuario/deletar/<?= htmlspecialchars($usuario->id_usuario) ?>" method="POST">
          <p class="w3-margin-top">
              <button type="submit" class="w3-button w3-round w3-red">Confirmar Exclusão</button>
              <a href="/backend/usuario/listar" class="w3-button w3-round w3-light-grey">Cancelar</a>
          </p>
      </form>

    </div>
  </div>
</div>