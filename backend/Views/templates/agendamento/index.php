<?php foreach($usuarios as $usuario): ?>
    <p><?= $usuario->id_usuario ?></p>
    <p><?= $usuario->nome_usuario ?></p>
    <p><?= $usuario->email_usuario ?></p>
    <p><?= $usuario->tipo_usuario ?></p>
    <p><?= $usuario->status_usuario ?></p>
    <?php endforeach; ?>