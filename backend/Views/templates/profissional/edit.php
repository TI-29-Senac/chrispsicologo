<form action="/backend/profissionais/atualizar/<?= htmlspecialchars($usuario->id_profissional) ?>" method="POST">
    
    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario->id_usuario) ?>">
    
    <label>Nome:</label>
    <input type="text" name="nome_usuario" value="<?= htmlspecialchars($usuario->nome_usuario) ?>" required>

    <label>Email:</label>
    <input type="email" name="email_usuario" value="<?= htmlspecialchars($usuario->email_usuario) ?>" required>

    <label>Especialidade:</label>
    <input type="text" name="especialidade" value="<?= htmlspecialchars($usuario->especialidade) ?>" required>

    <label>Senha:</label>
    <input type="password" name="senha_usuario" placeholder="Deixe em branco para não alterar">

    <label>Tipo:</label>
    <select name="tipo_usuario" required>
        <option value="admin" <?= $usuario->tipo_usuario === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="profissional" <?= $usuario->tipo_usuario === 'profissional' ? 'selected' : '' ?>>Profissional</option>
        <option value="cliente" <?= $usuario->tipo_usuario === 'cliente' ? 'selected' : '' ?>>Cliente</option>
    </select>

    <button type="submit">Atualizar</button>
</form>