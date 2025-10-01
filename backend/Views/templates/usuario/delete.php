<div>Excluir Usuário</div>
<p>Tem certeza que deseja excluir o usuário abaixo?</p>

<ul>
    <li><strong>ID:</strong> <?= $usuario->id_usuario ?></li>
    <li><strong>Nome:</strong> <?= $usuario->nome_usuario ?></li>
    <li><strong>Email:</strong> <?= $usuario->email_usuario ?></li>
    <li><strong>Tipo:</strong> <?= $usuario->tipo_usuario ?></li>
</ul>

<form action="/backend/usuario/deletar" method="POST">
    <!-- Passando o ID escondido -->
    <input type="hidden" name="id_usuario" value="<?= $usuario->id_usuario ?>">
    <button type="submit">Confirmar Exclusão</button>
</form>
