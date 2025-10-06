<form action="/backend/usuario/atualizar" method="POST">
    <input type="hidden" name="id_usuario" value="<?= $usuario->id_usuario ?>">
    
    <label>Nome:</label>
    <input type="text" name="nome_usuario" value="<?= htmlspecialchars($usuario->nome_usuario) ?>" required>

    <label>Email:</label>
    <input type="email" name="email_usuario" value="<?= htmlspecialchars($usuario->email_usuario) ?>" required>

    <label>Senha:</label>
    <input type="password" name="senha_usuario" placeholder="Digite nova senha se quiser alterar">

    <label>Tipo:</label>
    <select name="tipo_usuario" required>
        <option value="admin" <?= $usuario->tipo_usuario === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="user" <?= $usuario->tipo_usuario === 'user' ? 'selected' : '' ?>>User</option>
    </select>

    <button type="submit">Atualizar</button>
</form>
