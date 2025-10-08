<style>
    /* Seu CSS aqui, ele está correto. */
    table { width: 100%; border-collapse: collapse; margin-top: 20px; font-family: Arial, sans-serif; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
    th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
    th { background-color: #2196F3; color: white; font-size: 16px; }
    tr:nth-child(even) { background-color: #f5f5f5; }
    tr:hover { background-color: #e3f2fd; }
    a { color: #007bff; text-decoration: none; margin-right: 10px; }
    a:hover { text-decoration: underline; }
    p.no-data { font-family: Arial, sans-serif; font-size: 16px; color: #555; margin-top: 20px; }
</style>

<h2>Lista de Usuários</h2>

<?php if (!empty($usuarios)): ?>
<table>
    <thead>
        <tr>
            <th>ID Usuário</th>
            <th>ID Profissional</th> <th>Nome</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($usuarios as $usuario): ?>
        <tr>
            <td><?= htmlspecialchars($usuario->id_usuario) ?></td>

            <td><?= htmlspecialchars($usuario->id_profissional ?? 'N/A') ?></td>

            <td><?= htmlspecialchars($usuario->nome_usuario) ?></td>
            <td><?= htmlspecialchars($usuario->email_usuario) ?></td>
            <td><?= htmlspecialchars($usuario->tipo_usuario) ?></td>
            <td><?= htmlspecialchars($usuario->status_usuario) ?></td>
            <td>
                <a href="/backend/usuario/editar/<?= $usuario->id_usuario ?>">Editar</a>
                <a href="/backend/usuario/deletar/<?= $usuario->id_usuario ?>">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p class="no-data">Nenhum usuário encontrado.</p>
<?php endif; ?>