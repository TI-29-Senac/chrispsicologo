<div class="w3-container"> 
    <h4>Lista de Usuários</h4>
    <a href="/backend/usuario/criar" class="w3-button" style="background-color: #5D6D68 !important;">Adicionar Usuário</a>
    <br><br>
    
    <table class="w3-table-all w3-hoverable">
        <thead>
            <tr class="w3-light-grey">
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= htmlspecialchars($usuario->id_usuario); ?></td>
                <td><?= htmlspecialchars($usuario->nome_usuario); ?></td>
                <td><?= htmlspecialchars($usuario->email_usuario); ?></td>
                <td><?= htmlspecialchars(ucfirst($usuario->tipo_usuario)); ?></td>
                <td><span class="w3-tag <?= $usuario->status_usuario === 'ativo' ? 'w3-green' : 'w3-red'; ?>"><?= ucfirst($usuario->status_usuario); ?></span></td>
                <td>
                    <a href="/backend/usuario/editar?id=<?= $usuario->id_usuario; ?>" class="w3-button w3-small" style="background-color: #A3B8A1 !important;">Editar</a>
                    <a href="/backend/usuario/excluir?id=<?= $usuario->id_usuario; ?>" class="w3-button w3-red w3-small">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
</div>