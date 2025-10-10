<?php 
use App\Psico\Core\Flash;

$old_input = Flash::get('old_input') ?? [];
$errors = Flash::get('validation_errors') ?? [];

$nome_usuario = htmlspecialchars($old_input['nome_usuario'] ?? $usuario->nome_usuario ?? '');
$email_usuario = htmlspecialchars($old_input['email_usuario'] ?? $usuario->email_usuario ?? '');
$current_tipo = $old_input['tipo_usuario'] ?? $usuario->tipo_usuario ?? 'user';
?>

<form action="/backend/usuario/atualizar/<?= htmlspecialchars($usuario->id_usuario) ?>" method="POST">
    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario->id_usuario) ?>">



    <label>Nome:</label>
    <input type="text" 
           name="nome_usuario" 
           value="<?= $nome_usuario ?>" 
           required
           class="<?= isset($errors['nome_usuario']) ? 'is-invalid' : '' ?>">
    <?php if (isset($errors['nome_usuario'])): ?>
        <div class="error-message"><?= htmlspecialchars($errors['nome_usuario'][0]) ?></div>
    <?php endif; ?>

    <label>Email:</label>
    <input type="email" 
           name="email_usuario" 
           value="<?= $email_usuario ?>" 
           required
           class="<?= isset($errors['email_usuario']) ? 'is-invalid' : '' ?>">
    <?php if (isset($errors['email_usuario'])): ?>
        <div class="error-message"><?= htmlspecialchars($errors['email_usuario'][0]) ?></div>
    <?php endif; ?>

    <label>Senha:</label>
    <input type="password" 
           name="senha_usuario" 
           placeholder="Digite nova senha se quiser alterar"
           class="<?= isset($errors['senha_usuario']) ? 'is-invalid' : '' ?>">
    <?php if (isset($errors['senha_usuario'])): ?>
        <div class="error-message"><?= htmlspecialchars($errors['senha_usuario'][0]) ?></div>
    <?php endif; ?>

    <label>Tipo:</label>
    <select name="tipo_usuario" required>
        <option value="admin" <?= $current_tipo === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="user" <?= $current_tipo === 'profissional' ? 'selected' : '' ?>>Profissional</option>
    </select>

    <button type="submit">Atualizar</button>
</form>