<?php 
// Verifica se a variável $avaliacoes existe e não está vazia para evitar erros.
if (!empty($avaliacoes)): 
?>
    <table>
        <thead>
            <tr>
                <th>ID Avaliação</th>
                <th>ID Cliente</th>
                <th>ID Profissional</th>
                <th>Nota</th>
                <th>Comentário</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($avaliacoes as $avaliacao): ?>
            <tr>
                <td><?= $avaliacao['id_avaliacao'] ?></td>
                <td><?= $avaliacao['id_cliente'] ?></td>
                <td><?= $avaliacao['id_profissional'] ?></td>
                <td><?= $avaliacao['nota_avaliacao'] ?>/5</td>
                <td><?= htmlspecialchars($avaliacao['descricao_avaliacao']) ?></td>
                <td>
                    <a href="/backend/avaliacoes/editar?id=<?= $avaliacao['id_avaliacao'] ?>">Editar</a> |
                    <a href="/backend/avaliacoes/excluir?id=<?= $avaliacao['id_avaliacao'] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nenhuma avaliação encontrada.</p>
<?php endif; ?>