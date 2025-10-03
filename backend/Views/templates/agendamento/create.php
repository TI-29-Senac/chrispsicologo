<div>Sou o Create</div>
<form action="/backend/usuario/salvar" method="POST">
    <label for="nome_usuario">Nome:</label>
    <input type="text" id="nome_usuario" name="nome_usuario" required><br>
    <br>
    <label for="email_usuario">Email:</label>
    <input type="email" id="email_usuario" name="email_usuario" required><br>
    <br>
    <label for="senha_usuario">Senha:</label>
    <input type="password" id="senha_usuario" name="senha_usuario" required><br>
    <br>
    <label for="tipo_usuario">Tipo de Usuário:</label>
    <select id="tipo_usuario" name="tipo_usuario" required>
        <option value="admin">Admin</option>
        <option value="user">User</option>
        <option value="user">Recepcionista</option>
        <option value="user">Profissional</option>
    </select>
    <br>
    <button type="submit" value="Salvar">Salvar</button>
</form>