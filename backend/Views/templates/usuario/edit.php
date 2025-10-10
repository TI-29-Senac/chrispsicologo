<?php 
// ti-29-senac/chrispsicologo/chrispsicologo-backend-correto2/backend/Views/templates/usuario/edit.php

use App\Psico\Core\Flash;

// Recupera dados antigos ou do banco de dados para preencher o formulário
$old_input = Flash::get('old_input') ?? [];
$errors = Flash::get('validation_errors') ?? [];

$nome_usuario = htmlspecialchars($old_input['nome_usuario'] ?? $usuario->nome_usuario ?? '');
$email_usuario = htmlspecialchars($old_input['email_usuario'] ?? $usuario->email_usuario ?? '');
$current_tipo = $old_input['tipo_usuario'] ?? $usuario->tipo_usuario ?? 'user';
?>

<div class="w3-main" style="margin-left:300px;margin-top:43px;">
  <div class="w3-container w3-padding-32">
    <h2 style="color: #5D6D68;">📝 Editando Usuário: <?= $nome_usuario ?></h2>

    <div class="w3-card-4 w3-white" style="border-radius: 8px; padding: 20px;">
      
      <form action="/backend/usuario/atualizar/<?= htmlspecialchars($usuario->id_usuario) ?>" method="POST">
          <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario->id_usuario) ?>">

          <p>
              <label class="w3-text-grey">Nome:</label>
              <input class="w3-input w3-border <?= isset($errors['nome_usuario']) ? 'w3-border-red' : '' ?>" 
                     type="text" 
                     name="nome_usuario" 
                     value="<?= $nome_usuario ?>" 
                     required>
              <?php if (isset($errors['nome_usuario'])): ?>
                  <span class="w3-text-red"><?= htmlspecialchars($errors['nome_usuario']) ?></span>
              <?php endif; ?>
          </p>

          <p>
              <label class="w3-text-grey">Email:</label>
              <input class="w3-input w3-border <?= isset($errors['email_usuario']) ? 'w3-border-red' : '' ?>" 
                     type="email" 
                     name="email_usuario" 
                     value="<?= $email_usuario ?>" 
                     required>
              <?php if (isset($errors['email_usuario'])): ?>
                  <span class="w3-text-red"><?= htmlspecialchars($errors['email_usuario']) ?></span>
              <?php endif; ?>
          </p>

          <p>
              <label class="w3-text-grey">Nova Senha:</label>
              <input class="w3-input w3-border <?= isset($errors['senha_usuario']) ? 'w3-border-red' : '' ?>" 
                     type="password" 
                     name="senha_usuario" 
                     placeholder="Deixe em branco para não alterar">
              <?php if (isset($errors['senha_usuario'])): ?>
                  <span class="w3-text-red"><?= htmlspecialchars($errors['senha_usuario']) ?></span>
              <?php endif; ?>
          </p>

          <p>
              <label class="w3-text-grey">Tipo:</label>
              <select class="w3-select w3-border" name="tipo_usuario" required>
                  <option value="admin" <?= $current_tipo === 'admin' ? 'selected' : '' ?>>Admin</option>
                  <option value="profissional" <?= $current_tipo === 'profissional' ? 'selected' : '' ?>>Profissional</option>
                  <option value="cliente" <?= $current_tipo === 'cliente' ? 'selected' : '' ?>>Cliente</option>
              </select>
          </p>
          
          <p class="w3-margin-top">
              <button type="submit" class="w3-button w3-round" style="background-color: #5D6D68; color: white;">Atualizar Usuário</button>
              <a href="/backend/usuario/listar" class="w3-button w3-round w3-light-grey">Cancelar</a>
          </p>
      </form>

    </div>
  </div>
</div>