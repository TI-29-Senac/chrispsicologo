<div>Excluir Usuário</div>
<p>Tem certeza que deseja excluir o usuário abaixo?</p>

<ul>
    <li><strong>ID:</strong> <?= htmlspecialchars($usuario->id_usuario) ?></li>
    <li><strong>Nome:</strong> <?= htmlspecialchars($usuario->nome_usuario) ?></li>
    <li><strong>Email:</strong> <?= htmlspecialchars($usuario->email_usuario) ?></li>
    <li><strong>Tipo:</strong> <?= htmlspecialchars($usuario->tipo_usuario) ?></li>
</ul>

<form action="/backend/usuario/deletar/<?= htmlspecialchars($usuario->id_usuario) ?>" method="POST">
    <button type="submit" class="w3-button w3-red w3-round">Confirmar Exclusão</button>
    <a href="/backend/usuario/listar" class="w3-button w3-grey w3-round">Cancelar</a>
</form>