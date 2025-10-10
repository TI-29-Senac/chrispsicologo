<div class="w3-container"> 
    <div class="w3-row w3-margin-bottom">
        <div class="w3-col m8 l9">
            <h2 style="color: #5D6D68;">👤 Lista de Usuários</h2>
        </div>
        <div class="w3-col m4 l3 w3-right-align">
            <a href="/backend/usuario/criar" class="w3-button" style="background-color: #5D6D68 !important; color: white; border-radius: 8px;">+ Adicionar Usuário</a>
        </div>
    </div>
    
    <div class="w3-responsive">
        <table class="w3-table-all w3-hoverable w3-card-4">
            <thead style="background-color: #5D6D68; color: white;">
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
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario->id_usuario); ?></td>
                        <td><?= htmlspecialchars($usuario->nome_usuario); ?></td>
                        <td><?= htmlspecialchars($usuario->email_usuario); ?></td>
                        <td><?= htmlspecialchars(ucfirst($usuario->tipo_usuario)); ?></td>
                        <td><span class="w3-tag <?= $usuario->status_usuario === 'ativo' ? 'w3-green' : 'w3-red'; ?>"><?= ucfirst($usuario->status_usuario); ?></span></td>
                        <td>
                            <a href="/backend/usuario/editar/<?= $usuario->id_usuario; ?>" class="w3-button w3-tiny" style="background-color: #A3B8A1 !important; border-radius: 8px;">Editar</a>
                            <a href="/backend/usuario/excluir/<?= $usuario->id_usuario; ?>" class="w3-button w3-red w3-tiny" style="border-radius: 8px;">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="w3-center">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br>
    
    <?php if (isset($paginacao) && $paginacao['ultima_pagina'] > 1): ?>
    <div class="w3-center">
        <div class="w3-bar">
            <?php if ($paginacao['pagina_atual'] > 1): ?>
                <a href="?pagina=<?= $paginacao['pagina_atual'] - 1 ?>" class="w3-button">&laquo;</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $paginacao['ultima_pagina']; $i++): ?>
                <a href="?pagina=<?= $i ?>" class="w3-button <?= ($i == $paginacao['pagina_atual']) ? 'w3-green' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($paginacao['pagina_atual'] < $paginacao['ultima_pagina']): ?>
                <a href="?pagina=<?= $paginacao['pagina_atual'] + 1 ?>" class="w3-button">&raquo;</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>